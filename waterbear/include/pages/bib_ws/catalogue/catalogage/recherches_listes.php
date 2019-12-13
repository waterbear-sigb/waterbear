<?php

// Ce script va permettre de peupler un autocomplete avec des propositions estraites d'une liste du registre
// Les résultats peuvent éventuellement être filtrés par une chaine de caractères

// Signature du plugin appelé pour récupérer les données :
// [] => [restricteur|nom_liste]
// restricteur est envoyé dans la requête sous le nom "query"


// variables passées en paramètre
$query=$_REQUEST["query"]; // motif de recherche des champs autocomplete
if ($query == " ") {
    $query=""; // le fait de taper espace va simplement retourner la liste complète
}

// variables du registre
$nom_liste=$GLOBALS["affiche_page"]["parametres"]["nom_liste"];

$retour=array();
// Variables passées via le registre
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"]; // plugin utilisé pour effectuer la recherche

// !!! Le module autocomplete de YUI ne permet pas de gérer d'éventuelles erreurs
// Donc, on ne retourne que [resultat] (pas [succes] et [erreur])
$tmp=applique_plugin ($plugin_recherche, array("restricteur"=>$query));
foreach ($tmp["resultat"] as $elem) {
    $elem["nom"]=$elem["intitule"];
    $elem["id"]=$elem["valeur"];
    unset($elem["intitule"]);
    unset($elem["valeur"]);
    array_push($retour, $elem);
}


$output = $json->encode($retour);
print($output);
?>