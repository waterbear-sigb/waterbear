<?php

/**
 * plugin_catalogue_marcxml_formate_plugins_array()
 *  
 * Ce plugin retourne un tableau associatif, chaque �l�ment du tableau �tant g�n�r� par un autre plugin
 * Il appelle successicement les plugins comme ceci :
 * [notice]<=([notice])
 * 
 * ATTENTION : � la base ce plugin fonctionnait avec les plugin "get_datafields_xxx" qui attendaient le param�tre en [notice] et retournaient le r�sultat en [notice]
 * Mais il peut aussi fonctionner avec d'autres plugins qui ont une autre signature. Dans ce cas, il faut utiliser des alias
 * 
 * @param mixed $parametres
 * @param [notice]
 * @param [plugins]
 * @param       [toto] => [nom_plugin]
 * @param                 [parametres]   
 * @param [force_retour] : si 1, on retourne qqchse, m�me si 1 ou plusieurs plugins ont retourn� des erreurs. Sinon, on propage l'erreur
 * 
 * @return array => retourne directement le tableau � la racine
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