<?PHP
// variables passées en paramètre
$nom=$_REQUEST["nom"]; // nom du noeud
$ID_parent=$_REQUEST["ID_parent"]; // parent du noeud (numeric)
$description=$_REQUEST["description"]; // description du noeud
$ID_noeud=$_REQUEST["ID_noeud"]; // ID du noeud (numeric)
$valeur=$_REQUEST["valeur"]; // valeur du noeud
$modele=$_REQUEST["modele"]; // modèle (pour copier-coller)
$destination=$_REQUEST["destination"]; // destination (pour copier-coller)
$copie_contenu=$_REQUEST["copie_contenu"]; // pour copier-coller : s'il faut coller tout le noeud ou seulement son contenu (0|1)
$chemin=$_REQUEST["chemin"]; // chemin du noeud
$chemin_2_ID_noeud=$_REQUEST["chemin_2_ID_noeud"]; // quand on fournit un chemin au lieu de ID_noeud
$chemin_2_ID_parent=$_REQUEST["chemin_2_ID_parent"]; // quand on fournit un chemin au lieu de ID_parent

// Création de l'objet registre
$registre=new tvs_registre();

// Si ID non fournis, trouvés à partir des chemins (utilisé pour mwb : application des paramètres via WS)
if ($chemin_2_ID_noeud != "") {
    $ID_noeud=$registre->get_ID_by_chemin($chemin_2_ID_noeud);
}

if ($chemin_2_ID_parent != "") {
    $ID_parent=$registre->get_ID_by_chemin($chemin_2_ID_parent);
}



//$retour=array();
if ($operation == "creer_noeud_vierge") {
  	try {
	    $retour["succes"]=$registre->niv2_create_node (array("nom"=>"nouveau noeud", "parent"=>$ID_parent, "description"=>""));
	} catch (tvs_exception $e) {
	  	$retour["succes"]=0;
	  	$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
	}
} elseif ($operation == "creer_noeud") {
    try {
	    $retour["succes"]=$registre->niv2_create_node (array("nom"=>$nom, "parent"=>$ID_parent, "description"=>$description, "valeur"=>$valeur));
	} catch (tvs_exception $e) {
	  	$retour["succes"]=0;
	  	$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
	}
    
}elseif ($operation == "supprimer_noeud") {
  	try {
        $tmp=$registre->get_node_by_ID($ID_noeud);
        $chemin=$tmp["chemin"];
        $test=$registre->metawb_is_node_exportable($tmp["chemin"]);
        $retour["succes"]=$registre->delete_tree($ID_noeud);
        if ($test == "mwb_export") {
            metawb_log_registre ("supprimer_noeud", $chemin, "", "", "");
        }
  	} catch (tvs_exception $e) {
  	  	$retour["succes"]=0;
	    $retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
	}
} elseif ($operation == "update_noeud") {
  	try {
		$retour["succes"]=$registre->niv2_update_node (array("ID"=>$ID_noeud, "nom"=>$nom,  "description"=>$description, "valeur"=>$valeur, "bool_force_vide"=>1));
	} catch (tvs_exception $e) {
	  	$retour["succes"]=0;
	  	$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
	}

} elseif ($operation == "copy_branche") {
  	try {
		$retour["succes"]=$registre->copy_branche (array("modele_str"=>$modele, "destination_str"=>$destination, "copie_contenu"=>$copie_contenu));
        $test=$registre->metawb_is_node_exportable($destination);
        if ($test == "mwb_export") {
            metawb_log_registre ("copy_branche", $modele, "", $destination, $copie_contenu);
        }
	} catch (tvs_exception $e) {
	  	$retour["succes"]=0;
	  	$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
	}
} elseif ($operation == "exporter_branche_visuel") {
		$retour=$registre->exporter_branche_visuel ($chemin, "", "");
		print ($retour);
		die("");
} elseif ($operation == "exporter_branche_maj") {
        $retour=$registre->exporter_branche_maj ($chemin, "", 1);
        $output = $json->encode($retour);
        $output=str_replace ('\\', '\\\\', $output);
        $output=str_replace ('\'', '\\\'', $output);    
        print($output); 
        die("");
} elseif ($operation == "exporter_branche_compilation") {
        ini_set("max_execution_time", "0");
        $_SESSION["registre"] = array();
        if ($_REQUEST["compiler_tout"]==1) {
            $retour=$registre->compilation ("profiles/defaut");
        } elseif ($_REQUEST["compiler_tout"]==0) {
            $retour=$registre->compilation_incrementielle ();
        }
} elseif ($operation == "afficher_branche") { // récupération d'un arbre
	try {
		$retour=$registre->get_enfants($ID_noeud);
	} catch (tvs_exception $e) {
		$retour["succes"]=0;
		$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
	}
 } elseif ($operation == "reset_registre") { // récupération d'un arbre
	$_SESSION["registre"] = array();
    $retour["succes"]=1;
} elseif ($operation == "recherche_noeud") {
    try {
        $retour["resultat"]=$registre->get_nodes_by_value($nom, $valeur);
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
		$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
    }
} elseif ($operation == "ddbl_registre") {
    try {
        $retour["resultat"]=$registre->ddbl_registre(array("bool_test"=>$_REQUEST["bool_test"]));
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
		$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
    }
    
}else {
  	$retour["succes"]=0;
	$retour["erreur"]=get_intitule("erreurs/erreur_operation_non_definie", "message", array("operation"=>$operation));
}

//print ("\n\n\n");

//$json = new Services_JSON();
$output = $json->encode($retour);
print($output);

?>