<?php
$plugin_get_liste_objets=$GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_objets"];


$liste_objets=applique_plugin($plugin_get_liste_objets, array());

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("liste_objets"=>$liste_objets["resultat"])));
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>