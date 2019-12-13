<?php

/**
 * plugin_transactions_bureau_eval_bureau()
 * 
 * 2 solutions :
 * soit le plugin évalue directement une valeur du bureau [valeur] et applique un plugin en fonction de sa valeur (cas simple)
 * soit la valeur doit être comparée de manière plus complexe, auquel cas on fait appel à [plugin_evaluation]
 * Ce plugin utilise le plugin [plugin_evaluation] pour évaluer certaines variables du bureau par ex. bureau/nb_docs > bureau/nb_docs_max
 * En fonction de ce qui est retourné (généralement 0 ou 1), on utilise une close [switch] pour appliquer certaines actions
 * Pour chaque cas on pourra déterminer :
 * > une liste de plugins à effectuer (chaque plugin étant susceptible de modifier le bureau)
 * > une close break
 * 
 * Signature du plugin évaluation :
 * ([eval])=plugin_evaluation([type_plugin], [p1], [p2], [liste_param(option)])
 * 
 * Signatue des plugins dans le switch :
 * ([bureau])=plugin([bureau]) => !!! le retour de [bureau] n'est pas obligatoire. S'il n'est pas retourné, le bureau n'est pas modifié.
 * 
 * 
 * 
 * @param mixed $parametres
 * @param [bureau] => le bureau
 * @param [valeur] => Si on évalue directeent une valeur du bureau (sans passer par un plugin)
 * @param [plugin_evaluation] => le plugin utilisé pour évaluer certaines propriétés du bureau. 
 * @param                        En fonction des valeurs trouvées, on appliquera telle ou telle action définies dans le switch
 * @param [switch][cond1, cond2, cond3..., else] => les différents retours possibles de [plugin_evaluation]. On peut aussi utiliser la clef "else"
 * @param ---------- [break] => >0 il faudra arrêter les traitements à l'issue de celui-ci'
 * @param ---------- [plugins][0,1,2...] => les plugins à appliquer si cette condition est remplie. Chaque plugin est susceptible de modifier le bureau (mais pas forcément)
 * 
 * @return [bureau] => bureau éventuellement modifié par les plugins du switch
 * @return [eval] => la valeur retournée (0 ou 1 généralement)
 * @return [break] => si >0 il faudra interrompre les traitements
 */
function plugin_transactions_bureau_eval_bureau ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["eval"]=0;
    $retour["resultat"]["break"]=0;
    
    $switch=$parametres["switch"];
    $bureau=$parametres["bureau"];
    $plugin_evaluation=$parametres["plugin_evaluation"];
    
    // On évalue les conditions
    if (isset($parametres["valeur"])) {
        $eval=$parametres["valeur"];
    } else {
        $tmp=applique_plugin ($plugin_evaluation, array("bureau"=>$bureau));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $eval=$tmp["resultat"]["eval"];
    }
    
    // est-ce que cette condition a été prévue
    $case=$eval;
    if (!isset($switch[$eval])) {
        $case="else";
    }
    
    // Conditions
    if (is_array($switch[$case])) {
        if (isset($switch[$case]["break"])) {
            $retour["resultat"]["break"]=$switch[$case]["break"];
        }
        if (is_array($switch[$case]["plugins"])) {
            foreach ($switch[$case]["plugins"] as $plugin) {
                $tmp=applique_plugin($plugin, array("bureau"=>$bureau));
                if ($tmp["succes"] != 1) {
                    return ($tmp);
                }
                if (isset($tmp["resultat"]["bureau"])) {
                    $bureau=$tmp["resultat"]["bureau"];
                }
                if (isset($tmp["resultat"]["break"]) AND $tmp["resultat"]["break"] > $retour["resultat"]["break"]) {
                    $retour["resultat"]["break"]=$tmp["resultat"]["break"];
                }
            }
        }
    }
   
    $retour["resultat"]["eval"]=$eval;
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);  
}
?>