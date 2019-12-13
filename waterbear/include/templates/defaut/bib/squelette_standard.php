<?PHP
// Ce template est le squelette pour les pages du catalogue

if ($_SESSION["system"]["skin"]=="") {
    $_SESSION["system"]["skin"]=$GLOBALS["affiche_page"]["parametres"]["skin_defaut"];
}

?>
<html>
<head>

<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>

<!-- Titre et favicon -->
<title><?PHP print (get_intitule ("", $GLOBALS["affiche_page"]["parametres"]["titre_page"], array()));?></title>


<link rel="icon" type="image/png" href="<?PHP print($GLOBALS["affiche_page"]["parametres"]["favicon"]) ?>" />

<!-- CSS  -->
<link rel="stylesheet" type="text/css" href="css/defaut.css">
<link rel="stylesheet" type="text/css" href="js/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="js/yui/menu/assets/skins/sam/menu.css"> 
<link rel="stylesheet" type="text/css" href="js/yui/container/assets/skins/sam/container.css">


<?PHP //affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_css"], array()); // DEPRECATED utiliser include_css dans le registre ?>


<!-- JAVASCRIPT --> 
<script language="javascript">
var intitules = new Array();
intitules["erreur_connexion"]="<?PHP print(get_intitule("erreurs/messages_erreur", "erreur_connexion", array()));  ?>";
<?PHP
if ($GLOBALS["affiche_page"]["parametres"]["bool_alerte_poste"]==1) {

    print (utf8_encode("alert (\"Vous n'avez pas indiqué sur quel poste vous vous connectez. Cela peut provoquer des messages d'erreur lors du prêt et des erreurs dans le catalogage.\");"));

}
?>
</script>

<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_js_div"], array()); ?>
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_js_barres_et_menus"], array()); ?>


<?PHP 
if (! isset($param_tmpl_js)) {
    $param_tmpl_js = array(); // paramètres éventuelement passés à main VIA squelette
}

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_javascript"], $param_tmpl_js); ?>

<?PHP affiche_template ("div/include_js_et_css.php", array()); ?>

<!-- Skins -->
<?PHP

if ($_SESSION["system"]["skin"] != "" AND $_SESSION["system"]["skin"] != "defaut") {
    print ('<link rel="stylesheet" type="text/css" href="skins/'.$_SESSION["system"]["skin"].'/css/defaut.css"> \n');
}

?>


</head>
<body class="yui-skin-sam" onload="init()">

<!-- Case à cocher permettant d'ouvrir le lien dans un nouvel onglet -->

<div id="barre_menus0_div" class="barre_icone" >
<table><tr>
<!--<td><img src="IMG/icones/application_double.png" title="Ouvrir le lien dans un nouvel onglet" alt="Ouvrir le lien dans un nouvel onglet"/></td>-->
<td><input type="checkbox" title="Ouvrir le lien dans un nouvel onglet" alt="Ouvrir le lien dans un nouvel onglet" id="bool_open_new" /></td>
<td><img id="ico_waiting" src="IMG/icones/ico_waiting2.gif" title="Traitement en cours" alt="Traitement en cours" style="visibility: hidden;" onclick="affiche_waiting(false);"/></td>
</tr></table>

</div>

<!--  Barres de menus barre1 = menus propres au module (navigation entre les pages), menu2 = fonctions propres à la page en cours  -->

<div id="barre_menus1_div" class="barre_icone" ></div>
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_barre_menus1"], array("id_barre"=>"barre_menus1")); ?>

<div id="barre_menus2_div" class="barre_icone" ></div>
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_barre_menus2"], array("id_barre"=>"barre_menus2")); ?>

<!--  Barre des icones (raccourcis vers des liens des menus 1 et 2)  -->

<div id="barre_icone1_div" class="barre_icone" >
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_barre_icones1"], array("id_barre"=>"barre_icones1")); ?>
</div>
<div id="barre_icone2_div" class="barre_icone" >
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_barre_icones2"], array("id_barre"=>"barre_icones2")); ?>
</div>

<div>
<!--  Barre latérale  -->

<?PHP

//print_r($GLOBALS["affiche_page"]["parametres"]);

$bool_barre_laterale_defaut=$GLOBALS["affiche_page"]["parametres"]["bool_barre_laterale_defaut"];
if ($bool_barre_laterale_defaut == "1") {
    $visible_ouvert="visible";
    $visible_ferme="hidden";
    $classe_main="div_main";
} else {
    $visible_ouvert="hidden";
    $visible_ferme="visible";
    $classe_main="div_main_extended";
}

?>

<div id="div_barre_laterale_ouverte" class="squelette" style="visibility: <?PHP print ($visible_ouvert);  ?> ; background-image: url(<?PHP print ($GLOBALS["affiche_page"]["parametres"]["img_barre_laterale"]);  ?>);">
<?PHP affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_barre_acces"], array()); ?>
</div>

<!--  Barre latérale cachée  --> 

<div id="div_barre_laterale_fermee" class="squelette" style="visibility: <?PHP print ($visible_ferme);  ?> ; background-image: url(<?PHP print ($GLOBALS["affiche_page"]["parametres"]["img_barre_laterale"]);  ?>);">
<br />
<div class="fond_icones">


<!-- affichage des icones de la barre latérale cachée -->

<?PHP

$liste_icones=$GLOBALS["affiche_page"]["parametres"]["icones_barre_laterale"];

foreach ($liste_icones as $icone) {
    $img=$icone["img"];
    $lien=$icone["lien"];
    $aide=$icone["aide"];
    $img=add_skin($img);
    


?>

<img src="<?PHP print ($img);?>" title="<?PHP print ($aide);?>" onclick="menu_action_clic('', '', '<?PHP print ($lien)  ?>')"/> 
<br /><br />

<?PHP
} // fin du foreach


$img_deplie=add_skin("IMG/icones/application_side_expand.png");
print("<img src=\"$img_deplie\" onclick=\"barre_laterale('ouvrir');\"/>");
?>



<!-- FIN de l'affichage des icones de la barre latérale cachée -->

<!-- affichage des icones schedule recherche  -->
<br /><br /><br /><br />
<?PHP

// Icones schedule_recherches
$schedule_recherches=$GLOBALS["affiche_page"]["parametres"]["schedule_recherches"];
foreach ($schedule_recherches["recherches"] as $recherche) {
    $lien=$recherche["lien"];
    $code=$recherche["code"];
    print ("<img id=\"$code\" onclick=\"menu_action_clic('', '', '$lien')\" src=\"\" title='' class='img_schedule'/><br/><br/> \n");
}

$img_update_schedule=add_skin("IMG/icones/arrow_refresh.png");
 print ("<img  onclick=\"update_schedule()\" src=\"$img_update_schedule\" title='Rafraichir les icones' /><br/><br/> \n");

?>



</div>
</div>

<!--  Coeur de la page -->
<!--<div class="squelette" style="top:52 ; left:200 ; width:100% ; height:100%">-->
<div class="<?PHP print ($classe_main);  ?>" id="div_main"  >
<?PHP 
if (! isset($param_tmpl_main)) {
    $param_tmpl_main = array(); // paramètres éventuelement passés à main VIA squelette
}
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_main"], $param_tmpl_main);?>
</div>

</div>
<div id="div_menu_contextuel"></div>



</body>
</html>