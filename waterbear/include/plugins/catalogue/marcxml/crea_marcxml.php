<?php

/**
 * plugin_catalogue_marcxml_crea_marcxml()
 * 
 * Ce plugin cr�e un nouvel objet marcxml selon une d�finition
 * 
 * Option : pour un champ de lien explicite, on pourra reg�n�rer le champ � partir de la notice li�e
 * Pour cela fournir un [plugin_get_lien_explicite] au niveau du champ
 * Ce plugin doit avoir en [nom_plugin] => catalogue/marcxml/db/get_lien_explicite
 * et en param�tre
 * ----- [type] => type de la notice
 * ----- [ID] ou [notice] ID de la notice ou notice elle-m�me en XML
 * ----- [plugin_formate] le plugin va r�cup�rer et formater les infos dans la notice 
 * 
 * 
 * @param mixed $parametres
 * @param [definition][0,1,...] => la liste des champs
 * @param ---------------------[tag] => nom du champ (200, 700...)
 * @param ---------------------[definition][0,1,...][code|valeur] => liste des ss-champs avec code (a,b,c...) et valeur
 * @param ---------------------[plugin_get_lien_explicite] => va g�n�rer le champ � partir d'un ID de notice ou une notice XML de notice li�e (lien explicite)
 *                                                                Les ss-champs g�n�r�s s'ajouteront � ceux d�j� d�clar�s  
 * 
 * @return [notice] => notice xml
 */
function plugin_catalogue_marcxml_crea_marcxml ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $definition=$parametres["definition"];
    
    // 1) On cr�e une notice vide
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