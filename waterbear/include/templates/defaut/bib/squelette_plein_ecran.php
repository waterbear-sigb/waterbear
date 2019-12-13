<?PHP
// Ce template est le squelette pour une page plein écran

?>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>

<!-- Titre et favicon -->
<title><?PHP print (get_intitule ("", $GLOBALS["affiche_page"]["parametres"]["titre_page"], array()));?></title>
<?PHP dbg_log ("get_intitule(".$GLOBALS["affiche_page"]["parametres"]["titre_page"].")"); ?>

<link rel="icon" type="image/png" href="<?PHP print($GLOBALS["affiche_page"]["parametres"]["favicon"]) ?>" />

<!-- CSS  -->
<link rel="stylesheet" type="text/css" href="css/defaut.css">
<link rel="stylesheet" type="text/css" href="js/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="js/yui/menu/assets/skins/sam/menu.css"> 
<link rel="stylesheet" type="text/css" href="js/yui/container/assets/skins/sam/container.css">


<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_css"], array()); ?>


<!-- JAVASCRIPT --> 
<script language="javascript">
var intitules = new Array();
intitules["erreur_connexion"]="<?PHP print(get_intitule("erreurs/messages_erreur", "erreur_connexion", array()));  ?>";

</script>

<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_js_div"], array()); ?>
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_js_barres_et_menus"], array()); ?>


<?PHP 
if (! isset($param_tmpl_js)) {
    $param_tmpl_js = array(); // paramètres éventuelement passés à main VIA squelette
}

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_javascript"], $param_tmpl_js); ?>

<?PHP affiche_template ("div/include_js_et_css.php", array()); ?>

</head>
<body class="yui-skin-sam" onload="init()">

<div class="<?PHP print ($classe_main);  ?>" id="div_main"  >
<?PHP 
if (! isset($param_tmpl_main)) {
    $param_tmpl_main = array(); // paramètres éventuelement passés à main VIA squelette
}
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_main"], $param_tmpl_main);?>
</div>


<div id="div_menu_contextuel"></div>
</body>
</html>