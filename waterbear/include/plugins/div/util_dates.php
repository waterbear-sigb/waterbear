<?php

/**
 * plugin_div_util_dates()
 * 
 * Ce plugin permet de faire des op�rations sur les dates. 2 types sont possibles :
 * Conversion (d�faut) ou diff ([operation] = diff)
 * 
 * // Conversion
 * Permet de convertir une date (ou date du jour) d'un format dans un autre en lui ajoutant/retranchant �ventuellement du temps
 * 
 * // Diff
 * Permet de faire la diff�rence entre 2 timestamps
 * La plupart du temps ils devront �tre fournis par des alias
 * 
 * @param mixed $parametres
 * @param [operation] => si vaut "diff" fera une diff�rence entre 2 dates
 * 
 * // Conversion
 * @param [format_entree] => format de la date fournie (timestamp, us ou fr)
 * @param [format_sortie] => format attendu (timestamp, us ou fr)
 * @param [date] => date au format indiqu� plus haut. Si nul => date actuelle
 * @param [modif] => "plus" ou "moins" [OPTION]  => si on veut ajouter/retrancher du temps � la date
 * @param [nb_modif] => nombre d'unit�s de temps � ajouter/retrancher
 * @param [unite_modif] => unit� de temps � ajouter/retrancher (m, h, j) => sinon ou si vide => secondes
 * 
 * // Diff
 * @param [timestamp_diff1] => premi�re date (timestamp)
 * @param [timestamp_diff2] => 2e date (timestamp)
 * @param [unite_diff] => unit� de temps pour le retour (m, h, j) => sinon ou si vide => secondes
 * 
 * @return
 * @return [date] OU [diff]
 */
function plugin_div_util_dates ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $operation=$parametres["operation"];
    $format_entree=$parametres["format_entree"];
    $format_sortie=$parametres["format_sortie"];
    $date=$parametres["date"];
    $modif=$parametres["modif"]; // "plus" ou "moins"
    $nb_modif=$parametres["nb_modif"];
    $unite_modif=$parametres["unite_modif"]; // s, m, h, j
    
    $timestamp_diff1=$parametres["timestamp_diff1"];
    $timestamp_diff2=$parametres["timestamp_diff2"];
    $unite_diff=$parametres["unite_diff"];
    
    if ($operation == "diff") {
        $diff=$timestamp_diff1 - $timestamp_diff2;
        if ($unite_diff == "m") {
            $diff=floor($diff/60); 
        } elseif ($unite_diff == "h") {
            $diff=floor($diff/3600);
        } elseif ($unite_diff == "j") {
            $diff=floor($diff/86400);
        }
        $retour["resultat"]["diff"]=$diff;
        return ($retour);
    }
    
    // 1) on convertit la date en timestamp
    $timestamp=conversion_date ($date, $format_entree, "timestamp");
    
    // 2) le cas �ch�ant, on ajoute ou on retranche du temps
    $timestamp2=$timestamp;
    if ($nb_modif != "") {
        if ($unite_modif == "m") {
            $nb_modif = $nb_modif * 60;
        } elseif ($unite_modif == "h") {
            $nb_modif = $nb_modif * 60 * 60;
        } elseif ($unite_modif == "j") {
            $nb_modif = $nb_modif * 60 * 60 * 24;
        }
        if ($modif == "moins") {
            $timestamp2=$timestamp-$nb_modif;
        } else {
            $timestamp2=$timestamp+$nb_modif;
        }
    } 
    
    // 3) on convertit le timestamp dans le format de sortie
    $date2=conversion_date ($timestamp2, "timestamp", $format_sortie);
    
    $retour["resultat"]["date"]=$date2;
    return ($retour);
}



?>