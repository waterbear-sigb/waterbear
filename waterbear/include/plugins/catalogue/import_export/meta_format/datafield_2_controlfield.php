<?php

/**
 * plugin_catalogue_import_export_meta_format_datafield_2_controlfield()
 * 
 * Ce plugin permet de formater des champs codés. Waterbear divise les champs codés (type champ 100) en plusieurs sous-champs (str1, str2...)
 * Lors de l'export, il faut refusionner ces champs en une chaine de caratcère
 * Pour cela on prend en paramètre :
 * 
 * [champ] => le champ contenant les ss-champs à formater
 * [tvs_marcxml] => l'objet tvs_marcxml contenant le champ ci-dessus
 * [definition] => les infos de formatage qui a la forme suivante
 *             [ss_champs][0,1,2...][longueur|code|debut|valeur]
 * 
 * longueur et debut indiquent ou positionner le contenu du champ dans la chaine retournée. code est le nom du ss-champ donné (ex. str1)
 * Avec valeur on peut indiquer une valeur par défaut (car certaines informations ne sont pas gérées dans waterbear, on fournit donc une valeur par défaut pour l'export)
 * 
 * Ce plugin ne s'intéresse pas au fait de savoir s'il s'agit d'un datafield ou d'un controlfield. C'est le plugin appelant (xml2marc) qui placera la chaine obtenue
 * dans l'un ou l'autre
 * 
 * Cas particulier : si longueur n'est pas fournie, on retourne la valeur du champ sans formatage (utilisé pour 001) 
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_import_export_meta_format_datafield_2_controlfield ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    
    $champ=$parametres["champ"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $definition=$parametres["definition"];
     
    $longueur_totale=0;
    $chaine="";
    
    // 1) on génère une chaine vierge de la bonne longueur
    foreach($definition["ss_champs"] as $def) {
        $longueur_totale+=$def["longueur"];
    }
    $chaine=str_repeat(" ", $longueur_totale);
  
    // 2) pour chaque champ de la définition, on alimente la chaine
    foreach($definition["ss_champs"] as $def) {
        $longueur=$def["longueur"];
        $code=$def["code"];
        $debut=$def["debut"];
        $valeur=$def["defaut"];
        $ss_champs=$tvs_marcxml->get_ss_champs($champ, $code, "", "");
        if (isset($ss_champs[0])) {
            $valeur=$tvs_marcxml->get_valeur_ss_champ($ss_champs[0]);
        }
        if ($valeur != "") {
            if ($longueur != "") {
                $chaine=substr_replace($chaine, $valeur, $debut, $longueur);
            } else {
                $chaine=$valeur;
            }            
        }
    }
    
    // 3) on récupère les indices si existants
    $id1="";
    $id2="";
    $tmp=$tvs_marcxml->get_ss_champ_unique($champ, "id1", "", "");
    if ($tmp != "") {
        $id1=$tvs_marcxml->get_valeur_ss_champ($tmp);
    }
    
    $tmp=$tvs_marcxml->get_ss_champ_unique($champ, "id2", "", "");
    if ($tmp != "") {
        $id2=$tvs_marcxml->get_valeur_ss_champ($tmp);
    } 
    
    // 4) retour
    $retour["resultat"]["chaine"]=$chaine;
    $retour["resultat"]["id1"]=$id1;
    $retour["resultat"]["id2"]=$id2;
    return ($retour);   
}


?>