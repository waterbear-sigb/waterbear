<?php

// Si on n'a pas encore défini d'opération, on le fait
if ($ID_operation=="") {
    $ID_operation=get_id_operation();
    $_SESSION["operations"][$ID_operation]=array();
    
    // On instancie le bureau
    $_SESSION["operations"][$ID_operation]["bureau"]=array(); // Bureau
    $_SESSION["operations"][$ID_operation]["bureau"]["param_script"]=array(); // paramètres fournis directement via le WS
}

$GLOBALS["affiche_page"]["parametres"]["ID_operation"]=$ID_operation;


// Définition des onglets
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_def_onglets"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$def_onglets=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// type_2_grille
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_type_2_grille"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$type_2_grille=str_replace ('"', '\"', $json->encode($tmp["resultat"]));



affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_js"=>array("def_onglets"=>$def_onglets, "type_2_grille"=>$type_2_grille)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>