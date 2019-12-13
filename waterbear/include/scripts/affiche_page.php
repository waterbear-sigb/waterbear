<?PHP

if ($GLOBALS["affiche_page"]["page"] != "") {
	try {affiche_page();}
	catch (tvs_exception $e) {
	  	affiche_template ("erreurs/erreur_page.php", array("page"=>$GLOBALS["affiche_page"]["page"], "message"=>get_exception($e->get_infos())));
	  	die();
	}

} else {
  	$GLOBALS["affiche_page"]["include"]="";
}

if ($GLOBALS["affiche_page"]["include"] != "") {
  	include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."pages/".$GLOBALS["affiche_page"]["include"]);
}
















?>