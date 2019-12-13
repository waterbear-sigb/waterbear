<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_ajouter_champ()
 * 
 * @param mixed $parametres
 * @param [idx_onglet]
 * @param [ID_operation]
 * @param [nom_champ]
 * @param [action]
 * @return array
 * Ce plugin Ajoute un champ dans un onglet $idx_onglet
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_ajouter_champ ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];
    $idx_onglet=$parametres["idx_onglet"];
    $auto_plugin=$parametres["auto_plugin"];
    //$ID_parent=$parametres["infos"]["ID_parent"];
    
    $tmp=applique_plugin(array("nom_plugin"=>$auto_plugin), array()); 
    if ($tmp["succes"] != 1) {
        return ($tmp);
    } 
    
    // Retourne dans $infos toutes les infos pour générer le champ coté client
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_champ ($idx_onglet, $tmp["resultat"]);
    

    // On retourne d'abord les paramètres puis la méthode qui seront combinés coté client
    $retour["resultat"][0]=$infos;
    $retour["resultat"][1]="this_formulator.add_champ(param);";

    return ($retour);    
    
}



?>