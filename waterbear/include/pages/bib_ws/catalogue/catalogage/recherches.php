<?php


// variables pass�es en param�tre
$query=$_REQUEST["query"]; // motif de recherche des champs autocomplete

// Variables pass�es via le registre
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"]; // plugin utilis� pour effectuer la recherche
$variables_requete=$GLOBALS["affiche_page"]["parametres"]["variables_requete"]; // variables pass�es en param�tre au script (autre que query) susceptibles d'�tre utilis�es dans la recherche

$param_plugin_recherche=array();
$param_plugin_recherche["query"]=$query;

// On rajoute �ventuellement des variables suppl�mentaires dans les param�tres du plugin de recherche
if (is_array ($variables_requete)) {
    foreach ($variables_requete as $variable_requete) {
        $param_plugin_recherche[$variable_requete]=$_REQUEST[$variable_requete];
    }
}


// !!! Le module autocomplete de YUI ne permet pas de g�rer d'�ventuelles erreurs
// Donc, on ne retourne que [resultat] (pas [succes] et [erreur])
$tmp=applique_plugin ($plugin_recherche, $param_plugin_recherche);
$retour=$tmp["resultat"]["notices"];

$output = $json->encode($retour);
print($output);
?>