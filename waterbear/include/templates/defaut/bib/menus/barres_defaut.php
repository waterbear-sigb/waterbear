<?PHP
// paramètres :
//$id_barre => "nom" de la barre dans le registre (noeud _parametres/$id_barre de la page affichant le menu)

$nb_col=10; // par défaut
if (isset($GLOBALS["affiche_page"]["parametres"][$id_barre]["nb_col"])) {
	$nb_col=$GLOBALS["affiche_page"]["parametres"][$id_barre]["nb_col"];
}
$affiche_legende=0; // par défaut
if (isset($GLOBALS["affiche_page"]["parametres"][$id_barre]["affiche_legende"])) {
	$affiche_legende=$GLOBALS["affiche_page"]["parametres"][$id_barre]["affiche_legende"];
}

$chemin_langue=$GLOBALS["affiche_page"]["parametres"][$id_barre]["chemin_langue"];

$html_barre="";
$icones_array=array();
foreach ($GLOBALS["affiche_page"]["parametres"][$id_barre]["icones"] as $onsenfout => $icone) {
  	array_push ($icones_array, $icone);
}
for ($i=0 ; $i < $nb_col ; $i++) {
	$legende="";
	if (isset($icones_array[$i])) {
		if ($affiche_legende==1) {
		    //$legende=$icones_array[$i]["legende"];
		    $legende=get_intitule($chemin_langue, $icones_array[$i]["legende"], array());
		}
		$img_url=$icones_array[$i]["img_url"];
        $img_url=add_skin($img_url);
		//$img_alt=$icones_array[$i]["img_alt"];
		$img_alt=get_intitule($chemin_langue, $icones_array[$i]["img_alt"], array());
		$url=$icones_array[$i]["url"];
		$class="class=\"barre_icone_icone\"";
	} else {
	  	$img_url="IMG/icones/blank.png";
		$img_alt="";
		$url="";
		$class="";
	}
    if ($url != "") {
	   $html_barre.="<td $class><table style='margin:auto'><tr><td><img src=\"$img_url\" title=\"$img_alt\" alt=\"$img_alt\" onClick=\"menu_action_clic('', '', '$url')\" /> </td></tr><tr><td>$legende</td></tr></table></td>\n";
    } else {
        $html_barre.="<td $class><table style='margin:auto'><tr><td><img src=\"$img_url\" title=\"$img_alt\" alt=\"$img_alt\"  /> </td></tr><tr><td>$legende</td></tr></table></td>\n";
    }
}

// gestion de la recherche rapide
$html_recherche="";
//if (is_array($GLOBALS["affiche_page"]["parametres"][$id_barre]["recherche_rapide"])) {
if ($GLOBALS["affiche_page"]["parametres"][$id_barre]["recherche_rapide"]==1) {
    $recherche_rapide=$GLOBALS["affiche_page"]["parametres"]["recherche_rapide"];
    //$texte_recherche=get_intitule($chemin_langue, $recherche_rapide["texte"], array());
    $texte_recherche=$recherche_rapide["texte"];
    $ws_url=$recherche_rapide["ws_url"];
    $html_recherche="<td align='right'><form action='#' onsubmit='return recherche_rapide_submit();'><input id='champ_recherche_rapide' ws_url='$ws_url' value=\"$texte_recherche\" class='champ_recherche_rapide_blur' onfocus='recherche_rapide_focus();' onblur='recherche_rapide_blur();'/></form></td>";
}


// fin barre de la recherche rapide

$html_barre="<table width='100%' ><tr>$html_barre $html_recherche</tr></table>\n";

print ($html_barre);










?>