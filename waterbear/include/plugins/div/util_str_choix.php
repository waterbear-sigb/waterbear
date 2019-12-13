<?php

/**
 * plugin_div_util_str_choix()
 * 
 * Ce plugin retourne une chaine [texte] en fonction d'une chaine entr�e [texte]. Les diff�rentes valeurs possibles sont param�tr�es dans [liste]
 * on peut mettre [liste][_else] qui sera une valeur par d�faut
 * 
 * NOTE : m�me si ce plugin est pr�vu � la base pour retourner du texte, il peut tr�s bien retourner en fait une array (dans le registre)
 * 
 * @param mixed $parametres
 * @param [texte] => la valeur � tester
 * @param [liste][xxx|yyy|_else] => les diff�rentes valeurs possibles de [texte] et la valeur de remplacement
 *                                  Si vaut [_else] => valeur par defaut
 * @param on peut �galement mettre une clef [_void] qui sera appel�e si [texte] == ""
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