<?php

/**
 * plugin_div_prix_2_float()
 * 
 * Ce plugin transforme un prix tel que trouv� dans le champ 010 en prix exploitable num�riquement.
 * Supprime tout ce qui n'est pas num�rique ou "." et remplace "," par "."
 * 
 * 
 * @param mixed $parametres
 * @param [texte] => le prix
 * 
 * @return [texte] => le prix transform�
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