<?php

$ID_doc=$_REQUEST["ID_doc"];
$ID_lecteur=$_REQUEST["ID_lecteur"];
$bib=$_REQUEST["bib"];
$validation_message=$_REQUEST["validation_message"];
if ($bib == "-") {
    $bib="";
}

$plugin_main=$GLOBALS["affiche_page"]["parametres"]["plugin_main"]; 

$retour=applique_plugin($plugin_main, array("ID_doc"=>$ID_doc, "ID_lecteur"=>$ID_lecteur, "validation_message"=>$validation_message, "bib"=>$bib));


$output = $json->encode($retour);
print($output);

?>