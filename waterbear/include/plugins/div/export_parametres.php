<?php

function plugin_div_export_parametres ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $registre=new tvs_registre();
    
    $nom_parametre=$parametres["nom_parametre"];
    $code_langue=$parametres["code_langue"];
    $code_section=$parametres["code_section"]; // q (par défaut) ou j
    $format=$parametres["format"]; // format du retour : "array" ou "chaine"
    
    
    $branche=array();
    $branche_995=array();
    
    // 1. récupération des codes WB et codes 995
    try {
        if ($nom_parametre == "section") {
            $branche=$registre->registre_get_branche("profiles/defaut/langues/listes/catalogue/catalogage/grilles/exemplaire/section/_intitules", "");
            if ($code_section=="j") {
                $branche_995=$registre->registre_get_branche("profiles/defaut/plugins/plugins/catalogue/marcxml/convertir_code/exemplaire/rec995_section_j/parametres/liste_codes", "");
            } else {
                $branche_995=$registre->registre_get_branche("profiles/defaut/plugins/plugins/catalogue/marcxml/convertir_code/exemplaire/rec995_section/parametres/liste_codes", "");
            }
        } elseif ($nom_parametre == "type_doc") {
            $branche=$registre->registre_get_branche("profiles/defaut/langues/listes/catalogue/catalogage/grilles/exemplaire/type_doc/_intitules", "");
            $branche_995=$registre->registre_get_branche("profiles/defaut/plugins/plugins/catalogue/marcxml/convertir_code/exemplaire/rec995_type_doc/parametres/liste_codes", "");
        } elseif ($nom_parametre == "bibliotheque") {
            $branche=$registre->registre_get_branche("profiles/defaut/langues/listes/catalogue/catalogage/grilles/exemplaire/bibliotheque/_intitules", "");
        } elseif ($nom_parametre == "emplacement") {
            $branche=$registre->registre_get_branche("profiles/defaut/langues/listes/catalogue/catalogage/grilles/exemplaire/emplacement/_intitules", "");
        }
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["resultat"]=$e->get_exception();
        return($retour);
    }
    // 2. Création d'un array unique de la forme array [codes1, code2...][intitule|r995]
    $codes=array();
        
    foreach ($branche as $code => $langues) {
        if ($code === "-") {
            continue;
        }
        $codes[$code]=array();
        $codes["$code"]["intitule"]=$langues["_fr"];
    }
    foreach ($branche_995 as $code_995 => $code) { // ATTENTION : si plusieurs codes 995 pour un même code wb, il prendra le derneir, ce qui est le même comportement que l'export unimarc
        if (isset($codes[$code])) {
            $codes[$code]["r995"]=$code_995;
        }
    }
    
    // 3. éventuellement conversion en string
    if ($format=="chaine") {
        $tmp="";
        foreach ($codes as $code=>$elements) {
            print ($code."|".$elements["intitule"]."|".$elements["r995"]."\n");
        }
        $retour["resultat"]=$tmp;
    } else {
        $retour["resultat"]=$codes;
    }
    
    
    
       
    
    
    return ($retour);
}



?>