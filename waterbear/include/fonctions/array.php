<?php

// Fonctions utiles pour manipuler les array()

////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * tvs_reordonne_array()
 * 
 * @param mixed $array
 * @return array
 * réordonne les clefs d'une array. Si on a par exemple [0, 2, 4, 5], on aura [0,1,2,3]
 */
function tvs_reordonne_array ($array) {
    $retour=array();
    $idx=0;
    foreach ($array as $element) {
        $retour[$idx]=$element;
        $idx++;
    }
    return ($retour);
}

/**
 * tvs_unset_array()
 * 
 * @param mixed $array
 * @param mixed $idx
 * @return array
 * Supprime l'élément $idx d'une array et réordonne les clefs (pour qu'il n'y ait pas de trou)
 */
function tvs_unset_array ($array, $idx) {
    unset ($array[$idx]);
    $retour=tvs_reordonne_array ($array);
    return ($retour);
}


/**
 * tvs_insert_array()
 * 
 * @param array $array : le tableau d'origine
 * @param mixes $ainserer : l'élément à insérer
 * @param int $idx : l'endroit où insérer (insère l'élément AVANT celui indiqué)
 * @return array
 * 
 * insère un élément dans une array et réordonne les clefs
 * AVANT l'élément indiqué par $idx
 * si $idx vide ou supérieur au max, sera mi à la fin
 * 
 */
function tvs_insert_array ($array, $ainserer, $idx) {
    $retour=array();
    $bool=0; // est-ce qu'on a inséré 
    foreach ($array as $clef => $valeur) {
        if ($idx <= $clef AND $bool==0) {
            array_push($retour, $ainserer);
            $bool=1;
        }
        array_push($retour, $valeur);
    }
    if ($bool == 0) { // si pas inséré à la fin c'est que $idx est vide ou supérieur : mettre à la fin
        array_push($retour, $ainserer);
    }
    return ($retour);
}







?>