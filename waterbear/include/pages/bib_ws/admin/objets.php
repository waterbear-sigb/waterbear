<?PHP




// VARIABLES passées en paramètre
$type_objet=$_REQUEST["type_objet"]; // type d'objet
$nom=$_REQUEST["nom"];
$nom_colonne=$_REQUEST["nom_colonne"];
$ancien_nom_colonne=$_REQUEST["ancien_nom_colonne"];
$type_colonne=$_REQUEST["type_colonne"];
$description_colonne=$_REQUEST["description_colonne"];
$type_index=$_REQUEST["type_index"];
$multivaleurs=$_REQUEST["multivaleurs"];


$retour["succes"]=1;
//$retour["liste_objets"]=array();
$gestion_objets=new gestion_objets_db(array("type_objet"=>$type_objet));

if ($operation == "get_liste_objets") {
	$objets=$gestion_objets->get_liste_objets();
	if ( ! $objets) {
	  	$retour["succes"]=0;
	  	$retour["erreur"]=get_intitule("bib_ws/admin/objets", "erreur_recuperer_objets", array());
	} else {
	  	$retour["liste_objets"]=$objets;
	}
} elseif ($operation=="create_objet") {
  	try {$gestion_objets->create_objet();}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}
} elseif ($operation=="delete_objet") {
  	try {$gestion_objets->delete_objet();}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}  
} elseif ($operation=="empty_objet") {
  	try {$gestion_objets->empty_objet();}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}  
} elseif ($operation=="acces_valide_form") {
  	try {$gestion_objets->acces_valide_form(array("nom"=>$nom, "nom_colonne"=>$nom_colonne, "ancien_nom_colonne"=>$ancien_nom_colonne, "type_colonne"=>$type_colonne, "description_colonne"=>$description_colonne, "type_index"=>$type_index, "multivaleurs"=>$multivaleurs));}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}
} elseif ($operation=="acces_delete") {
  	try {$gestion_objets->acces_delete_column ($ancien_nom_colonne);}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}
} elseif ($operation=="tri_valide_form") {
  	try {$gestion_objets->tri_valide_form(array("nom"=>$nom, "nom_colonne"=>$nom_colonne, "ancien_nom_colonne"=>$ancien_nom_colonne, "type_colonne"=>$type_colonne, "description_colonne"=>$description_colonne));}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}
} elseif ($operation=="tri_delete") {
  	try {$gestion_objets->tri_delete_column ($ancien_nom_colonne);}
  	catch (tvs_exception $e) {
	    $retour["succes"]=0;
	  	$retour["erreur"]=get_exception($e->get_infos());
	}
} else {
  	$retour["succes"]=0;
	$retour["erreur"]=get_intitule("erreurs/erreur_operation_non_definie", "message", array("operation"=>$operation));
}

if ($retour["succes"] == 1) {
    metawb_log_objets ($operation, $type_objet, $nom, $nom_colonne, $ancien_nom_colonne, $type_colonne, $description_colonne, $type_index, $multivaleurs);
}

$json = new Services_JSON();
$output = $json->encode($retour);
print($output);



?>