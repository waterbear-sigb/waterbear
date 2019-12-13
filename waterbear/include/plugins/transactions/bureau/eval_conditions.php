<?php

/**
 * plugin_transactions_bureau_eval_conditions()
 * 
 * Ce plugin évalue les paramètres fournis (comparateurs mathématiques, logiques...)
 * [type_eval] indique le type d'évaluation à effectuer
 * 
 * Il retourne [eval] qui vaut généralement 0 ou 1
 * 
 * On peut utiliser un équivalent de parenthèses en incluant des plugins à la place d'un des paramètres avec !!
 * 
 * @param [type_eval] => type d'évaluation : egal|sup|sup_egal|inf|inf_egal|and|or
 * @param [p1] => paramètre 1
 * @param [p2] => paramètre 2
 * @param [liste_param] => liste de paramètres : si non fourni, on utilise p1 et p2 pour générer cette liste (utilisé quand on veut évaluer + de 2 paramètres par exemple pour and, or...)
 * 
 * @return [eval] => 0 ou 1
 */
function plugin_transactions_bureau_eval_conditions ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["eval"]=0;
    
    $type_eval=$parametres["type_eval"];
    $liste_param=$parametres["liste_param"];
    if (! is_array($liste_param)) {
        $liste_param=array("p1"=>$parametres["p1"], "p2"=>$parametres["p2"]);
    }
    
    if ($type_eval == "egal") { //////////////////////////////// =
        if ($parametres["p1"] == $parametres["p2"]) {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "sup") { //////////////////////////////// >
        if ($parametres["p1"] > $parametres["p2"]) {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "sup_egal") { //////////////////////////////// >=
        if ($parametres["p1"] >= $parametres["p2"]) {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "inf") { //////////////////////////////// <
        if ($parametres["p1"] < $parametres["p2"]) {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "inf_egal") { //////////////////////////////// <=
        if ($parametres["p1"] <= $parametres["p2"]) {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "null") { //////////////////////////////// == ""
        if ($parametres["p1"] == "") {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "not_null") { //////////////////////////////// != ""
        if ($parametres["p1"] != "") {
            $retour["resultat"]["eval"]=1;
        }
    } elseif ($type_eval == "and") { //////////////////////////////// AND
        foreach ($liste_param as $tmp) {
            if ($tmp == 0) {
                $retour["resultat"]["eval"]=0;
                return ($retour);
            }
        }
        $retour["resultat"]=1;
    } elseif ($type_eval=="or") { //////////////////////////////// OR
        foreach ($liste_param as $tmp) {
            if ($tmp == 1) {
                $retour["resultat"]["eval"]=1;
                break;
            }
        }
    }
    
    
    return ($retour);  
}
?>