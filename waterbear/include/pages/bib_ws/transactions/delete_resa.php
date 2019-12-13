<?php

// Paramètres passés au script
$ID_resa=$_REQUEST["ID_resa"];
$code_delete=$_REQUEST["code_delete"]; // si non fourni : vaut 43 (suppr. par bib)

$plugin_delete_resa=$GLOBALS["affiche_page"]["parametres"]["plugin_delete_resa"]; // plugin à utiliser

$tmp=applique_plugin($plugin_delete_resa, array("ID_resa"=>$ID_resa, "code_delete"=>$code_delete));
$output = $json->encode($tmp);
print($output);



?>