<?php

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// récupération des paramètres du registre
$plugin_export_parametres=$GLOBALS["affiche_page"]["parametres"]["plugin_export_parametres"];
$nom_parametre=$GLOBALS["affiche_page"]["parametres"]["nom_parametre"];
$code_langue=$GLOBALS["affiche_page"]["parametres"]["code_langue"];
$code_section=$GLOBALS["affiche_page"]["parametres"]["code_section"]; // q (par défaut) ou j
$format=$GLOBALS["affiche_page"]["parametres"]["format"]; // format du retour : "array" ou "chaine"

// Possible surcharge si passé en paramètre dans la requête http
if (isset($_REQUEST["nom_parametre"])) {
    $nom_parametre=$_REQUEST["nom_parametre"];
}

$tmp=applique_plugin($plugin_export_parametres, array("nom_parametre"=>$nom_parametre, "code_langue"=>$code_langue, "code_section"=>$code_section, "format"=>$format));
if ($tmp["succes"] != 1) {
    die ($tmp["erreur"]);
}
//$output = $json->encode($tmp);
//print($output);
print($retour["resultat"]);



?>