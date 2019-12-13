<?php

// Fonctions utiles pour manipuler les array()

////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * tvs_reordonne_array()
 * 
 * @param mixed $array
 * @return array
 * r�ordonne les clefs d'une array. Si on a par exemple [0, 2, 4, 5], on aura [0,1,2,3]
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
 * Supprime l'�l�ment $idx d'une array et r�ordonne les clefs (pour qu'il n'y ait pas de trou)
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
 * @param mixes $ainserer : l'�l�ment � ins�rer
 * @param int $idx : l'endroit o� ins�rer (ins�re l'�l�ment AVANT celui indiqu�)
 * @return array
 * 
 * ins�re un �l�ment dans une array et r�ordonne les clefs
 * AVANT l'�l�ment indiqu� par $idx
 * si $idx vide ou sup�rieur au max, sera mi � la fin
 * 
 */
function tvs_insert_array ($array, $ainserer, $idx) {
    $retour=array();
    $bool=0; // est-ce qu'on a ins�r� 
    foreach ($array as $clef => $valeur) {
        if ($idx <= $clef AND $bool==0) {
            array_push($retour, $ainserer);
            $bool=1;
        }
        array_push($retour, $valeur);
    }
    if ($bool == 0) { // si pas ins�r� � la fin c'est que $idx est vide ou sup�rieur : mettre � la fin
        array_push($retour, $ainserer);
    }
    return ($retour);
}







?>