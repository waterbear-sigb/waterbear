<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_modif_215()
 * 
 * Ce plugin un peu batard permet de gérer les ss-champs $a et $d du champ 215 (collation)
 * on rajoute "p." au nombre de pages (si numérique). Idem "cm" au format
 * Le plugin regarde si la chaine est numérique. Si oui, il la modifie, sinon la retourne telle quelle
 * 
 * @param[chaine] => chaine à modifier
 * @param[prefixe] => à préfixer (si numérique)
 * @param[suffixe] => à suffixer (si numérique
 * 
 * @return[chaine] => la chaine modifiée (ou non)
 * 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_modif_215 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $chaine=$parametres["chaine"];
    $prefixe=$parametres["prefixe"];
    $suffixe=$parametres["suffixe"];
    
    if (is_numeric($chaine)) {
        $chaine=$prefixe.$chaine.$suffixe;
    }
    
    $retour["resultat"]["chaine"]=$chaine;
    return($retour);
}


?>