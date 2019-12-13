<?php
//$nom=$_REQUEST["nom"]; // nom du noeud
$ID_script=$_REQUEST["ID_script"];
$ID_plugin=$_REQUEST["ID_plugin"];

if ($operation == "get_scripts") {
    $retour=dbg_plugins_client_get_scripts ();
} elseif ($operation == "delete_historique") {
    $retour=dbg_plugins_client_delete_historique ();
} elseif ($operation == "get_script") {
    $retour=dbg_plugins_client_get_script ($ID_script);
} elseif ($operation == "get_plugin") {
    $retour=dbg_plugins_client_get_plugin ($ID_script, $ID_plugin);
}



//$json = new Services_JSON();
$output = $json->encode($retour);
print($output);

?>