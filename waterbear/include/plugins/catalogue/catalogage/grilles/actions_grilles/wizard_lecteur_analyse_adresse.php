<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_analyse_adresse()
 * 
 * Ce plugin va analyser une adresse ayant la forme "rue des sports : 44130 Fay-de-Bretagne" pour en r�cup�rer le nom de la rue, le CP et la ville
 * il prend en param�tre [chaine]
 * il retourne [variables][rue|CP|ville]
 * 
 * @param mixed $parametres
 * @return [variables][rue|CP|ville]
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_analyse_adresse ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $chaine=$parametres["chaine"];
    
    // 1) on s�pare par ":"
    $segments=explode(":", $chaine);
    if (count($segments)!=2) {
        $retour["succes"]=0;
        $retour["erreur"]="Vous devez saisir l'adresse sous la forme RUE : CP VILLE";
        return ($retour);
    }
    
    $rue=trim($segments[0]);
    
    // on s�pare CP et ville
    $elements=explode(" ", trim($segments[1]), 2);
    if (count ($elements) != 2) {
        $retour["succes"]=0;
        $retour["erreur"]="Vous devez saisir l'adresse sous la forme RUE : CP VILLE";
        return ($retour);
    }
    
    $CP= trim($elements[0]);
    $ville= trim($elements[1]);
    
    $retour["resultat"]["variables"]=array("rue"=>$rue, "CP"=>$CP, "ville"=>$ville);
    
    return ($retour);
}


?>