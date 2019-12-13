<?PHP
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche un template
function affiche_template ($chemin, $variables) {
  	if ($chemin == "") {
	    return(false);
	}
  	$profile="defaut"; // TODO possibilité de récupérer le profile de template
  	$_chemin=$GLOBALS["tvs_global"]["conf"]["ini"]["template_path"]."/$profile/$chemin";
  	extract($variables);
	//print ("template : $_chemin <br>");
  	include ($_chemin);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche une liste d'options tirées du registre dans un template
// Les listes sont généralement fournies dans la branche _parametres du registre
function tmpl_affiche_liste ($liste_array) {
  	if (! is_array($liste_array["liste"])) {
		return(0);
	}
	if (! is_array($liste_array["options"])) {
		$chemin_langue="";
		$chemin_langue_intitule="0";
		$chemin_langue_valeur="0";
		$selected="";
	} else {
	  	$chemin_langue=$liste_array["options"]["chemin_langue"];
		$cherche_langue_intitule=$liste_array["options"]["cherche_langue_intitule"];
		$cherche_langue_valeur=$liste_array["options"]["cherche_langue_valeur"];
		$selected=$liste_array["options"]["selected"];
	}
	foreach ($liste_array["liste"] as $idx => $option) {
	  	$html_selected="";
	  	$intitule=$option["intitule"];
		if ($cherche_langue_intitule=="1") {
		  	$intitule=get_intitule($chemin_langue, $intitule, array());
		}
		$valeur=$option["valeur"];
		if ($cherche_langue_valeur=="1") {
		  	$valeur=get_intitule($chemin_langue, $valeur, array());
		}
		if ($idx == $selected) {
		  	$html_selected=" selected ";
		}
		print ("<option value=\"$valeur\" $html_selected >$intitule</option>\n");
	}
}



?>