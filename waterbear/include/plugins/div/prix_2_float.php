<?php

/**
 * plugin_div_prix_2_float()
 * 
 * Ce plugin transforme un prix tel que trouvé dans le champ 010 en prix exploitable numériquement.
 * Supprime tout ce qui n'est pas numérique ou "." et remplace "," par "."
 * 
 * 
 * @param mixed $parametres
 * @param [texte] => le prix
 * 
 * @return [texte] => le prix transformé
 */
function plugin_div_prix_2_float ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $prix=$parametres["texte"];
    
    $nb_car=strlen($prix);
    $prix_float="";
    
    for ($i=0 ; $i<$nb_car ; $i++) {
        $car=substr($prix, $i, 1);
        if (is_numeric($car)) {
            $prix_float.=$car;
        } elseif ($car=="." OR $car==",") {
            $prix_float.=".";
        } else {
            // on ne fait rien
        }
    }
    
        
    $retour["resultat"]["texte"]=$prix_float;
    return($retour);
    
    
}



?>