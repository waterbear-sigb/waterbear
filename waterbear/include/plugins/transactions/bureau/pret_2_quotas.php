<?php

/**
 * plugin_transactions_bureau_pret_2_quotas()
 * 
 * @param mixed $parametres
 * @param [bureau]
 * @param [arbre] => l'arbre des quotas à maj
 * @param [criteres] => criteres à récupérer sur le bureau
 * @param [validation_message] => oui|non => s'il faut forcer le prêt en cas de pb
 * 
 * 
 * @return
 * @return [arbre] => l'arbre maj
 * @return [duree] => durée du prêt
 * @return [depassement] => message d'erreur en cas de dépassement (indiquant le quota qui a bloqué...)
 * 
 */
function plugin_transactions_bureau_pret_2_quotas ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $arbre=$parametres["arbre"];
    $criteres=$parametres["criteres"];
    $validation_message=$parametres["validation_message"];
    //$arbre=$bureau["arbre"];
    //$criteres=$bureau["infos_quotas"]["criteres"];
    //$validation_message=$bureau["param_script"]["validation_message"];
    $duree_defaut=$arbre["_duree_defaut"];
    
    // on récupère sur le bureau les valeurs des différents critères paramétrés (type doc, section...)
    foreach ($criteres as $idx => $critere) {
        $criteres[$idx]["valeur"]=get_parametres_by_chemin($bureau, $critere["emplacement"]);
    }
    

    
    // on fait le test :
    // 1) est-ce que c'est possible [depassement] == 0
    // 2) maj [arbre]
    // 3) récupère [duree]
    $analyse_arbre=plugin_transactions_bureau_pret_2_quotas_analyse_arbre ($arbre, $criteres, $validation_message);
    if ($analyse_arbre["depassement"]===1) {
        $analyse_arbre["depassement"]=get_intitule ("bib/transactions/prets/standard", "message_quota_depasse_glob", array());
    }
    if ($analyse_arbre["duree"] == 0) {
        $analyse_arbre["duree"]=$duree_defaut;
    }
    $retour["resultat"]=$analyse_arbre;
    return ($retour);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function plugin_transactions_bureau_pret_2_quotas_analyse_arbre ($arbre, $criteres, $validation_message) {
    $retour=array();
    $max="";
    $duree="";
    $depassement=0;
    
    if ($arbre["_bloque"]==1) {
        return (array("arbre"=>$arbre, "depassement"=>get_intitule ("bib/transactions/prets/standard", "message_carte_bloquee", array())));
    }
    
    if (isset($arbre["_max"])) {
        $max=$arbre["_max"];
    } else {
        $max=0;
    }
    
    $compteur=$arbre["_compteur"];
    
    if ($compteur >= $max) {
        $depassement=1;
    }
    
    if ($arbre["_duree"] != "") {
        $duree = $arbre["_duree"];
    }
    
    // Si erreur et qu'on force pas, retour
    if ($depassement == 1 AND $validation_message != "oui") {
        return (array("arbre"=>$arbre, "depassement"=>1));
    }
    
    // si plus de criteres on retourne
    if (count ($criteres) == 0) {
        $arbre["_compteur"]++;
        return (array("arbre"=>$arbre, "duree"=>$duree, "depassement"=>0));
    }
    
    $tmp_critere=array_shift ($criteres);
    $critere=$tmp_critere["valeur"];
    
    if (!isset($arbre[$critere])) {
        $critere="_defaut";
    }
    
    if (isset($arbre[$critere])) {
        $ss_arbre=$arbre[$critere];
        $tmp=plugin_transactions_bureau_pret_2_quotas_analyse_arbre ($ss_arbre, $criteres, $validation_message);
        if ($tmp["depassement"] != "" AND $validation_message != "oui") {
            if ($tmp["depassement"]==1) {
                $tmp["depassement"]=get_intitule ("bib/transactions/prets/standard", "message_quota_depasse", array("quota"=>$critere));
            }
            return (array("arbre"=>$arbre, "depassement"=>$tmp["depassement"]));
        } else {
            $arbre[$critere]=$tmp["arbre"];
            $arbre["_compteur"]++;
            if ($tmp["duree"] != "") {
                $duree=$tmp["duree"];
                return (array("arbre"=>$arbre, "duree"=>$duree, "depassement"=>0));
            }
        } 
    } else {
        $tmp["depassement"]=get_intitule ("bib/transactions/prets/standard", "message_quota_inexistant", array("code_quota"=>$critere));
        return (array("arbre"=>$arbre, "depassement"=>$tmp["depassement"]));
    }
    
    
}


?>