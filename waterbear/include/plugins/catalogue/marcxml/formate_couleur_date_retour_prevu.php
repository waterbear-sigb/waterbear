<?php

function plugin_catalogue_marcxml_formate_couleur_date_retour_prevu ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $date=$parametres["date"];
    $marge_avertissement=$parametres["marge_avertissement"]*60*60*24;
    $chaine_retard=$parametres["chaine_retard"];
    $chaine_avertissement=$parametres["chaine_avertissement"];
    
    
    $date_timestamp=date_us_2_timestamp($date);
    $now=time();
    
    $diff=$date_timestamp-$now;
    $chaine=$date;
    if ($diff < 0) { // retard
        $chaine=str_replace("#date#", $date, $chaine_retard);
    } elseif ($diff < $marge_avertissement) {
        $chaine=str_replace("#date#", $date, $chaine_avertissement);
    }
    
    $retour["resultat"]["chaine"]=$chaine;
    return($retour);
}


?>