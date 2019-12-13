<?php

/**
 * plugin_div_get_intitule_liste()
 * 
 * Ce plugin retourne un intitule à partir d'un code d'une liste
 * 
 * @param mixed $parametres
 * @param [nom_liste]
 * @param [texte] => le code
 * 
 * @return [texte] => l'intitulé correspondant au code
 */
function plugin_div_get_intitule_liste ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $nom_liste="listes/".$parametres["nom_liste"];
    $nom_element=$parametres["texte"];
    
    $intitule=get_intitule($nom_liste, $nom_element, array());
    
    $retour["resultat"]["texte"]=$intitule;
    
    return ($retour);
    
}



?>