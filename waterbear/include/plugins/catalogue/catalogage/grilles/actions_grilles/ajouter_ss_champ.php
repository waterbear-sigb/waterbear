<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_ajouter_ss_champ()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ], [ID_parent]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [auto_plugin] // plugin à utiliser pour ajouter le ss-champ
 * @param [action]
 * @return array
 * Ce plugin ajoute un sous-champ
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_ajouter_ss_champ ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $auto_plugin=$parametres["auto_plugin"];
    //$ID_parent=$parametres["infos"]["ID_parent"];
    
    $tmp=applique_plugin(array("nom_plugin"=>$auto_plugin), array()); 
    if ($tmp["succes"] != 1) {
        return ($tmp);
    } 
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);

    $retour["resultat"][0]=$infos;
    $retour["resultat"][1]="this_formulator.add_ss_champ(param);";

    return ($retour);    
    
}



?>