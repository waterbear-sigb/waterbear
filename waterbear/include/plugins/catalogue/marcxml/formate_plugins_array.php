<?php

/**
 * plugin_catalogue_marcxml_formate_plugins_array()
 *  
 * Ce plugin retourne un tableau associatif, chaque élément du tableau étant généré par un autre plugin
 * Il appelle successicement les plugins comme ceci :
 * [notice]<=([notice])
 * 
 * ATTENTION : à la base ce plugin fonctionnait avec les plugin "get_datafields_xxx" qui attendaient le paramètre en [notice] et retournaient le résultat en [notice]
 * Mais il peut aussi fonctionner avec d'autres plugins qui ont une autre signature. Dans ce cas, il faut utiliser des alias
 * 
 * @param mixed $parametres
 * @param [notice]
 * @param [plugins]
 * @param       [toto] => [nom_plugin]
 * @param                 [parametres]   
 * @param [force_retour] : si 1, on retourne qqchse, même si 1 ou plusieurs plugins ont retourné des erreurs. Sinon, on propage l'erreur
 * 
 * @return array => retourne directement le tableau à la racine
 */
function plugin_catalogue_marcxml_formate_plugins_array ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    if (! is_array($parametres["plugins"])) {
        return ($retour);
    }
    
    foreach ($parametres["plugins"] as $nom => $plugin) {
        $tmp=applique_plugin($plugin, array("notice"=>$parametres["notice"]));
        if ($tmp["succes"]=1) {
            $retour["resultat"][$nom]=$tmp["resultat"]["texte"];
        } else {
            if ($parametres["force_retour"] != 1) {
                return ($tmp);
            }
        }
    }
 
    return ($retour);
}

?>