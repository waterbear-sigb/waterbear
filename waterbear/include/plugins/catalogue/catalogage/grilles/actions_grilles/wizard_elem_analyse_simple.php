<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_simple()
 * 
 * Ce plugin va analyser une chaine de caract�re segment�e. Par exemple "Critique de la raison pure / Kant . - Gallimard"
 * et retourne un tableau [variables]{titre => critique de la raison pure, auteur => Kant, editeur => Gallimard}
 * Le descriptif est fourni par [segments] qui indique pour chaque segment le caract�re de s�paration [separateur] et le nom du segment [nom]
 * Le caract�re de s�paration doit pr�c�der le segment et peut mesurer plusieurs caract�res
 * Le 1er segment (pr�c�d� d'aucun caract�res est d�fini par [nom_base]
 * 
 * Si [segments] n'est pas d�fini, le plugin retournera simplement [nom_base]=>chaine
 * 
 * Si certains segments sont r�p�tables (ex France : histoire : tructruc ) on aura si par exemple on prend {nom => subdivision, separateur => ":"} :
 * {subdivision => histoire, subdivision_1 => tructruc}
 * 
 * @param mixed $parametres
 * @param [segments][0,1,2...][nom | separateur]
 * @param [nom_base] 
 * @param [chaine]
 * 
 * @return array
 * @return [variables][nom1 => valeur1, nom2 => valeur2...]
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_simple ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["variables"]=array();
    
    $chaine=$parametres["chaine"];
    $segments=$parametres["segments"];
    $nom_base=$parametres["nom_base"];
    
    if (! is_array($segments)) {
        $segments=array();
    }
    
    $longueur=strlen($chaine);
    $buffer="";
    
    // pour chaque caract�re (de la fin vers le d�but)
    for ($i=$longueur-1 ; $i >= 0 ; $i--) {
        $car=substr($chaine, $i, 1); 
        $buffer=$car.$buffer; // on rajoute au buffer
        foreach ($segments as $segment) { // pour chaque s�parateur de segment...
            $separateur=$segment["separateur"];
            $longueur_separateur=strlen($separateur);
            if (substr($buffer, 0, $longueur_separateur) == $separateur) { // Si on trouve un s�parateur de segment...
                $nom=$segment["nom"];
                $valeur=substr($buffer, $longueur_separateur, strlen($buffer)-$longueur_separateur);
                $valeur=trim($valeur);
                //$retour["resultat"]["variables"][$nom]=$valeur;
                $retour["resultat"]["variables"]=plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_simple_add_elem ($retour["resultat"]["variables"], $nom, $valeur);
                $buffer="";
                break;
            }
        }
    }
    
    // A la fin, on regarde s'il reste qqchse et on lui attribue le nom [nom_base]
    $buffer=trim($buffer);
    if ($buffer != "") {
        $retour["resultat"]["variables"][$nom_base]=$buffer;
    }
    
    return ($retour);
}

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_simple_add_elem ($tableau, $nom, $valeur) {
    if (!isset ($tableau[$nom])) {
        $tableau[$nom]=$valeur;
        return ($tableau);
    }

// A MODIFIER : car retourne les segments en ordre invers� et �a pose probl�me !!!
    $tableau2[$nom]=$valeur;
    $idx=1;
    foreach ($tableau as $elem) {
        $nom2=$nom."_"."$idx";
        $tableau2[$nom2]=$tableau[$nom];
        $idx++;
    }
    return ($tableau2);
    
}


?>