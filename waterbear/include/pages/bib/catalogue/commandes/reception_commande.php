<?php

// on crée un ID_operation
if ($ID_operation=="") {
    $ID_operation=get_id_operation();
    $_SESSION["operations"][$ID_operation]=array();
}


$GLOBALS["affiche_page"]["parametres"]["ID_operation"]=$ID_operation;
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>