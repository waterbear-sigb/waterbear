<?php

/**
 * plugin_div_util_str_choix()
 * 
 * Ce plugin retourne une chaine [texte] en fonction d'une chaine entrée [texte]. Les différentes valeurs possibles sont paramétrées dans [liste]
 * on peut mettre [liste][_else] qui sera une valeur par défaut
 * 
 * NOTE : même si ce plugin est prévu à la base pour retourner du texte, il peut très bien retourner en fait une array (dans le registre)
 * 
 * @param mixed $parametres
 * @param [texte] => la valeur à tester
 * @param [liste][xxx|yyy|_else] => les différentes valeurs possibles de [texte] et la valeur de remplacement
 *                                  Si vaut [_else] => valeur par defaut
 * @param on peut également mettre une clef [_void] qui sera appelée si [texte] == ""
 * 
 * @return [texte]
 */
function plugin_div_util_str_choix ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $texte=$parametres["texte"];
    $liste=$parametres["liste"];
    $else=$liste["_else"];
    if ($texte == "") {
        $texte="_void";
    }
    
    if (isset($liste[$texte])) {
        $retour["resultat"]["texte"]=$liste[$texte];
    } else {
        $retour["resultat"]["texte"]=$else;
    }
    
    return ($retour);
}

?>