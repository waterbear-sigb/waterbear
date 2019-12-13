<?php

$autoplugin=$_REQUEST["autoplugin"];
$chemin_liste="";
$element="";
if ($autoplugin != "") {
    $chaine="profiles/defaut/plugins/plugins/".$autoplugin."/parametres/!!liste_choix/parametres/nom_liste";
    try {
    $chemin_liste=get_registre($chaine);
    } catch (tvs_exception $e) {
	  	die ("impossible de récupérer $chaine : erreur $e");
	}
    $tmp=explode("/", $chemin_liste);
    $element=array_pop($tmp);
    $chemin_liste="profiles/defaut/langues/listes/".$chemin_liste;
}




affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("element"=>$element, "chemin_liste"=>$chemin_liste)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");


?>