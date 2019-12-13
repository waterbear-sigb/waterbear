<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_cab_incrementiel()
 * 
 * Ce plugin permet de générer un cab automatiquement (incrémentiel)
 * Il appelle un sous-plugin qui retourne le cab
 * Ensuite il maj le formulaire (client et serveur) et fait une validation
 * 
 * @param mixed $parametres
 * @return
 */
 
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_cab_incrementiel ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"]; // ss champ cab
    $ID_parent=$infos["ID_parent"];
    
    $plugin_get_cab_incrementiel=$parametres["plugin_get_cab_incrementiel"];
    
    
    // 1) on récupère le cab et on maj le registre
    $tmp=applique_plugin($plugin_get_cab_incrementiel, array());
    if ($tmp["succes"] != 1) {
        return($tmp);
    }
    $cab=$tmp["resultat"]["cab"];
    
    // 2) on maj le formaulaire côté client et serveur
    $update=array("valeur"=>$cab);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$cab.'");');
    
    // 3) on valide le ss-champ (comme si on avait pressé entrée)
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].validation();');
    
 
    
    return($retour);
}



?>