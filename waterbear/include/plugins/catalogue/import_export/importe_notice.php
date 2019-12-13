<?php

/**
 * plugin_catalogue_import_export_importe_notice()
 * 
 * Ce plugin va importer une notice fournie en XML ou tvs_marcxml
 * Elle effectue un certain nombre d'opérations :
 * 1) Nettoyage de la notice (suppression/renommage de champs/ss-champs)
 * 2) dédoublonnage
 * 3) si doublon trouvé, fusion des 2 notices en appliquant une des politiques possibles
 * 3 bis) on vérifie la validité de la notice (champs / ss-champs obligatoires...)
 * 4) Analyse de la notice champ par champ pour appliquer des plugins sur les champs/ss-champs. Permet en particulier de générer les autorités, ou de modifier des valeur (ex. formattage ISBN)
 * 5) ** option ** modification de la notice (par exemple ajout de champs...)
 * 6) Importation dans la base
 * 
 * Politiques de fusion :
 * garder => on garde l'ancienne notice sans modification (et sans traitements supplémentaires) dans ce cas, on retourne l'ancienne notice en XML et le n° de notice et on arrête les traitements
 * remplacer => on remplace l'ancienne notice par la nouvelle (on garde juste le n° de notice). Les traitements continuent (en particulier au niveau de l'analyse des champs)
 * base_ancienne => les 2 notices sont fusionnées, mais on part de l'ancienne notice (à laquelle on pourra éventuellement rajouter/remplacer certains champs de la nouvelle)
 * base_nouvelle => les 2 notices sont fusionnées mais on part de la nouvelle notice
 * 
 * @param mixed $parametres
 * @param [notice] OU [tvs_marcxml]
 * @param [type_obj]
 * @param [plugin_nettoie_notice] => ce plugin supprime/renomme les champs ss-champs de la notice
 * @param [plugin_ddbl] => plugin de dédoublonnage
 * @param [politique_fusion] => politique à appliquer en cas de doublon : garder | remplacer | base_ancienne | base_nouvelle (cf. ci-dessus)
 * @param [plugin_fusion] => filtre permettant de fusionner les 2 notices si politique == base_ancienne ou base_nouvelle
 * @param [plugin_verifie_notice] => ** option ** vérifie si la notice est bien formée (champs / ss-champs obligatoires / non répétables)
 * @param [plugin_analyse_champs] => ce plugin va analyser les différents champs / ss-champs de la notice et leur appliquer éventuellement des plugins. en particulier sur les champs de lien pour générer les autorités et maj le champ, et pour formater certains ss-champs (ex. ISBN)
 * @param [plugin_maj] => plugin de modif de la notice
 * @param [plugin_notice_2_db] => plugin utilisé pour enregistrer la notice dans la DB
 * @param [import_options] => un tableau contenant divers options qui peuvent être saisies dans le formulaire d'import (ex. bib pour rec 995)
 *                           Ces options sont passées aux différents plugins qui pourront les intégrer
 * 
 * 
 * @return [commentaire]
 *         [ID_notice]
 *         [tvs_marcxml]  
 */
function plugin_catalogue_import_export_importe_notice($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $notice=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $plugin_analyse_champs=$parametres["plugin_analyse_champs"];
    $plugin_nettoie_notice=$parametres["plugin_nettoie_notice"];
    $plugin_ddbl=$parametres["plugin_ddbl"];
    $plugin_fusion=$parametres["plugin_fusion"];
    $plugin_verifie_notice=$parametres["plugin_verifie_notice"];
    $plugin_maj=$parametres["plugin_maj"];
    $politique_fusion=$parametres["politique_fusion"]; // garder | remplacer | base_ancienne | base_nouvelle 
    $type_obj=$parametres["type_obj"];
    $import_options=$parametres["import_options"];
    
    $ID_notice="";
    $commentaire="";
    
    
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    // 1) On nettoie la notice (suppression de champs/ss-champs, renommages des champs/ss-champs)
    if (is_array($plugin_nettoie_notice)) {
        $tmp=applique_plugin($plugin_nettoie_notice, array("tvs_marcxml"=>$tvs_marcxml, "import_options"=>$import_options)); //retourne un tvs_marcxml mais pas besoin de le récupérer, car modifié par référence
        if ($tmp["succes"] != 1) {
            return ($tmp); 
        }
    }
    
    // 2) On dédoublonne
    if (is_array($plugin_ddbl)) {
        $tmp=applique_plugin($plugin_ddbl, array("notice"=>$tvs_marcxml->notice, "import_options"=>$import_options));
        if ($tmp["succes"] != 1) {
            return ($tmp); 
        }
    $notice_ddbl=$tmp["resultat"]["notice"];
    $ID_notice_ddbl=$tmp["resultat"]["ID_notice"];    
    }
    

    
    
    // 3) Si doublon, on fusionne suivant une des politiques de fusion
    if ($ID_notice_ddbl != "") {
        $commentaire.="doublon : $ID_notice_ddbl. ";
        if ($politique_fusion == "remplacer") {
            $commentaire.="Notice existante remplacee. ";
            $ID_notice=$ID_notice_ddbl;
        } elseif ($politique_fusion == "base_ancienne") {
            $commentaire.="Notices fusionnees (sur la base de l'ancienne notice). ";
            $tmp=applique_plugin($plugin_fusion, array("marcxml_b"=>$tvs_marcxml, "xml_a"=>$notice_ddbl, "type_obj"=>$type_obj, "format_retour"=>"marcxml", "import_options"=>$import_options));
            if ($tmp["succes"] != 1) {
                return ($tmp); 
            }
            $tvs_marcxml=$tmp["resultat"]["notice"];
            $ID_notice=$ID_notice_ddbl;
        } elseif ($politique_fusion == "base_nouvelle") {
            $commentaire.="Notices fusionnees (sur la base de la nouvelle notice). ";
            $tmp=applique_plugin($plugin_fusion, array("marcxml_a"=>$tvs_marcxml, "xml_b"=>$notice_ddbl, "type_obj"=>$type_obj, "format_retour"=>"marcxml", "import_options"=>$import_options));
            if ($tmp["succes"] != 1) {
                return ($tmp); 
            }
            $tvs_marcxml=$tmp["resultat"]["notice"];
            $ID_notice=$ID_notice_ddbl;
        } else { // par défaut : garder
            $commentaire.="Ancienne notice conservee. ";
            $tvs_marcxml=new tvs_marcxml(array());
            $tvs_marcxml->load_notice($notice_ddbl);
            $retour["resultat"]["commentaire"]=$commentaire;
            $retour["resultat"]["ID_notice"]=$ID_notice_ddbl;
            $retour["resultat"]["tvs_marcxml"]=$tvs_marcxml;
            return ($retour);
        }

    }
    
    // 3 bis) on teste la validité de la notice
    if (is_array($plugin_verifie_notice)) {
        $tmp=applique_plugin($plugin_verifie_notice, array("tvs_marcxml"=>$tvs_marcxml, "import_options"=>$import_options));
        if ($tmp["resultat"]["bool_erreur"]==1) {
            $retour["resultat"]["bool_erreur"]=1;
            $retour["resultat"]["commentaire"]=$tmp["resultat"]["message"];
            return($retour);
        }
    }
    
    // 4) On fait une analyse champ par champ (en particulier, création des autorités et maj des champs de liens)
    if (is_array($plugin_analyse_champs)) {
        $tmp=applique_plugin($plugin_analyse_champs, array("tvs_marcxml"=>$tvs_marcxml, "import_options"=>$import_options));
        if ($tmp["succes"] != 1) {
            return ($tmp); 
        }
    }
    
    // 5) ** option ** on modifie la notice (par exemple pour ajouter des champs comme un 997 vide...)
    if ($plugin_maj != "") {
        $tmp=applique_plugin($plugin_maj, array("tvs_marcxml"=>$tvs_marcxml, "import_options"=>$import_options));
        if ($tmp["succes"] != 1) {
            return ($tmp); 
        }
    }
    
    // 6) On crée la notice
    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$tvs_marcxml->notice, "ID_notice"=>$ID_notice, "import_options"=>$import_options));
    if ($tmp["succes"] != 1) {
        return ($tmp); 
    }
    $commentaire.="Notice ".$tmp["resultat"]["ID_notice"];
    
    $retour["resultat"]["commentaire"]=$commentaire;
    $retour["resultat"]["ID_notice"]=$tmp["resultat"]["ID_notice"];
    $retour["resultat"]["tvs_marcxml"]=$tvs_marcxml;
    
    
    return ($retour);
}


?>