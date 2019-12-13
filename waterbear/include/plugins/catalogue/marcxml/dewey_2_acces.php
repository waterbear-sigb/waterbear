<?php

/**
 * plugin_catalogue_marcxml_dewey_2_acces()
 * 
 * Ce plugin convertit un indice Dewey en chaine pour la recherche. Il retire tout ce qui est non num�rique
 * 004.168 2 AUD ===> 0041682
 * 
 * @param mixed $parametres
 * @param [chaine] => la chaine � transformer
 * 
 * @return [chaine] => la chaine transform�e
 */
function plugin_catalogue_marcxml_dewey_2_acces ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["chaine"]="";
    
    $chaine=$parametres["chaine"];
    $longueur=strlen($chaine);
    
    for ($i=0 ; $i < $longueur ; $i++) {
        $car=substr($chaine, $i, 1);
        if (is_numeric($car)) {
            $retour["resultat"]["chaine"].=$car;
        }
    }
    
    return ($retour);
}


?>