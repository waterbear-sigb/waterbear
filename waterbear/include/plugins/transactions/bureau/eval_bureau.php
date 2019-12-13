<?php

/**
 * plugin_transactions_bureau_eval_bureau()
 * 
 * 2 solutions :
 * soit le plugin �value directement une valeur du bureau [valeur] et applique un plugin en fonction de sa valeur (cas simple)
 * soit la valeur doit �tre compar�e de mani�re plus complexe, auquel cas on fait appel � [plugin_evaluation]
 * Ce plugin utilise le plugin [plugin_evaluation] pour �valuer certaines variables du bureau par ex. bureau/nb_docs > bureau/nb_docs_max
 * En fonction de ce qui est retourn� (g�n�ralement 0 ou 1), on utilise une close [switch] pour appliquer certaines actions
 * Pour chaque cas on pourra d�terminer :
 * > une liste de plugins � effectuer (chaque plugin �tant susceptible de modifier le bureau)
 * > une close break
 * 
 * Signature du plugin �valuation :
 * ([eval])=plugin_evaluation([type_plugin], [p1], [p2], [liste_param(option)])
 * 
 * Signatue des plugins dans le switch :
 * ([bureau])=plugin([bureau]) => !!! le retour de [bureau] n'est pas obligatoire. S'il n'est pas retourn�, le bureau n'est pas modifi�.
 * 
 * 
 * 
 * @param mixed $parametres
 * @param [bureau] => le bureau
 * @param [valeur] => Si on �value directeent une valeur du bureau (sans passer par un plugin)
 * @param [plugin_evaluation] => le plugin utilis� pour �valuer certaines propri�t�s du bureau. 
 * @param                        En fonction des valeurs trouv�es, on appliquera telle ou telle action d�finies dans le switch
 * @param [switch][cond1, cond2, cond3..., else] => les diff�rents retours possibles de [plugin_evaluation]. On peut aussi utiliser la clef "else"
 * @param ---------- [break] => >0 il faudra arr�ter les traitements � l'issue de celui-ci'
 * @param ---------- [plugins][0,1,2...] => les plugins � appliquer si cette condition est remplie. Chaque plugin est susceptible de modifier le bureau (mais pas forc�ment)
 * 
 * @return [bureau] => bureau �ventuellement modifi� par les plugins du switch
 * @return [eval] => la valeur retourn�e (0 ou 1 g�n�ralement)
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
    
    // On �value les conditions
    if (isset($parametres["valeur"])) {
        $eval=$parametres["valeur"];
    } else {
        $tmp=applique_plugin ($plugin_evaluation, array("bureau"=>$bureau));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $eval=$tmp["resultat"]["eval"];
    }
    
    // est-ce que cette condition a �t� pr�vue
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