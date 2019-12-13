<?php

$retour=array();
$retour["succes"]=1;
$retour["resultat"]=array("nb_notices"=>0);

$timestamp=$_REQUEST["timestamp"];
$idx=$_REQUEST["idx"];
$retour["resultat"]["idx"]=$idx;
$_SESSION["system"]["timestamp_last_schedule"]=$timestamp;



$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"];

$tmp=applique_plugin ($plugin_recherche, array());
if ($tmp["succes"] != 1) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}
$retour["resultat"]["nb_notices"]=$tmp["resultat"]["nb_notices"];

// On enregistre dans la session le nb de notices trouvées pour que l'icone s'afficha automatiquement en cas de changement de page
if (!is_array($_SESSION["system"]["schedule_recherches"])) {
    $_SESSION["system"]["schedule_recherches"]=array();
}
if (!is_array($_SESSION["system"]["schedule_recherches"][$idx])) {
    $_SESSION["system"]["schedule_recherches"][$idx]=array();
}
$_SESSION["system"]["schedule_recherches"][$idx]["nb_notices"]=$retour["resultat"]["nb_notices"];

$output = $json->encode($retour);
print($output);
?>