<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_simple_validation()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @return array
 * Ce plugin se contente de synchroniser les fomulators (client et serveur) pour un élément sans effectuer d'autre traitement
 * Utilisé pour les champs simples (notes, résumé...)
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_simple_validation ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $valeur=$_REQUEST["valeur"];
  
    $update=array("valeur"=>$valeur);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);

    return ($retour);    
    
}



?>