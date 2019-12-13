<?php
$interactif=$_REQUEST["interactif"];
$filtre=$_REQUEST["filtre"];
$fichier=$_REQUEST["fichier"];
$panier=$_REQUEST["panier"];
$import_options=$_REQUEST["import_options"];


// On récupère le fichier
$param=upload_file("fichier");
if ($param["erreur"] != "") {
  	affiche_template("erreurs/erreur_div.php", array("message"=>get_intitule("erreurs/messages_erreur", "impossible_uploader_fichier", array("message"=>$param["erreur"]))));
  	die("");
}
$_SESSION["operations"][$ID_operation]["fichier"]=$param["chemin"];
$_SESSION["operations"][$ID_operation]["taille_fichier"]=$param["taille"];
$_SESSION["operations"][$ID_operation]["type_objet"]=$type_objet;
$_SESSION["operations"][$ID_operation]["filtre"]=$filtre;
$_SESSION["operations"][$ID_operation]["import_options"]=$json->decode($import_options);


// On affiche le template
affiche_template($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());

$_SESSION["operations"][$ID_operation]["interactif"]=$interactif;
$_SESSION["operations"][$ID_operation]["panier"]=$panier;

// Au cas où il y aurait d'autres pages à afficher après celle-ci
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");


?>