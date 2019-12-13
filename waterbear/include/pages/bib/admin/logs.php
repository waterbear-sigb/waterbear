<?php

$log_path=$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"];

$liste_fichiers=scandir($log_path);
$a_afficher="";

if ($_REQUEST["delete_log"] == "tout") {
  foreach ($liste_fichiers as $fichier) {
    if (substr ($fichier, 0, 1) == ".") {
        continue;
    }
    $chemin_complet=$log_path."/".$fichier;
    fopen ($chemin_complet, "w");
    $a_afficher.="On efface $chemin_complet <br>";
  } 
} elseif ($_REQUEST["delete_log"] != "") {
    $chemin_complet=$log_path."/".$_REQUEST["delete_log"];
    $a_afficher.="On efface $chemin_complet <br>";
    fopen ($chemin_complet, "w");
}








affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"],   array("param_tmpl_main" => array("liste_fichiers"=>$liste_fichiers, "log_path"=>$log_path, "a_afficher"=>$a_afficher)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>