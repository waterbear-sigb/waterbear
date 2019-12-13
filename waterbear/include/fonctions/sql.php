<?PHP

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_query($parametres) {

$sql=$parametres["sql"];
$contexte=$parametres["contexte"];
$time_start=microtime(true);
$resultat=mysql_query($sql);
$time_stop=microtime(true);
$duree=$time_stop-$time_start;
if ($resultat) {
  	tvs_log("sql_querys", "REQUETE", array($sql, $contexte, $duree));
	return($resultat);
} else {
  	tvs_log_txt("sql_errors", array($sql, $contexte, mysql_errno(), mysql_error()));
  	throw new tvs_exception("SQL/div", array("contexte"=>$contexte));
}

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_as_array ($parametres) {
  	$sql=$parametres["sql"];
	$contexte=$parametres["contexte"];
  	$retour=array();
  	$resultat=sql_query(array("sql"=>$sql, "contexte"=>$contexte));
  	while ($ligne=mysql_fetch_assoc($resultat)) {
	    array_push ($retour, $ligne);
	}
	return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_as_value ($parametres) {
  	$sql=$parametres["sql"];
	$contexte=$parametres["contexte"];
  	$retour="";
  	$resultat=sql_query(array("sql"=>$sql, "contexte"=>$contexte));
  	if ($ligne=mysql_fetch_array($resultat)) {
  	  	$retour=$ligne[0];
  	}
  	return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Sécurise des arguments pour inclure dans une requête SQL
// tient compte du paramètre 'magic_quotes' du PHP.INI
// Récursif pour les array
function secure_sql ($asecuriser) {
	if (is_array($asecuriser)) {
	  	$retour=array();
	  	foreach ($asecuriser as $idx=>$valeur) {
		    $retour[$idx]=secure_sql($valeur);
		}
		return($retour);
	}
	
	// Si ce n'est pas une array...
	// Si magic_quotes = ON, on enlèves les slashes
	if (get_magic_quotes_gpc()==1) {
	  	$asecuriser=stripslashes($asecuriser);
	}
	$asecuriser=mysql_real_escape_string($asecuriser);
	return($asecuriser);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_insert_id () {
  	return (mysql_insert_id ());
}

?>
