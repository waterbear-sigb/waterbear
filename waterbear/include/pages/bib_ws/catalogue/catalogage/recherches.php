<?php


// variables passées en paramètre
$query=$_REQUEST["query"]; // motif de recherche des champs autocomplete

// Variables passées via le registre
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"]; // plugin utilisé pour effectuer la recherche
$variables_requete=$GLOBALS["affiche_page"]["parametres"]["variables_requete"]; // variables passées en paramètre au script (autre que query) susceptibles d'être utilisées dans la recherche

$param_plugin_recherche=array();
$param_plugin_recherche["query"]=$query;

// On rajoute éventuellement des variables supplémentaires dans les paramètres du plugin de recherche
if (is_array ($variables_requete)) {
    foreach ($variables_requete as $variable_requete) {
        $param_plugin_recherche[$variable_requete]=$_REQUEST[$variable_requete];
    }
}


// !!! Le module autocomplete de YUI ne permet pas de gérer d'éventuelles erreurs
// Donc, on ne retourne que [resultat] (pas [succes] et [erreur])
$tmp=applique_plugin ($plugin_recherche, $param_plugin_recherche);
$retour=$tmp["resultat"]["notices"];

$output = $json->encode($retour);
print($output);
?>