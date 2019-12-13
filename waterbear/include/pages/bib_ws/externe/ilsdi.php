<?php
$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

header('Content-type: application/xml');

$plugin_ilsdi=$GLOBALS["affiche_page"]["parametres"]["plugin_ilsdi"];
$tmp=applique_plugin($plugin_ilsdi, array());
if ($tmp["succes"] != 1) {
    die ($tmp["erreur"]); // quelle manière unifiée de renvoyer une erreur ??
}
$xml=$tmp["resultat"]["xml"];
print($xml);


?>