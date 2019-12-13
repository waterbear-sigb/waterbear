<?PHP
// Si on n'a pas encore défini d'opération, on le fait

$ID_notice=$_REQUEST["ID_notice"];
if ($ID_operation=="") {
    $ID_operation=get_id_operation();
    $_SESSION["operations"][$ID_operation]=array();
}
if ($_SESSION["operations"][$ID_operation]["ID_notice"] == "") {
    $_SESSION["operations"][$ID_operation]["ID_notice"]=$ID_notice;
}
$erreurs="";

// liste des masques
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_masques"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$liste_masques=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

$actions_fin=str_replace ('"', '\"', $json->encode($GLOBALS["affiche_page"]["parametres"]["actions_fin"]));

$GLOBALS["affiche_page"]["parametres"]["ID_operation"]=$ID_operation;
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_js"=>array("liste_masques"=>$liste_masques, "actions_fin"=>$actions_fin ,"erreurs"=>$erreurs)));
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>

 