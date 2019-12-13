<?php
/**
 * plugin_catalogue_marcxml_wizard_emplacement()
 * 
 * Ce plugin va retourner un choix dans un arbre à partir d'un certain nombre de critères
 * Il peut être utilisée (par exemple) pour déterminer le code emplacement à partir de la cote, du type doc et d'autres paramètres
 * 
 * [infos] => les critères qui seront utilisés pour déterminer quel code retourner. par exemple, si se base sur le type doc et la cote on pourra avoir :
 *            [0] => LIV , [1] => SF TOT
 * [choix] => l'arbre des choix possibles en fonction des critères ci dessus. A chaque niveau, un noeud [_else] indiquera le coix par défaut
 *            par exemple :
 *              [LIV]
 *                    [0]=>généralités
 *                    [1]=>philosophie
 *                    [_else]=>indéterminé
 *               [_else]
 *                    [R]=> fiction
 * 
 * NOTE : le teste se fait systématiquement avec trocature à droite (par ex. SF TOTO correspondra bien à SF)
 * 
 * NOTE2 : l'arbre est inversé de telle sorte que les codes les plus précis seront testés avant les plus généraux. Ainsi 91 sera testé avant 9
 * 
 * retourne [code]
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_marcxml_wizard_emplacement ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"]; // les critères pertinents par exemple [0]=>LIV, [1]=>SF TOT
    $choix=$parametres["choix"]; // l'arbre de décision
    
    $pointeur=$choix;
    foreach($infos as $idx_info => $info) {
        $pointeur=plugin_catalogue_marcxml_wizard_emplacement_teste_pointeur($info, $pointeur);
        if ($pointeur=="") {
            return($retour);
        }
    }
    if (!is_string($pointeur)) {
        return($retour);
    }
    $retour["resultat"]["code"]=$pointeur;
    
    
    return($retour);   
}

function plugin_catalogue_marcxml_wizard_emplacement_teste_pointeur ($critere, $choix) {
    $choix=array_reverse($choix, true);
    settype($critere, "string");
    foreach ($choix as $choi => $tableau) { // ^^ 
    settype($choi, "string");

    $toto=stripos($critere, $choi);
        if ($toto === 0) {
            return($tableau);
        }
    }
    if (isset($choix["_else"])) {
        return ($choix["_else"]);
    } 
    return ("");
}



?>