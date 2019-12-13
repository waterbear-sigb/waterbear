<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_fin_element ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $plugin_wizard=$parametres["plugin_wizard"];
    $element=$parametres["element"];
    $tag=$element["tag"];
    $code=$element["code"];
    
    $liste_champs=$formulator->get_champs_by_nom($tag);
    foreach ($liste_champs as $champ) {
        if ($code != "") {
            $liste_ss_champs=$formulator->get_ss_champs_by_nom($champ["id"], $code);
            foreach ($liste_ss_champs as $ss_champ) {
                $ID_element=$ss_champ["id"];
                $tmp=applique_plugin ($plugin_wizard, array("infos"=>$ss_champ, "ID_element"=>$ID_element, "ID_operation"=>$ID_operation));
                return ($tmp);
            }
        } else { // action au niveau du champ
            $ID_element=$champ["id"];
            $tmp=applique_plugin ($plugin_wizard, array("infos"=>$champ, "ID_element"=>$ID_element, "ID_operation"=>$ID_operation));
            return ($tmp);
        }
    }
    
    return ($retour);
    
}


?>