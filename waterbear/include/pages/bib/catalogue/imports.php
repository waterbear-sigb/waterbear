<?PHP
// Si on n'a pas encore défini d'opération, on le fait
if ($ID_operation=="") {
    $ID_operation=get_id_operation();
    $_SESSION["operations"][$ID_operation]=array();
}
$GLOBALS["affiche_page"]["parametres"]["ID_operation"]=$ID_operation;


include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>
