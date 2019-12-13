<?php

/**
 * plugin_catalogue_catalogage_grilles_add_ID_grille_modification()
 * 
 * @param array $parametres
 * @param [plugin_definition_grille] : le plugin qui va nous donner la définition de la grille (quels champs vont dans quels onglets, le type des champs, les icones, les événements...)
 * @param [type_objet] : type de l'objet
 * @param SOIT [ID_notice] : ID de la notice à modifier
 * @param SOIT [notice] : notice DOMXml
 * @return array
 */
function plugin_catalogue_catalogage_grilles_add_ID_grille_modification ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $table_champs=array(); // table permettant de retouver facilement l'idx de l'onglet d'un champ ainsi que les infos des champs et ss-champs

    // 1) On récupère les infos sur la grille
    $tmp=applique_plugin($parametres["plugin_definition_grille"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $infos_grille=$tmp["resultat"];
    $retour["resultat"]=$infos_grille; // on retournera 'en gros' la même grille, mais avec des champs et sous-champs différents
    
    // 2) On génère  une "table" des champs qui permettra de savoir facilement dans quel onglet se trouve chaque champ et de connaitre les modeles
    foreach ($infos_grille["onglets"] as $clef_onglet => $onglet) {
        if (is_array($onglet["champs"])) {
            foreach ($onglet["champs"] as $champ) {
                $nom_champ=$champ["nom"];
                $table_champs[$nom_champ]=array();
                $table_champs[$nom_champ]["clef_onglet"]=$clef_onglet;
                $table_champs[$nom_champ]["modele"]=$champ;
                $retour["resultat"]["onglets"][$clef_onglet]["champs"]=array(); // on RAZ les champs qu'on va retourner pour chaque onglet
                foreach ($champ["ss_champs"] as $ss_champ) {
                    $nom_ss_champ=$ss_champ["nom"];
                    $table_champs[$nom_champ."_".$nom_ss_champ]=array();
                    $table_champs[$nom_champ."_".$nom_ss_champ]["modele"]=$ss_champ;
                }
            }
        }
    }
    
    // 3) On récupère la notice
    if ($parametres["notice"] == "") {
        $notice_xml=get_objet_xml_by_id($parametres["type_objet"], $parametres["ID_notice"]);
    } else {
        $notice_xml=$parametres["notice"];
    }


    if ($notice_xml=="") {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("catalogue/marcxml/db/crea_notice", "contenu_vide");
        return ($retour);
    }
    
    // 4) Pour chaque champ de la notice
    $liste_champs=$notice_xml->getElementsByTagName("datafield");
    for ($i=0 ; $i < $liste_champs->length ; $i++) {
        $champ=$liste_champs->item($i);
        $nom_champ=$champ->attributes->getNamedItem("tag")->value;
        //$liste_ss_champs=$champ->chidNodes;
        $liste_ss_champs=$champ->getElementsByTagName("subfield");
        $clef_onglet=$table_champs[$nom_champ]["clef_onglet"];
        if (! isset ($table_champs[$nom_champ])) {
            continue;
        }
        $tmp_array=$table_champs[$nom_champ]["modele"];
        $tmp_array["ss_champs"]=array(); // on RAZ les ss champs qu'on va renvoyer
        for ($j=0 ; $j < $liste_ss_champs->length ; $j++) { // pour chaque ss-champ
            $ss_champ=$liste_ss_champs->item($j);
            $nom_ss_champ=$ss_champ->attributes->getNamedItem("code")->value;
            $valeur_ss_champ=$ss_champ->textContent;
            if (! isset ($table_champs[$nom_champ."_".$nom_ss_champ])) {
                continue;
            }
            $tmp_array_ss_champ=$table_champs[$nom_champ."_".$nom_ss_champ]["modele"];
            $tmp_array_ss_champ["valeur"]=$valeur_ss_champ;
            if (isset($tmp_array_ss_champ["force_valeur"])) { // on force la valeur dans le registre. Typiquement pour mettre une date de maj
                $tmp_array_ss_champ["valeur"]=$tmp_array_ss_champ["force_valeur"];
            }
            array_push($tmp_array["ss_champs"], $tmp_array_ss_champ);
        }
        array_push ($retour["resultat"]["onglets"][$clef_onglet]["champs"], $tmp_array);
    }
    
    // On relance une numérotation
    $tmp=applique_plugin($parametres["plugin_definition_grille"], $retour["resultat"]);
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    
    $retour["resultat"]=$tmp["resultat"];
    return ($retour);
    
    //$toto=var_export($retour, true);
    //tvs_log("dbg", "add_ID_grille_modification", array($toto));
    
    
    
    
} // fin du plugin


?>