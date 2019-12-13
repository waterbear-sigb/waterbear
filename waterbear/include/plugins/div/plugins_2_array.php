<?php

/**
 * plugin_div_plugins_2_array()
 * 
 * @param mixed $parametres
 * @return array
 * 
 * Ce plugin retourne de manière récursive les éléments fournis en paramètre (soit qu'ils soient fournis via le scripts ou le registre)
 * SI un élément commence par "!!", il est considéré correspondre à un plugin. Dans ce cas le script retourne à cet endroit ce qui a été retourné par le plugin
 * Par exemple, si on a en paramères
 * [intitule]=>toto
 * [valeur]=>tutu
 * [!!liste_champs]=>[nom_plugin]=>aa/bb/cc, [parametres]=>???
 * 
 * le plugin retournera
 * [intitule]=>toto
 * [valeur]=>tutu
 * [liste_champs]=> XXXX (ce que retourne le plugin aa/bb/cc)
 * 
 */
function plugin_div_plugins_2_array ($parametres) {
    $retour = array ();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    //$parametres=plugins_2_param($parametres, array());
    $retour["resultat"]=$parametres;
    return ($retour);
}



?>