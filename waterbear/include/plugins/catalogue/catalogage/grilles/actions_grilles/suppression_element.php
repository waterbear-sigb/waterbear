<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_suppression_element()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ], [ID_parent]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @return array
 * Ce plugin supprime un élément SANS EFFECTUER de vérification coté serveur (si le champ est obligatoire)
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_suppression_element ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$parametres["infos"]["ID_parent"];
  
    $_SESSION["operations"][$ID_operation]["formulator"]->delete_element ($ID_element);

    $retour["resultat"][0]='this_formulator.delete_element('.$ID_element.', '.$ID_parent.');';
    //$retour["resultat"][0]='this_formulator.delete_element('.$ID_element.', 1);';

    return ($retour);    
    
}



?>