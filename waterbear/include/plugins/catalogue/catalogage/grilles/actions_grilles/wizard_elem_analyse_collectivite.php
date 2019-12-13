<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_collectivite()
 * 
 * Ce plugin analyse une chaine du type "Aire France. comité d'entreprise"
 * et retourne un tableau du type ["entree"=>"Air France", "subdivision1"=>"Comité d'entreprise", ...]
 * jusqu'à 5 subdivisions
 * 
 * @param mixed $parametres
 * @param [chaine]
 * 
 * @return [variables]
 * @return -----------[entree|subdivision1|subdivision2...] 
 */

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_collectivite ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $retour["resultat"]["variables"]["subdivision1"]="";
    $retour["resultat"]["variables"]["subdivision2"]="";
    $retour["resultat"]["variables"]["subdivision3"]="";
    $retour["resultat"]["variables"]["subdivision4"]="";
    $retour["resultat"]["variables"]["subdivision5"]="";
    
    $chaine=$parametres["chaine"];
    
    $liste=explode(".", $chaine);
    
    // On récupère l'élement d'entrée
    $retour["resultat"]["variables"]["entree"]=ucfirst(trim($liste[0]));
    
    // On récupère les subdivisions
    if (count ($liste)>1) {
        for ($idx=1 ; $idx < count($liste) ; $idx++) {
            $retour["resultat"]["variables"]["subdivision".$idx]=ucfirst(trim($liste[$idx]));
        }
    }
    return ($retour);
    
}



?>