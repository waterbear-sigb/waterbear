<?php

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

$plugin_get_cab_incrementiel=$GLOBALS["affiche_page"]["parametres"]["plugin_get_cab_incrementiel"];

$tmp=applique_plugin($plugin_get_cab_incrementiel, array());
$output = $json->encode($tmp);
print($output);



?>