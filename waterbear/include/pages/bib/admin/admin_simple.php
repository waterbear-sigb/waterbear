<?php
$erreurs="";

// Structure des paramètres
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_structure_parametres"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$structure_parametres=str_replace ('"', '\"', $json->encode($tmp["resultat"]));


$erreurs=str_replace ('"', '\"', $erreurs);
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_js"=>array("erreurs"=>$erreurs, "structure_parametres"=>$structure_parametres)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>