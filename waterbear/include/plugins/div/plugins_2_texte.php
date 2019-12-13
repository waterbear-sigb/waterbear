<?php

/**
 * plugin_div_plugins_2_texte()
 * 
 * @param mixed $parametres
 * @return array
 * 
 * Ce plugin retourne simplement le texte fourni dans l'attribut texte
 * 
 * @param [texte] => le texte
 * 
 * @return [texte] => même chose
 * 
 */
function plugin_div_plugins_2_texte ($parametres) {
    $retour = array ();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    //$parametres=plugins_2_param($parametres, array());
    $retour["resultat"]["texte"]=$parametres["texte"];
    return ($retour);
}



?>