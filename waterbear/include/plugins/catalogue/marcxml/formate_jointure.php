<?php

/**
 * plugin_catalogue_marcxml_formate_jointure()
 * 
 * Ce plugin permet d'afficher dans une notice, des infos issues d'une autre notice liée (lien implicite ou explicite)
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_marcxml_formate_jointure ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // utilisé pour explicite / implicite
    $type_obj=$parametres["type_obj"];
    $ID_notice=$parametres["ID_notice"];
    $plugin_formate=$parametres["plugin_formate"];
    $defaut=$parametres["defaut"];
    
    // utilisé pour implicite uniquement
    $sens_lien=$parametres["sens_lien"];
    $type_lien=$parametres["type_lien"];
    $type_obj_origine=$parametres["type_obj_origine"];
    $avant=$parametres["avant"];
    $apres=$parametres["apres"];
    $avant_verif=$parametres["avant_verif"];
    
    if ($sens_lien == "") {
        $sens_lien = "explicite";
    }
    
    if ($sens_lien == "explicite") {
        // On récupère la notice liée
        $notice=get_objet_xml_by_id($type_obj, $ID_notice);
        // on applique le plugin avec la notice en paramètre
        $tmp=applique_plugin ($plugin_formate, array("notice"=>$notice));
        if ($tmp["resultat"]["texte"]=="" AND $defaut != "") {
            $tmp["resultat"]["texte"]=$defaut;
        }
        
        return ($tmp);    
    } else {
        $liste=get_objets_lies($type_obj, $type_lien, $ID_notice, $type_obj_origine);
        $texte_retour="";
        foreach ($liste as $element) {
            $ID=$element["ID"];
            $notice=get_objet_xml_by_id($type_obj, $ID);
            $tmp=applique_plugin ($plugin_formate, array("notice"=>$notice));
            $texte=$tmp["resultat"]["texte"];
            if ($texte != "") {
                if ($texte_retour != "" AND $avant_verif != "") {
                    $texte_retour.=$avant_verif;
                }
                if ($avant != "") {
                    $texte_retour.=$avant;
                }
                $texte_retour.=$texte;
                $texte_retour.=$apres;
            }
            
        } // fin du pour chaque élément
        if ($texte_retour=="" AND $defaut != "") {
            $texte_retour=$defaut;
        }
        $retour["resultat"]["texte"]=$texte_retour;
        return ($retour);
        
    }

}

?>