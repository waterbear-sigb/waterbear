<?php

/**
 * plugin_div_formate_variables()
 * 
 * Ce plugin va remplacer les occurences de vraibales dans un mod�le.
 * 
 * par exemple un [modele] sera " Ceci est un #toto# particuli�rement stupide"
 * 
 * dans [variables] on aura [toto] => "exemple"
 * 
 * on retournera dans [texte] : "Ceci est un exemple particuli�rement stupide"
 * 
 * @param mixed $parametres
 * @param [modele] => le mod�le dans lequel on remplacera les variables par leur valeur
 * @param [variables] [xxx => yyy, aaa => bbbb, ...]
 * 
 * 
 * @return [texte]
 */
function plugin_div_formate_variables ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $modele=$parametres["modele"];
    $variables=$parametres["variables"];
    
    foreach ($variables as $intitule => $valeur) {
        $intitule="#".$intitule."#";
        $modele = str_ireplace($intitule, $valeur, $modele);
    }
    
    $retour["resultat"]["texte"]=$modele;
    
    
    return ($retour);
    
}

?>