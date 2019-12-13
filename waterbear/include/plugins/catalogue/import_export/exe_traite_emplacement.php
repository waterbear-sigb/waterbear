<?php

/**
 * plugin_catalogue_import_export_exe_traite_emplacement
 * 
 * Ce plugin retourne le code emplacement pressenti pour une notice exemplaire 
 * il prend en param�tre la notice et retourne [code]
 * [tvs_marcxml] ou [notice]
 * [plugin_get_infos] un plugin de formatage qui va retourner les infos utilis�es pour d�terminer l'emplacement (type doc, section, cote...). Les infos sont s�par�es par un pipe : LIV|adulte|R TOT infos attendues dans [texte]
 * [plugin_get_emplacement] le plugin qui va retourner l'emplacement dans [code] � partir des infos retourn�es par le plugin pr�c�dent. info attendue dans [code]
 * 
 * @param mixed $parametres
 * @return [code]
 */
 
function plugin_catalogue_import_export_exe_traite_emplacement($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $import_options=$parametres["import_options"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $notice=$parametres["notice"];
    
    // on r�cup�re $tvs_marcxml
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    $plugin_get_infos=$parametres["plugin_get_infos"];
    $plugin_get_emplacement=$parametres["plugin_get_emplacement"];
    
    // 1) on r�cup�re les infos pertinentes (cote, type doc, section...)
    $tmp=applique_plugin ($plugin_get_infos, array("tvs_marcxml" => $tvs_marcxml, "import_options"=>$import_options));
    if ($tmp["succes"]!=1) {
        return ($tmp);
    }
    $def_str=$tmp["resultat"]["texte"];
    $infos=explode("|", $def_str);
    
    // 2) on r�cup�re l'emplacement
    $tmp=applique_plugin($plugin_get_emplacement, array("infos"=>$infos));
    return ($tmp);
    
}



?>