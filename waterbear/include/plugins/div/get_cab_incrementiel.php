<?php
/**
 * plugin_div_get_cab_incrementiel()
 * 
 * Ce plugin retourne le n� incr�mentiel suivant pour un type de cab et met � jour le compteur associ�
 * 
 * @param mixed $parametres
 * @param [longueur] => la longueur du cab � g�n�rer (y compris le pr�fixe)
 * @param [prefixe] => pr�fixe du cab � g�n�rer
 * @param [chemin_compteur] => chemin du compteur dans le registre o� est indiqu� le dernier num�ro � partir de system/compteurs/... : SANS le pr�fixe. peut �tre le nom du compteur ou pr�c�d� d'un chemin (sans commencer par slash)
 * 
 * @return [cab] => le cb g�n�r�
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
    
    // on r�cup�re le n� incr�mentiel
    $incrementiel=get_compteur($chemin_compteur);
    if ($incrementiel===false) {
        $tmp["succes"]=0;
        $tmp["erreur"]="Le compteur system/compteur/$chemin_compteur n'existe pas";
        return ($tmp);
    }
    
    // on calcule longueur incr�mentiel
    $longueur_incrementiel=$longueur;
    if ($prefixe!="") {
        $longueur_prefixe=strlen($prefixe);
        $longueur_incrementiel-=$longueur_prefixe;
    }
    
    // on rajoute autant de "0" que n�cessaire avant incrementiel pour avoir la bonne longueur
    $suffixe=str_pad($incrementiel,$longueur_incrementiel, "0", STR_PAD_LEFT);
    
    // on colle le pr�fixe et le suffixe
    $retour["resultat"]["cab"]=$prefixe.$suffixe;
    
    return ($retour);
}



?>