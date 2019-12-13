<?php
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/metawb.php");
$chaine=$_REQUEST["chaine"];

if ($chaine != "") {
    $_SESSION["registre"] = array();
    mwb_importe_registre ($chaine);
    die ("FIN");
}



affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");



?>