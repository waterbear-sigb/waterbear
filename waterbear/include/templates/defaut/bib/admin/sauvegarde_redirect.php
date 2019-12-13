Redirection vers le gestionnaire de sauvegarde...
<script language="javascript">
<?php 
$url=$GLOBALS["affiche_page"]["parametres"]["url"];
$domaine=$_SESSION["metawb"]["site"];
$url.="?domaine=$domaine";
?>
var url="<?php print($url);  ?>";

window.location.href=url;

</script>