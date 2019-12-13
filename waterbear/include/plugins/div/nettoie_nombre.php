<?php

/**
 * plugin_div_nettoie_nombre()
 * 
 * Ce plugin supprime tous les caractères autres que numériques et renvoie une chaine - nombre
 * peut être utilisé (par exemple) pour traiter le n° de tome dans les accès pour pemettre un tri numérique en étant sûr de ne pas avoir d'erreur sql du 
 * fait de la présence de caractères inattendus
 * 
 * [texte] plugin [texte]
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_div_nettoie_nombre ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $texte=$parametres["texte"];
    $chaine="";
    for ($idx=0; $idx<strlen($texte); $idx++) {
        $car=substr($texte, $idx, 1);
        if (is_numeric($car)) {
            $chaine.=$car;
        }
    }
    
   
    $retour["resultat"]["texte"]=$chaine;
    return($retour);
    
    
    
    
    
    
}



?>