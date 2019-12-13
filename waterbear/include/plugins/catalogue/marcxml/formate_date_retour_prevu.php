<?php

/**
 * plugin_catalogue_marcxml_formate_date_retour_prevu()
 * 
 * Ce plugin génère une chaine pour al date de retour avec date de retour + icone pour prolonger + nb prolongations
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_marcxml_formate_date_retour_prevu ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["chaine"]="";
    
    $date_retour_prevu=$parametres["date_retour_prevu"];
    $nb_prolongations=$parametres["nb_prolongations"];
    $ID_pret=$parametres["ID_pret"];
    
   
    $plugin_formate_date=$parametres["plugin_formate_date"]; // option
    $chaine_globale=$parametres["chaine_globale"];
    
    // 1) : on remplace nb_prolongations : 0 => ""
    if ($nb_prolongations == 0) {
        $nb_prolongations="";
    }
    
    // 2) on formate la date de retour
    if (is_array($plugin_formate_date)) {
        $tmp=applique_plugin($plugin_formate_date, array("date"=>$date_retour_prevu));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $date_retour_prevu=$tmp["resultat"]["chaine"];
    }
    
    
    // 3) on génère la chaine globale
    $chaine_globale=str_replace("#date_retour_prevu#", $date_retour_prevu, $chaine_globale);
    $chaine_globale=str_replace("#ID_pret#", $ID_pret, $chaine_globale);
    $chaine_globale=str_replace("#nb_prolongations#", $nb_prolongations, $chaine_globale);
    
    $retour["resultat"]["chaine"]=$chaine_globale;

    return($retour);
}
?>