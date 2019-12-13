<?php

/**
 * plugin_catalogue_marcxml_crea_marcxml()
 * 
 * Ce plugin crée un nouvel objet marcxml selon une définition
 * 
 * Option : pour un champ de lien explicite, on pourra regénérer le champ à partir de la notice liée
 * Pour cela fournir un [plugin_get_lien_explicite] au niveau du champ
 * Ce plugin doit avoir en [nom_plugin] => catalogue/marcxml/db/get_lien_explicite
 * et en paramètre
 * ----- [type] => type de la notice
 * ----- [ID] ou [notice] ID de la notice ou notice elle-même en XML
 * ----- [plugin_formate] le plugin va récupérer et formater les infos dans la notice 
 * 
 * 
 * @param mixed $parametres
 * @param [definition][0,1,...] => la liste des champs
 * @param ---------------------[tag] => nom du champ (200, 700...)
 * @param ---------------------[definition][0,1,...][code|valeur] => liste des ss-champs avec code (a,b,c...) et valeur
 * @param ---------------------[plugin_get_lien_explicite] => va générer le champ à partir d'un ID de notice ou une notice XML de notice liée (lien explicite)
 *                                                                Les ss-champs générés s'ajouteront à ceux déjà déclarés  
 * 
 * @return [notice] => notice xml
 */
function plugin_catalogue_marcxml_crea_marcxml ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $definition=$parametres["definition"];
    
    // 1) On crée une notice vide
    $marcxml=new tvs_marcxml(array());
    $marcxml->new_notice();
    
    // 2) On ajoute les champs
    foreach ($definition as $tmp) {
        $tag=$tmp["tag"];
        $def=$tmp["definition"];
        if (! is_array($def)) {
            $def=array();
        }
        $plugin_get_lien_explicite=$tmp["plugin_get_lien_explicite"];
        
        if ($plugin_get_lien_explicite != "") {
            $bidon=applique_plugin($plugin_get_lien_explicite, array());
            if ($bidon["succes"] != 1) {
                return ($bidon);
            }
            $def=array_merge($def, $bidon["resultat"]["champ"]);
        }
        $marcxml->add_champ($tag, $def, "last");
    }
    
    // 3) on retourne la notice
    $retour["resultat"]["notice"]=$marcxml->notice;
    //$retour["resultat"]["notice"]=$marcxml->notice->saveXML(); // TMP !!!!!
    
    
    return ($retour);
} // fin du plugin
?>