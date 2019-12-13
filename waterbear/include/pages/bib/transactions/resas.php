<?php
$plugin_liste_bibs=$GLOBALS["affiche_page"]["parametres"]["plugin_liste_bibs"];

$liste_bibs="<option value=''>#erreur#</option>";
$tmp=applique_plugin($plugin_liste_bibs, array("bool_texte"=>1));
if ($tmp["succes"] == 1) {
    $liste_bibs=$tmp["resultat"]["texte"];
}

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("liste_bibs"=>$liste_bibs)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");



?>