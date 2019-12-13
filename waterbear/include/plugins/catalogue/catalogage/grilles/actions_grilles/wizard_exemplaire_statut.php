<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_exemplaire_statut ($parametres) {
    
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    
    $valeur=$_REQUEST["valeur"];
    
    $plugin_infos_statut=$parametres["plugin_infos_statut"];
    
    // 0) on maj $a
    $update=array("valeur"=>$valeur);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    // Le plugin est normalement déclenché par une icone => champ
    // mais il peut aussi l'être par une action sur le ss-champ => dans ce cas, on récupère ID_parent
    if ($infos["type_element"]=="ss_champ") {
        $ID_element=$infos["ID_parent"];
    }
    
    // 1) On récupère la valeur du $a
    $liste_a=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "a");
    $statut=$liste_a[0]["valeur"];
    
    // 2) on récupère les modifications à apporter aux autres ss-champs
    $tmp=applique_plugin ($plugin_infos_statut, array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $infos_statut=$tmp["resultat"];
    
    if (isset($infos_statut[$statut])) {
        foreach ($infos_statut[$statut] as $ss_champ => $valeur) {
            if ($valeur != "") {
                $liste_x=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, $ss_champ);
                $ID_element_ss_champ=$liste_x[0]["id"];
                array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element_ss_champ.'].set_valeur("'.$valeur.'");');
            }
        }
        
    }
    return ($retour);
    
}


?>