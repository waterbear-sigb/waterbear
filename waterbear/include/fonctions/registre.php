<?PHP
// L'essentiel de ces fonctions est géré par la classe tvs_registre, mais on utilise de simples fonctions 
// comme point d'accès pour éviter d'avoir besoin de faire un "new registre()" à chaque fois
//


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne une valeur du registre (variable ou array) à partir d'un chemin
// Essaye d'abord de récupérer l'info dans $SESSION puis dans la DB
function get_registre ($chaine) {
  	$registre=new tvs_registre();
  	$retour=$registre->registre_get_branche($chaine, "");
  	return($retour);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Retourne les enfants d'un noeud à partir de son chemin (pas toute la branche)
// Essaye d'abord de récupérer l'info dans $SESSION puis dans la DB
function get_enfants ($chaine) {
  	$registre=new tvs_registre();
  	$retour=$registre->registre_get_enfants($chaine);
  	return($retour);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// TODO : récupère une branche dans un profil donné
function p_get_registre ($chaine) {
	$profile="defaut";
	$chaine="profiles/".$profile."/".$chaine;
	return(get_registre ($chaine));
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// TODO : récupère les enfants dans un profil donné
function p_get_enfants ($chaine) {
	$profile="defaut";
	$chaine="profiles/".$profile."/".$chaine;
	return(get_enfants ($chaine));
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Crée une valeur dans le registre (création ou MAJ)
// Crée l'arborescence si nécessaire
// On gère les erreurs dès ce niveau pour que la focnction reste simple à utiliser
function set_registre ($chemin, $valeur, $description) {
  	$registre=new tvs_registre();
  	try {
	    $registre->create_node_chemin (array(), $chemin, $valeur, $description);
	} catch (tvs_exception $e) {
	  	tvs_log_txt("gen_errors", array("Erreur dans set_registre()", "chemin : $chemin", "valeur : $valeur", "description : $description", "erreur : $e"));
	  	return (false);
	}
    //$test=$registre->metawb_is_node_exportable($chemin);
    //if ($test == "mwb_export") {
    //    metawb_log_registre ("set_registre", $chemin, $valeur, $description);
    //}
	return (true);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprime une branche du registre (et maj $_SESSION)
function unset_registre ($chemin) {
  	$registre=new tvs_registre();
  	$noeud=$registre->get_node_by_chemin($chemin);
  	if ($noeud != false) {
	    try {$registre->delete_tree($noeud["ID"]);}
	    catch (tvs_exception $e) {
		  	tvs_log_txt("gen_errors", array("Erreur dans unset_registre()", "chemin : $chemin", "erreur : $e"));
		  	return (false);
		}
		return (true);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 
function get_compteur ($nom_compteur) {
    $registre=new tvs_registre();
    $node=$registre->get_node_by_chemin ("system/compteurs/$nom_compteur");
	if ($node === false) {
	    return (false);
	}
	$valeur=$node["valeur"];
	$id=$node["ID"];
	$valeur++;
	$registre->niv2_update_node (array("ID"=>$id, "valeur"=>$valeur));
    return ($valeur);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 
function metawb_log_registre ($type, $chemin, $nom, $valeur, $description) {
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["mwb_bool_master"] != 1) {
        return ("");
    }
    $now=time();
    $chemin=secure_sql($chemin);
    $nom=secure_sql($nom);
    $valeur=secure_sql($valeur);
    $description=secure_sql($description);
    $sql="INSERT INTO metawb_log_registre values ('$now', '$type', '$chemin', '$nom', '$valeur', '$description')";
    sql_query(array("sql"=>$sql, "contexte"=>"registre::metawb_log_registre()"));
    
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// cette fonction supprime tous les noeus "noeud vide" qui peuvent survenir si on passe 2 fois une mise à jour
function nettoie_registre () {
    $sql="delete from tvs_registre where chemin like '%nouveau noeud%'";
    sql_query(array("sql"=>$sql, "contexte"=>"registre::nettoie_registre()"));
    
}

?>