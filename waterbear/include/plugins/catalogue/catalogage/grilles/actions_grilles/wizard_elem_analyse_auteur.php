<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_auteur()
 * 
 * Ce plugin analyse une chaine du type "Duby, Georges (1950-...)"
 * et retourne un tableau du type ["nom"=>"Duby", "prenom"=>"Georges", "date"=>"1950-..."]
 * 
 * @param mixed $parametres
 * @param [chaine]
 * 
 * @return [variables]
 * @return -----------[nom|prenom|dates] 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_auteur ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $chaine=$parametres["chaine"];
    
    $nom="";
    $prenom="";
    $date="";
    
    // 1) Est-ce qu'il y a une date ?
    $pos1=strpos($chaine, "(");
    if ($pos1 === false) { // pas de date
        // on ne fait rien
    } else { // DATE !!
        $pos2=strpos($chaine, ")");
        if ($pos2 === false) { // si pas de parenthèse fermante, on en rajoute à la fin
            $chaine.=")";
            $pos2=strlen($chaine); // si pas de parenthèse fermante, on prend le dernier caractère
        }
        $dates = substr ($chaine, $pos1+1, ($pos2-$pos1)-1);
        $dates=trim($dates);
        
        // on retire la date de la chaine
        $chaine=substr($chaine, 0, $pos1);
    }
    
    // 2) on récupère le nom et éventuellement le prénom
    $tmp=explode (",", $chaine, 2);
    if (count($tmp) > 1) {
        $nom=trim($tmp[0]);
        $prenom=trim($tmp[1]);
    } else {
        $nom=trim($tmp[0]);
    }

    
    $retour["resultat"]["variables"]=array("nom"=>$nom, "prenom"=>$prenom, "dates"=>$dates);
    return ($retour);
}
?>