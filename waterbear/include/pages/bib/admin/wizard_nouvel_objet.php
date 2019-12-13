<?php
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/wizard_nouvel_objet.php");
    
if ($_REQUEST["nom_obj"] != "") {
    wizard_nouvel_objet ();
} else {
    affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());
    include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");
}

?>