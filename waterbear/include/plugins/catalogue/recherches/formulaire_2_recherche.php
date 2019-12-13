<?php

/**
 * plugin_catalogue_recherches_formulaire_2_recherche()
 * 
 * Ce plugin convertit un tableau de paramètres de recherche retourné par un formulaire
 * en tableau de paramètres pour les fonctions de recherche.
 * Selon les cas, la transformation peut être faible ou plus importante
 * 
 * @param mixed $parametres
 * @param [array_in] => le tableau contenant les criteres de recherche
 * @param [criteres] => criteres supplémentaires
 * @param [nb_notices_par_page] => nb notices / page
 * @return
 */
function plugin_catalogue_recherches_formulaire_2_recherche ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $array_in=$parametres["array_in"];
    $array_out=array();

    $array_out=$array_in; 
    
    // nb notices par page
    $array_out["nb_notices_par_page"]=$parametres["nb_notices_par_page"];
    
    // critères supplémentaires
    foreach ($parametres["criteres"] as $critere) {
        array_push($array_out["criteres"], $critere);
    }
    
    $retour["resultat"]["array_out"]=$array_out;
    return ($retour);
}



?>