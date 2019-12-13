<?php
/**
 * [param_recherche] (via requete)
 * [plugin_formulaire_2_recherche] ** option ** pour modifier les paramètres du formulaire
 * [plugin_total] le plugin qui va retourner la bloc des totaux (chaine de caractères)
 * 
*/


$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// 1) On récupère les paramètres de recherche
$param_recherche=$json->decode($_REQUEST["param_recherche"]);


// 2) Eventuellement, on modifie (ou enrichit) les paramètres
if (isset ($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire_2_recherche"])) {
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire_2_recherche"], array("array_in"=>$param_recherche)); // on modifie éventuellement les paramètres de recherche
    if ($tmp["succes"] != 1) {
        $output = $json->encode($retour);
        print($output);
        die();
    }
    $param_recherche=$tmp["resultat"]["array_out"];
}
    
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_total"], array("param_recherche"=>$param_recherche));
$total=$tmp["resultat"]["texte"];
$retour["resultat"]["texte"]=$total;

$output = $json->encode($retour);
print($output);

?>