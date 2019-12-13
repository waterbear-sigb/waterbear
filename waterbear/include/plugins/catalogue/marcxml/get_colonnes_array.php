<?php

/**
 * plugin_catalogue_marcxml_get_colonnes_array()
 * 
 * Comme get_colonnes, mais retourne le résultat sous forme d'array.
 * Fait correspondre la colonne [nom_colonne] au champ [nom_champ] du tableau
 * 
 * @param array $parametres
 * @param [tableau] => le tableau contenant les colonnes à formater
 * @param [colonnes][0,1,2...][nom_colonne | nom_champ | avant | apres ] => infos contenant le formatage
 * 
 * 
 * @return [texte] => ATTENTION : un tableau
 */
function plugin_catalogue_marcxml_get_colonnes_array ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $tableau=$parametres["tableau"];
  
    $tableau_retour=array();
  
    foreach ($parametres["colonnes"] as $colonne) {

        $element="";
        $nom_colonne=$colonne["nom_colonne"];
        $avant=$colonne["avant"];
        $apres=$colonne["apres"];
        $nom_champ=$colonne["nom_champ"];
        if (! isset ($tableau[$nom_colonne])) {
            $element="";
        } else {
            $element=$tableau[$nom_colonne];
        }
        if ($element !== "") {
            $element=$avant.$element.$apres;
        }
        
        $tableau_retour[$nom_champ]=$element;
    }
    
    $retour["resultat"]["texte"]=$tableau_retour;
    
    return ($retour);
}



?>