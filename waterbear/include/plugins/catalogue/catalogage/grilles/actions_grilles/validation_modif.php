<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_validation_modif()
 * 
 * Ce plugin va transformer le contenu d'un sous-champ (passé via $_REQUEST["valeur"]) en lui appliquant le plugin plugin_modif
 * La signature du plugin est : [chaine]=>[chaine]
 * Il met à jour le formulator (coté serveur et client)
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [plugin_modif] => plugin à utiliser pour modifier la chaine 
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_validation_modif ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $chaine=$_REQUEST["valeur"];
    
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    
    $tmp=applique_plugin($parametres["plugin_modif"], array("chaine"=>$chaine));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    $update=array("valeur"=>$tmp["resultat"]["chaine"]);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$tmp["resultat"]["chaine"].'");');
    
    
    return ($retour);
    
}


?>