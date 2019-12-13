<?php

$input_panier=$_REQUEST["input_panier"];

$liste_plugins=$GLOBALS["affiche_page"]["parametres"]["liste_plugins"];
$max_execution_time=$GLOBALS["affiche_page"]["parametres"]["max_execution_time"];
ini_set("max_execution_time", $max_execution_time);

$message="";
foreach ($liste_plugins as $plugin_messages) {
    $tmp=applique_plugin($plugin_messages, array("panier"=>$input_panier));
    if ($tmp["succes"] == 1) {
        $message.=$tmp["resultat"]["message"];
    }
    
}



affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("message"=>$message)));

    



include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>