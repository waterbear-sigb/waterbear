<?PHP
// Connexion MySQL
$db=mysql_connect($GLOBALS["tvs_global"]["conf"]["ini"]["metawb_adresse_db"],$GLOBALS["tvs_global"]["conf"]["ini"]["metawb_login_db"], $GLOBALS["tvs_global"]["conf"]["ini"]["metawb_mdp_db"]) OR die ("Impossible de se connecter à metawb<br>");;
mysql_select_db ($GLOBALS["tvs_global"]["conf"]["ini"]["metawb_nom_db"],$db) OR die ("Impossible de sélectionner la DB <br>");;
sql_query(array("sql"=>"SET NAMES 'UTF8'", "connexion_mysql.php"));

?>