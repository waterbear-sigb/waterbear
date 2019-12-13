<?php

/**
 * plugin_div_nettoie_nombre()
 * 
 * Ce plugin supprime tous les caract�res autres que num�riques et renvoie une chaine - nombre
 * peut �tre utilis� (par exemple) pour traiter le n� de tome dans les acc�s pour pemettre un tri num�rique en �tant s�r de ne pas avoir d'erreur sql du 
 * fait de la pr�sence de caract�res inattendus
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