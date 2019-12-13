<?php
/**
 * plugin_div_get_cab_incrementiel()
 * 
 * Ce plugin retourne le n° incrémentiel suivant pour un type de cab et met à jour le compteur associé
 * 
 * @param mixed $parametres
 * @param [longueur] => la longueur du cab à générer (y compris le préfixe)
 * @param [prefixe] => préfixe du cab à générer
 * @param [chemin_compteur] => chemin du compteur dans le registre où est indiqué le dernier numéro à partir de system/compteurs/... : SANS le préfixe. peut être le nom du compteur ou précédé d'un chemin (sans commencer par slash)
 * 
 * @return [cab] => le cb généré
 * 
 */
function plugin_div_get_cab_incrementiel ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["cab"]="";
    
    $longueur=$parametres["longueur"];
    $prefixe=$parametres["prefixe"];
    $chemin_compteur=$parametres["chemin_compteur"];
    
    // on récupère le n° incrémentiel
    $incrementiel=get_compteur($chemin_compteur);
    if ($incrementiel===false) {
        $tmp["succes"]=0;
        $tmp["erreur"]="Le compteur system/compteur/$chemin_compteur n'existe pas";
        return ($tmp);
    }
    
    // on calcule longueur incrémentiel
    $longueur_incrementiel=$longueur;
    if ($prefixe!="") {
        $longueur_prefixe=strlen($prefixe);
        $longueur_incrementiel-=$longueur_prefixe;
    }
    
    // on rajoute autant de "0" que nécessaire avant incrementiel pour avoir la bonne longueur
    $suffixe=str_pad($incrementiel,$longueur_incrementiel, "0", STR_PAD_LEFT);
    
    // on colle le préfixe et le suffixe
    $retour["resultat"]["cab"]=$prefixe.$suffixe;
    
    return ($retour);
}



?>