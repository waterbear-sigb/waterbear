<?php
/**
 * plugin_catalogue_marcxml_wizard_emplacement()
 * 
 * Ce plugin va retourner un choix dans un arbre � partir d'un certain nombre de crit�res
 * Il peut �tre utilis�e (par exemple) pour d�terminer le code emplacement � partir de la cote, du type doc et d'autres param�tres
 * 
 * [infos] => les crit�res qui seront utilis�s pour d�terminer quel code retourner. par exemple, si se base sur le type doc et la cote on pourra avoir :
 *            [0] => LIV , [1] => SF TOT
 * [choix] => l'arbre des choix possibles en fonction des crit�res ci dessus. A chaque niveau, un noeud [_else] indiquera le coix par d�faut
 *            par exemple :
 *              [LIV]
 *                    [0]=>g�n�ralit�s
 *                    [1]=>philosophie
 *                    [_else]=>ind�termin�
 *               [_else]
 *                    [R]=> fiction
 * 
 * NOTE : le teste se fait syst�matiquement avec trocature � droite (par ex. SF TOTO correspondra bien � SF)
 * 
 * NOTE2 : l'arbre est invers� de telle sorte que les codes les plus pr�cis seront test�s avant les plus g�n�raux. Ainsi 91 sera test� avant 9
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
    
    $infos=$parametres["infos"]; // les crit�res pertinents par exemple [0]=>LIV, [1]=>SF TOT
    $choix=$parametres["choix"]; // l'arbre de d�cision
    
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