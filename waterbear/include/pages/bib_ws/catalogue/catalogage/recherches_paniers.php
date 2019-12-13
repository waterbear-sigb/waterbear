<?php

// Ce script va renvoyer une liste de paniers à un champ autocomplete
// on pourra soit spécifier le type_obj dans le registre, soit le récupérer via la requête (sous forme de variable incluse)

// Signature du plugin appelé pour récupérer les données :
// [] => [restricteur|nom_liste]
// restricteur est envoyé dans la requête sous le nom "query"


// variables passées en paramètre
$query=$_REQUEST["query"]; // motif de recherche des champs autocomplete
$type_obj=$_REQUEST["type_obj"];

if ($query == " ") {
    $query=""; // le fait de taper espace va simplement retourner la liste complète
}

$retour=array();
// Variables passées via le registre
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"]; // plugin utilisé pour effectuer la recherche

// !!! Le module autocomplete de YUI ne permet pas de gérer d'éventuelles erreurs
// Donc, on ne retourne que [resultat] (pas [succes] et [erreur])
if ($type_obj != "") {
    $tmp=applique_plugin ($plugin_recherche, array("query"=>$query, "type_obj"=>$type_obj));
} else {
    $tmp=applique_plugin ($plugin_recherche, array("query"=>$query));
}
$retour=$tmp["resultat"];


$output = $json->encode($retour);
print($output);
?>