<?php

// Ce script va permettre de peupler un autocomplete avec des propositions estraites d'une liste du registre
// Les r�sultats peuvent �ventuellement �tre filtr�s par une chaine de caract�res

// Signature du plugin appel� pour r�cup�rer les donn�es :
// [] => [restricteur|nom_liste]
// restricteur est envoy� dans la requ�te sous le nom "query"


// variables pass�es en param�tre
$query=$_REQUEST["query"]; // motif de recherche des champs autocomplete
if ($query == " ") {
    $query=""; // le fait de taper espace va simplement retourner la liste compl�te
}

// variables du registre
$nom_liste=$GLOBALS["affiche_page"]["parametres"]["nom_liste"];

$retour=array();
// Variables pass�es via le registre
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"]; // plugin utilis� pour effectuer la recherche

// !!! Le module autocomplete de YUI ne permet pas de g�rer d'�ventuelles erreurs
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