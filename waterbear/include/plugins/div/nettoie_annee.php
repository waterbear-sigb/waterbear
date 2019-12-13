<?php

/**
 * plugin_div_nettoie_annee()
 * 
 * Ce plugin, nettoie la date du champ 210$d en enlevant crochets et autres commentaires.
 * Ne retourne qu'une chaine numérique de 4 caractères
 * 
 * [texte] plugin [texte]
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_div_nettoie_annee ($parametres) {
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
    
    if (strlen($chaine) != 4) {
        $chaine="";
    }
    
    $retour["resultat"]["texte"]=$chaine;
    return($retour);
    
    
    
    
    
    
}



?>