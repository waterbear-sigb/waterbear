<?php

/**
 * plugin_div_plugins_2_array()
 * 
 * @param mixed $parametres
 * @return array
 * 
 * Ce plugin retourne de mani�re r�cursive les �l�ments fournis en param�tre (soit qu'ils soient fournis via le scripts ou le registre)
 * SI un �l�ment commence par "!!", il est consid�r� correspondre � un plugin. Dans ce cas le script retourne � cet endroit ce qui a �t� retourn� par le plugin
 * Par exemple, si on a en param�res
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