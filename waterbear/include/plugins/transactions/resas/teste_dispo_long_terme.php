<?php

/**
 * plugin_transactions_resas_teste_dispo_long_terme()
 * 
 * @param mixed $parametres
 * @param [exemplaire] => notice exemplaire en ligne de base de données (avec les colonnes)
 * 
 * Ce plugin teste la réservabilité "long terme" d'un exemplaire
 * 
 * @return [reservable] (0 ou 1)
 * 
 */
function plugin_transactions_resas_teste_dispo_long_terme ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ligne=$parametres["exemplaire"];
    
    $reservable=$ligne["a_reservable"];
    $actif=$ligne["a_actif"];
    
    if ($reservable == "oui" AND $actif = "oui") {
        $retour["resultat"]["reservable"]=1;
    } else {
        $retour["resultat"]["reservable"]=0;
    }
    
    return ($retour);
    
}



?>