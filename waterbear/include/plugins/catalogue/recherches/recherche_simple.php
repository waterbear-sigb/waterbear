<?php

// Include
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/recherche_simple.php");

/**
 * plugin_catalogue_recherches_recherche_simple()
 * 
 * Interface avec la classe recherche_simple
 * Tous les paramètres de la méthode init sont fournis dans le paramètre [param_recherche]
 * 
 * @param mixed $parametres
 * @param [param_recherche] => les paramètres attendus par recherche_simple::init() (cf la classe recherche_simple)
 * @param [count_seulement] => si vaut 1, on ne fait que le count
 * @param [somme] => nom de la colonne dont on veut la somme ** option **
 * @param [somme_seulement] => si vaut 1, on fait count et sum (mais pas requête)
 * 
 * @return array
 * @return [nb_pages] => nb de pages
 * @return [nb_notices] => nb de notices
 * @return [notices] => les notices (peut être très variable selon format choisi : d'une chaine SQL à une liste formatée...) 
 * @return [somme] => la somme d'une colonne ** option **
 * 
 */
function plugin_catalogue_recherches_recherche_simple ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $recherche=new recherche_simple();
    $recherche->init($parametres["param_recherche"]);
    
    $tmp=$recherche->count();
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $retour["resultat"]["nb_notices"]=$tmp["resultat"]["nb_notices"];
    $retour["resultat"]["nb_pages"]=$tmp["resultat"]["nb_pages"];
    
    if ($parametres["count_seulement"] == "1") {
        $retour["resultat"]["notices"]="";
        return ($retour);
    }
    
    if ($parametres["somme"] != "") {
        $tmp=$recherche->sum($parametres["somme"]);
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $retour["resultat"]["somme"]=$tmp["resultat"]["somme"];
    } else {
        $retour["resultat"]["somme"]=0;
    }
    
    if ($parametres["somme_seulement"] == "1") {
        return ($retour);
    }
    
    $tmp=$recherche->formate_resultat();
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $retour["resultat"]["notices"]=$tmp["resultat"];
    
    return ($retour);
} // fin du plugin



?>