<?php

// Ce script va renvoyer une liste de paniers � un champ autocomplete
// on pourra soit sp�cifier le type_obj dans le registre, soit le r�cup�rer via la requ�te (sous forme de variable incluse)

// Signature du plugin appel� pour r�cup�rer les donn�es :
// [] => [restricteur|nom_liste]
// restricteur est envoy� dans la requ�te sous le nom "query"


// variables pass�es en param�tre
$query=$_REQUEST["query"]; // motif de recherche des champs autocomplete
$type_obj=$_REQUEST["type_obj"];

if ($query == " ") {
    $query=""; // le fait de taper espace va simplement retourner la liste compl�te
}

$retour=array();
// Variables pass�es via le registre
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"]; // plugin utilis� pour effectuer la recherche

// !!! Le module autocomplete de YUI ne permet pas de g�rer d'�ventuelles erreurs
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