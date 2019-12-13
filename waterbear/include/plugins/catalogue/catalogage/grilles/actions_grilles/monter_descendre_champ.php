<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_monter_descendre_ss_champ()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ], [ID_parent]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [sens] // "monter" ou "descendre"
 * 
 * @return array
 * Ce plugin monte ou descend un sous-champ d'un cran (si sa position l'autorise) en fonction de $sens
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_monter_descendre_champ ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$parametres["infos"]["ID_parent"];
    $sens=$parametres["sens"]; // monter ou descendre
  
    $ID_rempl=$_SESSION["operations"][$ID_operation]["formulator"]->monte_descend_champ ($ID_element, $sens);
    
    if ($ID_rempl!==false) {
        if ($sens == "descendre") {
            $retour["resultat"][0]='this_formulator.liste_objets['.$ID_parent.'].move_element('.$ID_rempl.', '.$ID_element.');';
        } else {
            $retour["resultat"][0]='this_formulator.liste_objets['.$ID_parent.'].move_element('.$ID_element.', '.$ID_rempl.');';
        }
    }
    return ($retour);    
    
}



?>