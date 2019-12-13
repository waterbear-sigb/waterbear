<?php

/**
 * plugin_transactions_bureau_eval_conditions()
 * 
 * Ce plugin �value les param�tres fournis (comparateurs math�matiques, logiques...)
 * [type_eval] indique le type d'�valuation � effectuer
 * 
 * Il retourne [eval] qui vaut g�n�ralement 0 ou 1
 * 
 * On peut utiliser un �quivalent de parenth�ses en incluant des plugins � la place d'un des param�tres avec !!
 * 
 * @param [type_eval] => type d'�valuation : egal|sup|sup_egal|inf|inf_egal|and|or
 * @param [p1] => param�tre 1
 * @param [p2] => param�tre 2
 * @param [liste_param] => liste de param�tres : si non fourni, on utilise p1 et p2 pour g�n�rer cette liste (utilis� quand on veut �valuer + de 2 param�tres par exemple pour and, or...)
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