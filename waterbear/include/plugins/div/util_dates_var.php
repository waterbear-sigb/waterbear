<?php

/**
 * plugin_catalogue_marcxml_verifie_notice()
 * 
 * Ce plugin permet de convertir certains codes en dates. Cela permet d'éviter d'avoir à modifier certaines recherches ou stats tous les ans
 * 
 * Il prend [chaine] et paramètres et retourne [chaine]
 * 
 * an, an1, an2, an3... ====> 2013, 2012, 2011, 2010...
 * date, date1, date2, date3... ====> 2013-08-15, 2013-08-14, 2013-08-13, 2013-08-12 ...
 * date_an, date_an1, date_an2, date_an3... => 2013-08-15, 2012-08-15, 2011-08-15, 2010-08-15...
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_div_util_dates_var($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $chaine=$parametres["chaine"];
    $retour["resultat"]["chaine"]=$chaine;
    $maintenant=time();
    $jour=60*60*24;
    $annee=365*$jour;
    
    if (stripos($chaine, "date_an")!==false) {
        $reste=str_ireplace("date_an", "", $chaine);
        if (is_numeric($reste)) {
            $maintenant-=$annee*$reste;
        }
        $retour["resultat"]["chaine"]=date("Y-m-d", $maintenant);
    } elseif (stripos($chaine, "date")!==false) {
        $reste=str_ireplace("date", "", $chaine);
        if (is_numeric($reste)) {
            $maintenant-=$jour*$reste;
        }
        $retour["resultat"]["chaine"]=date("Y-m-d", $maintenant);
    } elseif (stripos($chaine, "an")!==false) {
        $reste=str_ireplace("an", "", $chaine);
        if (is_numeric($reste)) {
            $maintenant-=$annee*$reste;
        }
        $retour["resultat"]["chaine"]=date("Y", $maintenant);
    }
    
    return ($retour);
}

?>