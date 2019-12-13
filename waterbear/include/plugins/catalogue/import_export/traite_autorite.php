<?php

/**
 * plugin_catalogue_import_export_traite_autorite()
 * 
 * Ce plugin va gérer les champs liés à autorité quand on importe une autorité (par exemple le champ 700 d'une notice bibliographique)
 * Il va créer la notice autorité (ou la dédoublonner si elle existe déjà) à partir des infos contenues dans le champ
 * puis il va mettre à jour le champ lui-même à partir de la notice autorité créée
 * 
 * Il reçoit en paramètre une notice (tvs_marcxml) et le champ à traiter (sous forme de DomNode qui doit provenir du même DOM)
 * Différents plugins vont gérer les différentes étapes du processus.
 * 
 * 1) On génère la notice autorité à partir des infos contenues dans le champ (plugin_formate_champ). La notice est générée sous forme de chaine de caratère avec le
 *    format suivant : 200:a:toto|b:tutu|$$210:a:popo|b:pupu|$$
 * 
 * 2) A partir de cette chaine de caratères on va créer la définition (Array) de la notice autorité, puis on va générer cete notice en XML (plugin_crea_objet)
 * 
 * 3) On importe cette notice autorité (plugin_importe_objet). C'est ce plugin qui sera chargé de dédoublonner la notice autorité. Nous on récupère la notice une fois importée 
 *    (qui peut être celle qu'on a généré ou bien celle présente dans la base ou une fusion des 2)
 * 
 * 4) Enfin on maj le champ de la notice elle-même. C'est un champ de type lien explicite. Donc on récupère d'abord les infos sur le lien explicite (plugin_param_lien explicite)
 *    Entre autres choses, ça fournit le plugin qui va servir à formater la notice autorité pour générer le champ de lien explicite (c'est en fait l'opération inverse qu'en 1))
 *    Puis on maj la notice (et le champ) : plugin_maj_lien_explicite
 * 
 * Le plugin ne retourne rien, car la notice est directement maj (par référence). ATTENTION le plugin n'enregistre pas la notice dans la DB. C'est à la charge
 * du plugin appelant
 *  
 * @param mixed $parametres
 * @param [type_obj] -> type d'objet de la notice autorité liée
 * @param [tvs_marcxml] -> notice en tvs_marcxml
 * @param [champ] -> champ de lien à traiter sous forme de DomNode (appartenant au même DOM que la notice)
 * @param [plugin_formate_champ] -> plugin qui va générer une notice autorité (sous forme de string) à partir du champ de lien
 * @param [plugin_crea_objet] -> plugin qui va générer la notice autorité en XML
 * @param [plugin_importe_objet] -> plugin qui va importer la notice autorité dans la base (et le cas échéant la dédoublonner)
 * @param [plugin_param_lien_explicite] -> Infos sur le lien explicite (du champ lié à l'autorité)
 * @param [plugin_rajoute_ID_notice] => plugin de type modif_marcxml pour rajouter l'ID de la notice autorité nouvellement crée en 000$a
 * !!!! deprecated !!!! => info récupérée via plugin_param_lien_explicite @param [plugin_formate_autorite] => plugin qui va générer le champ de lien (sous forme de string) à partir de la notice autorité (opération inverse de plugin_formate_champ mais entre temps le champ devra être changé, ne serait-ce que pour inclure les $3, $9a...)
 * @param [plugin_maj_lien_explicite] -> plugin qui va mettre à jour le champ de lien une fois la notice autorité importée
 * @param [nom_champ] -> nom du champ de lien
 * @param [import_options] => un tableau contenant divers options qui peuvent être saisies dans le formulaire d'import (ex. bib pour rec 995)
 *                           Ces options sont passées aux différents plugins qui pourront les intégrer
 
 * 
 * @return void
 * 
 */
function plugin_catalogue_import_export_traite_autorite($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $type_obj=$parametres["type_obj"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $champ=$parametres["champ"];
    
    $plugin_formate_champ=$parametres["plugin_formate_champ"];
    $plugin_crea_objet=$parametres["plugin_crea_objet"];
    $plugin_importe_objet=$parametres["plugin_importe_objet"];
    $plugin_param_lien_explicite=$parametres["plugin_param_lien_explicite"];
    $plugin_maj_lien_explicite=$parametres["plugin_maj_lien_explicite"];
    //$plugin_formate_autorite=$parametres["plugin_formate_autorite"];
    $plugin_rajoute_ID_notice=$parametres["plugin_rajoute_ID_notice"];
    $nom_champ=$parametres["nom_champ"];
    $import_options=$parametres["import_options"];
    
    // 1) On récupère la notice autorité sous forme de string de type
    // 200:a:toto|b:tutu$$210:c:popo|d:pupu|$$
    $tmp=applique_plugin ($plugin_formate_champ, array("champ"=>$champ, "tvs_marcxml" => $tvs_marcxml, "import_options"=>$import_options));
    if ($tmp["succes"]!=1) {
        return ($tmp);
    }
    $def_str=$tmp["resultat"]["texte"];
    
    // 2) On crée la définition (array) de la notice autorité
    $def_array=array();
    $def_array["definition"]=array();
    $tmp_liste_champs=explode("$$", $def_str);
    foreach ($tmp_liste_champs as $tmp_champ) { // pour chaque champ
        $tmp_elem_champ=explode(":", $tmp_champ, 2);
        if (count($tmp_elem_champ)==2) {
            $def_champ_array=array();
            $tag=$tmp_elem_champ[0];
            $def_champ_array["tag"]=$tag;
            $def_champ_array["definition"]=array();
            $ss_champs_str=$tmp_elem_champ[1];
            $tmp_liste_ss_champs=explode("|", $ss_champs_str);
            foreach ($tmp_liste_ss_champs as $tmp_ss_champ) { // pour chaque ss_champ
                $tmp_elem_ss_champ=explode(":", $tmp_ss_champ, 2);
                if (count($tmp_elem_ss_champ)==2) {
                     $code=$tmp_elem_ss_champ[0];
                     $valeur=$tmp_elem_ss_champ[1];
                     array_push($def_champ_array["definition"], array("code"=>$code, "valeur"=>$valeur));
                }
            } // fin de pour chaque ss_champ...
            array_push ($def_array["definition"], $def_champ_array);
        }
    } // fin de pour chaque champ
    
    // 3) On génère la notice autorité XML
    $tmp=applique_plugin($plugin_crea_objet, $def_array);
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    $notice_autorite=$tmp["resultat"]["notice"];
    
    // 4) On importe cette notice autorité
    $tmp=applique_plugin($plugin_importe_objet, array("notice"=>$notice_autorite, "import_options"=>$import_options));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    if ($tmp["resultat"]["bool_erreur"]==1) {
        return($retour);
    }
    $tvs_marcxml_autorite=$tmp["resultat"]["tvs_marcxml"];
    $ID_notice_autorite=$tmp["resultat"]["ID_notice"]; 
    
    // 5) on récupère les infos sur le lien explicite
    $tmp=applique_plugin($plugin_param_lien_explicite, array());
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    $infos_lien_explicite=$tmp["resultat"];
    $ss_champs_a_conserver=$infos_lien_explicite["ss_champs_a_conserver"];
    $plugin_formate_autorite=$infos_lien_explicite["plugin_formate"];
    
    // 5bis) On rajoute ID_notice dans le champ 000$a
    $tmp=applique_plugin($plugin_rajoute_ID_notice, array("tvs_marcxml"=>$tvs_marcxml_autorite, "ID_notice"=>$ID_notice_autorite));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    
    // 6) on génère la chaine de caratères qui va servir à maj le lien explicite
    $tmp=applique_plugin($plugin_formate_autorite, array("tvs_marcxml"=>$tvs_marcxml_autorite));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    $str_lien_explicite=$tmp["resultat"]["texte"];
    
    // 7) on maj la notice de base (lien explicite)
    $tmp=applique_plugin($plugin_maj_lien_explicite, array("tvs_marcxml"=>$tvs_marcxml, "champ"=>$champ, "champ_remplace"=>$str_lien_explicite, "ss_champs_a_conserver"=>$ss_champs_a_conserver, "nom_champ"=>$nom_champ));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    } 
 
    
    return ($retour);
}


?>