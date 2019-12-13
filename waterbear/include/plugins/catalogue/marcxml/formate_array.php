<?php

/**
 * plugin_catalogue_marcxml_formate_array()
 * 
 * Formate une array. retourne une chaine de caractères
 * 
 * @param mixed $parametres
 * @param [avant]
 * @param [apres]
 * @param [avant_element]
 * @param [avant_element_verif]
 * @param [apres_element]
 * @param [tableau] => l'array à formater
 * 
 * @return [texte] => chaine formatée
 */
 
function plugin_catalogue_marcxml_formate_array ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $chaine="";
    $avant=$parametres["avant"];
    $apres=$parametres["apres"];
    $avant_element=$parametres["avant_element"];
    $avant_element_verif=$parametres["avant_element_verif"];
    $apres_element=$parametres["apres_element"];
    foreach ($parametres["tableau"] as $element) {
        if ($chaine != "" AND $avant_element_verif != "") {
            $chaine.=$avant_element_verif;
        }
        $chaine.=$avant_element.$element.$apres_element;
    }
    $chaine=$avant.$chaine.$apres;
    
    $retour["resultat"]["texte"]=$chaine;
    return ($retour);
}


?>