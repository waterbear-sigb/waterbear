<?php

/**
 * plugin_catalogue_recherches_formulaire_2_recherche()
 * 
 * Ce plugin convertit un tableau de param�tres de recherche retourn� par un formulaire
 * en tableau de param�tres pour les fonctions de recherche.
 * Selon les cas, la transformation peut �tre faible ou plus importante
 * 
 * @param mixed $parametres
 * @param [array_in] => le tableau contenant les criteres de recherche
 * @param [criteres] => criteres suppl�mentaires
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
    
    // crit�res suppl�mentaires
    foreach ($parametres["criteres"] as $critere) {
        array_push($array_out["criteres"], $critere);
    }
    
    $retour["resultat"]["array_out"]=$array_out;
    return ($retour);
}



?>