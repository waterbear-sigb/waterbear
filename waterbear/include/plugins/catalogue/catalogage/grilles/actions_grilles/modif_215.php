<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_modif_215()
 * 
 * Ce plugin un peu batard permet de g�rer les ss-champs $a et $d du champ 215 (collation)
 * on rajoute "p." au nombre de pages (si num�rique). Idem "cm" au format
 * Le plugin regarde si la chaine est num�rique. Si oui, il la modifie, sinon la retourne telle quelle
 * 
 * @param[chaine] => chaine � modifier
 * @param[prefixe] => � pr�fixer (si num�rique)
 * @param[suffixe] => � suffixer (si num�rique
 * 
 * @return[chaine] => la chaine modifi�e (ou non)
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