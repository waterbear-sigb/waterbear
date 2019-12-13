<?PHP
// paramètres :
//$id_barre => "nom" de la barre dans le registre (noeud _parametres/$id_barre de la page affichant le menu)
$js_struct=""; // le code javascript qui va contenir la définition des menus
$idx_chapeau=0;
$json = new Services_JSON();
$tmp_barre=array(); // array contenant la structure du menu qui sera convertie en JSON



foreach ($GLOBALS["affiche_page"]["parametres"][$id_barre] as $chapeau) { // Pour chaque chapeau !!!!!!!!!!!!!!!!!
	$tmp_chapeau=array();
	//$tmp_chapeau["text"]=$chapeau["titre"];
	$chemin_langue=$chapeau["chemin_langue"];
	$tmp_chapeau["text"]=get_intitule($chemin_langue, $chapeau["titre"], array());
	$tmp_chapeau["submenu"]=array();
	$tmp_chapeau["submenu"]["id"]=$id_barre."_".$idx_chapeau;
	$tmp_chapeau["submenu"]["itemdata"]=array();
	foreach ($chapeau["sections"] as $section) { // Pour chaque section !!!!!!!!!!!!!!!!!
		$tmp_section=array();
		foreach ($section["menus"] as $menu) { // Pour chaque menu !!!!!!!!!!!!!!!!!
			//$tmp_menu=array("text"=>$menu["titre"], "onclick"=>array("fn"=>"menu_action_clic", "obj"=>$menu["url"]));
			$tmp_menu=array("text"=>get_intitule($chemin_langue, $menu["titre"], array()), "onclick"=>array("fn"=>"menu_action_clic", "obj"=>$menu["url"]));
			array_push($tmp_section, $tmp_menu);
		}
		array_push($tmp_chapeau["submenu"]["itemdata"], $tmp_section);
	}
	array_push($tmp_barre, $tmp_chapeau);
	$idx_chapeau++; // à la fin
}

$js_struct=$json->encode($tmp_barre);
$js_struct=ereg_replace("\"menu_action_clic\"", "menu_action_clic", $js_struct); // Pour enlever les guillemets autour du nom de la fonction (rajoutés automatiquement par JSON)

?>


<SCRIPT language="javascript">
var tableau_boutons=new Array();
var tableau_menus=new Array();

YAHOO.util.Event.onDOMReady(function () {
	var aItemData=<?PHP print ($js_struct);  ?>; // la structure du menu
	var <?PHP print ($id_barre);?>_bar1 = new YAHOO.widget.MenuBar("<?PHP print ($id_barre);?>_bar2", {lazyload: true, itemdata: aItemData}); 
	<?PHP print ($id_barre);?>_bar1.render(document.getElementById("<?PHP print ($id_barre);?>_div"));
});


</SCRIPT>
