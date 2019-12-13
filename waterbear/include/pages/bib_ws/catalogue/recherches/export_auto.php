<?php

$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"];
$clef_timestamp=$GLOBALS["affiche_page"]["parametres"]["clef_timestamp"];

// 1) on récupère le timestamp
$timestamp=0;
if ($clef_timestamp != "") {
    $timestamp=get_registre("system/timestamps/$clef_timestamp");
}

// 2) on effectue la recherche
$tmp=applique_plugin($plugin_recherche, array("variables"=>$_REQUEST, "timestamp"=>$timestamp));
if ($tmp["succes"] != 1) {
    die($tmp["erreur"]);
}

// 3) on maj le timestamp
if ($clef_timestamp != "") {
    set_registre ("system/timestamps/$clef_timestamp", time(), "donnees exportees le ".date("d/m/Y")." a ".date("G:i:s"));
}

$notices=$tmp["resultat"]["notices"];
//foreach ($notices as $notice) {
//    print_r($notice);
//}
//print_r($notices);

print($notices);





?>