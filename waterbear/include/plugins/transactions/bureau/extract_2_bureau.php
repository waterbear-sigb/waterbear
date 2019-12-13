<?php

/**
 * plugin_transactions_bureau_extract_2_bureau()
 * 
 * Ce plugin enrichit une variable passée en paramètre (une array appelée $bureau)
 * avec des variables, soit constantes (string, array, json)
 * soit extraites d'un plugin
 * soit extraites d'un autre emplacement du bureau (on copie une variable du bureau vers lui-même mais ailleurs)
 * 
 * Si [plugin_extraction] est défini, on récupérera les données d'un plugin (sinon, on copie des valeurs constantes)
 * 
 * Pour chaque extraction, on doit spécifier [destination] qui est l'endroit du BUREAU où copier les données
 * On peut copier 5 types de données :
 * > Une constante : [valeur]=xxx
 * > Une array (vide) : [type_data]=array
 * > Une array complexe via json : [type_data]=json et [valeur]="la chaine json à évaluer"
 * > Un élément retourné par le plugin : [origine] = data_plugin/xx/yy/zz (data_plugin correspond à $tmp[resultat])
 * > Un élément déjà présent sur le bureau : [origine] = bureau/xx/yy/zz
 * 
 * @param mixed $parametres
 * @param [bureau] => le bureau à enrichir
 * @param [plugin_extraction] => (optionnel) : s'il faut enrichir le bureau avec des variables extraites d'un plugin
 * @param [extractions][0,1,2...]
 * @param -------[destination] => emplacement dans le bureau où copier la valeur
 * @param -------[type_data] => type de valeur à copier. array|json (cf explication plus haut)
 * @param -------[type_extract] => si "push" on fait un array_push, si "unshift" on fait un array_unshift, sinon, on fait simplement un =
 * @param -------[valeur] => valeur à copier ou chaine json
 * @param -------[origine] => emplacement de la valeur à copier (si extraite du plugin ou du bureau)
 * 
 * @return [bureau] => le bureau avec les valeurs extraites
 */
function plugin_transactions_bureau_extract_2_bureau ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $plugin_extraction=$parametres["plugin_extraction"];
    $extractions=$parametres["extractions"];
    GLOBAL $json;
    
    $param=$parametres;
    
    // Si paramétré, on exécute un plugin pour en récupérer les infos
    if ($plugin_extraction["nom_plugin"] != "") {
        $tmp=applique_plugin ($plugin_extraction, array("bureau"=>$bureau));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $param["data_plugin"]=$tmp["resultat"];
    }
    
    // Pour chaque extraction
    foreach ($extractions as $extraction) {
        $origine=$extraction["origine"];
        $destination=$extraction["destination"];
        $valeur=$extraction["valeur"];
        $type_data=$extraction["type_data"];
        $type_extract=$extraction["type_extract"];
        
        // on récupère la valeur (sur le bureau, résultat du plugin ou data)
        if ($origine != "") {
            $valeur=get_parametres_by_chemin($param, $origine);
        } elseif ($type_data == "array") {
            $valeur=array();
        } elseif ($type_data == "json") {
            $valeur=$json->decode($valeur);
        }
        
        // on maj le bureau
        $bureau=plugin_transactions_bureau_extract_2_bureau_maj_valeur_array ($bureau, $destination, $valeur, $type_extract); 
    }
    
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);
} // fin du plugin

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function plugin_transactions_bureau_extract_2_bureau_maj_valeur_array ($parametres, $chemin_parametre, $valeur, $type) {
    $tmp=explode("/", $chemin_parametre, 2);
    
    if (count($tmp)==1) {
        $clef=$tmp[0];
        if ($type=="push") {
            array_push($parametres[$clef], $valeur);
        } elseif ($type == "unshift") {
            array_unshift($parametres[$clef], $valeur);
        } else {
            $parametres[$clef]=$valeur;
        }
        return ($parametres);
    }
    
    $clef=$tmp[0];
    $chemin=$tmp[1];
    $parametres[$clef]=plugin_transactions_bureau_extract_2_bureau_maj_valeur_array($parametres[$clef], $chemin, $valeur, $type);
    return ($parametres);
}
?>