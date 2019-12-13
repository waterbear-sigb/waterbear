<?php

function plugin_catalogue_periodiques_analyse_date_reception ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $texte=$parametres["texte"];
    
    $elements=explode("|", $texte);
    $prochaine_date=$elements[0];
    $marge=$elements[1];
    $prochaine_date_timestamp=strtotime($prochaine_date);
    $now=time();
    $now_chaine=date("Y-m-d", $now);
    
    if ($prochaine_date_timestamp === false) {
        $retour["resultat"]["texte"]="bulletinage_erreur";
    } elseif ($prochaine_date == $now_chaine) {
        $retour["resultat"]["texte"]="bulletinage_OK";
    } elseif (($prochaine_date_timestamp - $marge*24*60*60)>$now) {
        $retour["resultat"]["texte"]="bulletinage_passe";
    } elseif (($prochaine_date_timestamp + $marge*24*60*60)<$now) {
        $retour["resultat"]["texte"]="bulletinage_retard";
    } else {
        $retour["resultat"]["texte"]="bulletinage_marge";
    }
    
    
    
    
    
    
    
    return ($retour);
}


?>