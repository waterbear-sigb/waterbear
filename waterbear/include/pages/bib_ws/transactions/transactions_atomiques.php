<?php

// Paramètres passés au script
$mode=$_REQUEST["mode"];
$cab_lecteur=$_REQUEST["cab_lecteur"];
$cab_doc=$_REQUEST["cab_doc"];
$validation_message=$_REQUEST["validation_message"];

// Paramètres du registre
$plugin_transaction=$GLOBALS["affiche_page"]["parametres"]["plugin_transaction"]; // plugin à utiliser

$tmp=applique_plugin($plugin_transaction, array("mode"=>$mode, "cab_lecteur"=>$cab_lecteur, "cab_doc"=>$cab_doc, "validation_message"=>$validation_message));
$output = $json->encode($tmp);
print($output);


?>