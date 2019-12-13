<?php
$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

$biblio=$_REQUEST["biblio"]; // chemin vers fichier biblio
$autorite=$_REQUEST["autorite"]; // chemin vers fichier autorités => non exploité pour l'instant
$date=date("Y-m-d");

$biblio=secure_sql($biblio);
$autorite=secure_sql($autorite);

if ($biblio != "") {
    sql_query(array("sql"=>"insert into mel_monitoring values ('', 'biblio', '$biblio', '$date', '0000-00-00')", "contexte"=>"bib_ws/externe/mel_monitoring"));
}

if ($autorite != "") {
    sql_query(array("sql"=>"insert into mel_monitoring values ('', 'autorite', '$autorite', '$date', '0000-00-00')", "contexte"=>"bib_ws/externe/mel_monitoring"));
}

$output = $json->encode($retour);
print($output);


?>