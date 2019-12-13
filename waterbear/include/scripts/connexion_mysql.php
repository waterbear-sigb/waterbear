<?PHP
// Connexion MySQL
$db=mysql_connect($GLOBALS["tvs_global"]["conf"]["ini"]["mysql_adresse_db"],$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_login_db"], $GLOBALS["tvs_global"]["conf"]["ini"]["mysql_mdp_db"]) OR die ("Impossible de se connecter à MySQL<br>");
mysql_select_db ($GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"],$db) OR die ("Impossible de selectionner la DB <br>".$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]);
sql_query(array("sql"=>"SET NAMES 'UTF8'", "connexion_mysql.php"));
$_SESSION["system"]["DB"]=$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"];

?>