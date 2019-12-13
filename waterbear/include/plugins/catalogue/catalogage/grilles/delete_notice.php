<?php

function plugin_catalogue_catalogage_grilles_delete_notice($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $plugin_delete=$parametres["plugin_delete"];
    $type_obj=$parametres["type_obj"];
    
    // 1) On récupère ID_notice (s'il n'y en a pas => erreur)
    $ID_notice=$_SESSION["operations"][$parametres["ID_operation"]]["ID_notice"];
    if ($ID_notice == "") {
        $retour["succes"]=0;
        $retour["erreur"]="cette notice ne peut etre supprimee car elle n'a pas ete enregistree";
        return ($retour);
    }
    
    // 2) suppression
    $tmp=applique_plugin($plugin_delete, array("ID"=>$ID_notice, "type_obj"=>$type_obj));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    $ID_notice=$tmp["resultat"]["ID_notice"]; // peut être mis à 0 si la notice est compltement supprimée
    
    $retour["resultat"][0]="alert('OK');";
    $retour["resultat"][1]='this_formulator.post_enregistrer_notice('.$ID_notice.');';
    return ($retour);
}
    



?>