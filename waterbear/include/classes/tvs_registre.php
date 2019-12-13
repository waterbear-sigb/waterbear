<?PHP

// CREATE TABLE tvs_registre (ID INT PRIMARY KEY AUTO_INCREMENT, parent INT, nom VARCHAR(250), description TEXT, valeur TEXT, chemin TEXT, permissions TEXT)

// CREATE TABLE historique_registre (ID INT PRIMARY KEY AUTO_INCREMENT, timestamp INT, type_operation VARCHAR(50), chemin TEXT)
// CREATE INDEX idx_timestamp ON historique_registre (timestamp)

// CREATE TABLE metawb_log_registre (timestamp INT, type VARCHAR(50), chemin TEXT, nom VARCHAR(250), valeur TEXT, description TEXT);

class tvs_registre {

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function tvs_registre() {
  
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_node_by_ID ($ID) {
  	$noeud=sql_as_array(array("sql"=>"select * from tvs_registre where ID = $ID", "contexte"=>"tvs_registre::get_node_by_ID()"));
	if (!isset($noeud[0])) {
	    return (false);
	}
	 return($noeud[0]);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function analyse_chemin ($chemin) {
  	$retour=array();
  	$tmp=explode("/",$chemin);
  	return($tmp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// transforme un tableau contenant les étapes d'un chemin
// en chaine du type "$_SESSION["registre"]["noeud1"]["noeud2"]" pour faire un eval
function analyse_chemin_str ($tableau) {
  	$retour='$_SESSION["registre"]';
  	foreach ($tableau as $noeud) {
	    $retour.="[\"$noeud\"]";
	}
	return($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne un noeud par l'ID de son parent et par son nom
function get_node_by_nom ($nom, $ID_parent) {
  	if ($ID_parent =="") {
	    $ID_parent=0;
	}
  	$noeud=sql_as_array(array("sql"=>"select * from tvs_registre where parent=$ID_parent AND nom='$nom'", "contexte"=>"tvs_registre::get_node_by_nom()"));
	if (!isset($noeud[0])) {
	    return (false);
	}
	return($noeud[0]);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne un noeud par son chemin (ex : toto/tutu/titi)

function get_node_by_chemin ($chemin) {
	$noeud=sql_as_array(array("sql"=>"select * from tvs_registre where chemin = \"$chemin\"", "contexte"=>"tvs_registre::get_node_by_chemin()"));
	if (!isset($noeud[0])) {
	    return (false);
	}
	 return($noeud[0]);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne ID par son chemin (ex : toto/tutu/titi)

function get_ID_by_chemin ($chemin) {
    $noeud=$this->get_node_by_chemin($chemin);
    if ($noeud==false) {
        return (false);
    }
    $ID=$noeud["ID"];
    return ($ID);
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Retourne des noeuds ayant une certaine valeur (et éventuellement dont le nom est xxx)
function get_nodes_by_value ($nom, $valeur) {
    if ($nom=="" AND $valeur=="") {
        return("");
    }
    $sql_nom="";
    $sql_valeur="";
    if ($nom != "") {
        $nom=secure_sql($nom);
        $sql_nom=" nom like '$nom' ";
    }
    if ($valeur != "") {
        $valeur=secure_sql($valeur);
        $sql_valeur="valeur like '$valeur' ";
        if ($sql_nom != "") {
            $sql_valeur=" AND $sql_valeur";
        }
    }
    $sql="select * from tvs_registre where $sql_nom $sql_valeur order by chemin";
    $nodes=sql_as_array(array("sql"=>$sql, "contexte"=>"tvs_registre::get_node_by_value()"));
    return ($nodes);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne tous les enfants d'un noeud (non récursif !)
function get_enfants ($ID) {
  	$enfants=sql_as_array(array("sql"=>"select * from tvs_registre where parent = $ID ORDER BY nom", "contexte"=>"tvs_registre::get_enfants()"));
	return($enfants);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne le nombre d'enfants d'un noeud (à partir d'un ID de noeud parent)
function get_nb_enfants ($ID) {
  	$noeud=sql_as_value(array("sql"=>"select count(*) from tvs_registre where parent = $ID", "contexte"=>"tvs_registre::get_nb_enfants()"));
  	return($noeud);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne le chemin d'un noeud à partir de son nom et de l'ID de son parent
function get_chemin_node ($nom, $ID_parent) {
  	if ($ID_parent==0) {
	    return($nom);
	} else {
	  	$noeud_parent=sql_as_array(array("sql"=>"select * from tvs_registre where ID = $ID_parent", "contexte"=>"tvs_registre::get_chemin_noeud()"));
	  	$chemin_parent=$this->get_chemin_node ($noeud_parent[0]["nom"], $noeud_parent[0]["parent"]);
	  	$chemin=$chemin_parent."/".$nom;
	  	return($chemin);
	}
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ les chemins de tous les enfants d'un noeud
function maj_chemins_enfants_node ($ID, $chemin) {
  	$enfants=$this->get_enfants ($ID);
  	foreach ($enfants as $enfant) {
  	  	$nom=$enfant["nom"];
  	  	$ID_enfant=$enfant["ID"];
  	  	$chemin_enfant=$chemin."/".$nom;
	    sql_query(array("sql"=>"UPDATE tvs_registre SET chemin='$chemin_enfant' where ID=$ID_enfant", "contexte"=>"tvs_registre::maj_chemins_enfants_node()"));
	    $now=time();
        sql_query (array("sql"=>"INSERT INTO historique_registre values ('', $now, 'update', '$chemin_enfant')", "contexte"=>"tvs_registre::maj_chemins_enfants_node"));
        $this->maj_chemins_enfants_node ($ID_enfant, $chemin_enfant);
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// vérifie qu'il n'y a pas de "/" dans le nom
function is_probleme_nom ($nom) {
  	if (strpos($nom, "/")===false) {
	    return (false);
	} elseif ($nom=="") {
	  	return (false);
	}else {
	  	return (true);
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// FONCTIONS DE BAS NIVEAU POUR CREER / MAJ / SUPPRIMER NOEUD
//// PAS DE DDBL, GESTION D'UN SEUL NOEUD...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Crée un noeud (à partir de l'ID du parent et d'un nom)
function create_node ($parametres) {
  	$parent=0;
  	$description="";
  	$valeur="";
  	$permissions="";
  	$parametres=secure_sql ($parametres);
  	extract($parametres);
  	$chemin=$this->get_chemin_node ($nom, $parent);
  	sql_query(array("sql"=>"INSERT INTO tvs_registre values ('', $parent, '$nom', '$description', '$valeur', '$chemin', '$permissions')", "contexte"=>"tvs_registre::create_node()"));
    $insert_id=sql_insert_id();
    $now=time();
    sql_query (array("sql"=>"INSERT INTO historique_registre values ('', $now, 'create', '$chemin')", "contexte"=>"tvs_registre::create_node()"));
	return($insert_id);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Modifie un noeud (à partir d'un ID)
// ATTENTION : nécessite de refournir TOUS les champs (même ceux qui ne sont pas modifiés) sauf parent
function update_node ($parametres) {
  	$parametres=secure_sql ($parametres);
  	extract($parametres);
  	sql_query(array("sql"=>"UPDATE tvs_registre SET nom='$nom', description='$description', valeur='$valeur', chemin='$chemin', permissions='$permissions' where ID=$ID", "contexte"=>"tvs_registre::update_node()"));
    $now=time();
    sql_query (array("sql"=>"INSERT INTO historique_registre values ('', $now, 'update', '$chemin')", "contexte"=>"tvs_registre::update_node()"));
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprime un noeud (à partir d'un ID)
// ATTENTION : Supprime UN SEUL noeud (pas son arborescence)
function delete_node ($ID) {
    $node=$this->get_node_by_ID($ID);
    $chemin=$node["chemin"];
  	sql_query(array("sql"=>"delete from tvs_registre where ID=$ID", "contexte"=>"tvs_registre::delete_node()"));
    $now=time();
    sql_query (array("sql"=>"INSERT INTO historique_registre values ('', $now, 'delete', '$chemin')", "contexte"=>"tvs_registre::delete_node()"));
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// FONCTIONS DE NIVEAU 2 
//// GERE L'ARBORESCENCE
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Tableaux retournés
// [0]["noeud"]=>array("ID", "parent",...)
//    ["enfants"]=>array() // liste de noeuds
function get_tree_by_ID ($ID) {
  	$retour=array();
  	$retour["noeud"]=array();
  	$retour["enfants"]=array();
  	$retour["noeud"]=$this->get_node_by_ID($ID);
  	$tmp=$this->get_enfants($ID);
  	foreach ($tmp as $enfant) {
  	  	$ID_enfant=$enfant["ID"];
	    $tmp2=$this->get_tree_by_ID($ID_enfant);
	    array_push($retour["enfants"], $tmp2);
	}
	return ($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// supprime toute une arborescence à partir d'un noeud
// ATTENTION on ne fait pas le log metawb ici car c'est une fonction récursive
function delete_tree ($ID) {
  	$tree=$this->get_enfants($ID);
  	$this->delete_node($ID);
  	foreach ($tree as $enfant) {
	    $ID_enfant=$enfant["ID"];
	    $this->delete_tree($ID_enfant);
	}
	return(1);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ noeud en récupérant les anciennes valeurs et en modifiant ce qui est nécessaire (nécessite un ID)
// si [bool_force_vide]==1 alors on maj les valeurs même si elles sont vides (ce qui permet de supprimer une valeur dans le registre)
// si [bool_force_vide_valeur]==1 alors on maj les valeurs même si elles sont vides uniquement pour valeur, pas pour description
// MAJ le chemin, dédoublonne...

function niv2_update_node ($parametres) {
  	$retour=1;
  	$noeud=$this->get_node_by_ID($parametres["ID"]);
    $chemin_old=$noeud["chemin"]; // pour metawb_log
  	
  	// Si maj du nom...
  	if ($parametres["nom"] != "" AND $parametres["nom"] != $noeud["nom"]) {
  	  	$parametres["nom"]=trim($parametres["nom"]);
  	  	// On vérifie que le nom est correct
  	  	if ($this->is_probleme_nom($parametres["nom"])) { 
	    	$retour=0;
  			throw new tvs_exception ("registre/nom_incorrect", array("nom"=>$parametres["nom"]));
  			return ($retour);
		}
		// On vérifie qu'il n'y a pas de doublon de chemin
		if ($this->get_node_by_nom($parametres["nom"], $noeud["parent"]) != false) {
		  	$retour=0;
  			throw new tvs_exception ("registre/doublon", array());
			return ($retour);
		}
		
  	  	$chemin=$this->get_chemin_node($parametres["nom"], $noeud["parent"]);
	    $noeud["chemin"]=$chemin;
	    $this->maj_chemins_enfants_node ($parametres["ID"], $chemin); // on maj les chemins des enfants
	}
	$atester=array("nom", "valeur", "description", "permissions");
	foreach ($atester as $tmp) {
		if ($parametres[$tmp] != "" OR ($parametres["bool_force_vide"]==1 AND $tmp != "nom" ) OR ($parametres["bool_force_vide_valeur"]==1 AND $tmp == "valeur" )) {
		  	$noeud[$tmp]=$parametres[$tmp];
		}
	}
	$this->update_node($noeud);
    $test=$this->metawb_is_node_exportable($noeud["chemin"]);
    if ($test == "mwb_export") {
        metawb_log_registre ("niv2_update_node", $chemin_old, $noeud["nom"], $noeud["valeur"], $noeud["description"]);
    }
	return ($retour);
	
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Crée un noeud en faisant des vérifications...
// vérifie le nom, dédoublonne...

function niv2_create_node ($parametres) {
	$retour=1;
  	$parametres["nom"]=trim($parametres["nom"]);
  	// On vérifie que ID_parent est bien numerique
  	if (!is_numeric($parametres["parent"])) {
	    $retour=0;
  		throw new tvs_exception ("registre/parent_incorrect", array("parent"=>$parametres["nom"]));
  		return ($retour);
	}
  	// On vérifie que le nom est correct
  	if ($this->is_probleme_nom($parametres["nom"])) { 
	   	$retour=0;
  		throw new tvs_exception ("registre/nom_incorrect", array("nom"=>$parametres["nom"]));
  		return ($retour);
	}
	// On vérifie qu'il n'y a pas de doublon de chemin
	if ($this->get_node_by_nom($parametres["nom"], $parametres["parent"]) != false) {
	  	$retour=0;
  		throw new tvs_exception ("registre/doublon", array("chemin"=>$parametres["parent"]."/".$parametres["nom"]));
  		return ($retour);
	}
	$retour=$this->create_node($parametres);
    $tmp=$this->get_node_by_ID($parametres["parent"]);
    $chemin_old=$tmp["chemin"];
    $test=$this->metawb_is_node_exportable($chemin_old);
    if ($test == "mwb_export") {
        metawb_log_registre ("niv2_create_node", $chemin_old, $parametres["nom"], $parametres["valeur"], $parametres["description"]);
    }
	return($retour);
  
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// FONCTIONS DE NIVEAU 3 
//// RECUPERE DES DONNEES EN TENANT COMPTE DE $_SESSION...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne une branche du registre en vérifiant si elle n'est pas déjà dans $_SESSION et en la mettant dans $_SESSION
// le paramètre $noeud est optionnel : contient et ID et valeur du dernier noeud du chemin pour éviter d'avoir à faire 2 fois la recherche (récursivité)


function registre_get_branche ($chemin, $noeud) {
    $test=$this->get_branche_compilation ($chemin, false);
    if ($test !== false) {
        if ($noeud=="") {
            tvs_log("registre_querys", "BRANCHE - INCLUDE*", $chemin);
        } else {
            tvs_log("registre_sub_querys", "BRANCHE - INCLUDE*", $chemin);
        }
        return ($test);
    }
    
    // 1) on récupère le noeud correspondant au chemin dans $_SESSION
    $tmp=get_parametres_by_chemin($_SESSION["registre"], $chemin);
    
    if ($tmp !== false) { // si ce chemin existe dans $_SESSION
        // 2) Si le noeud est complet, on le retourne
        if ($tmp["_COMPLET"]==1) {
            if ($noeud=="") {
                tvs_log("registre_querys", "BRANCHE - SESSION", $chemin);
                return($this->registre_expurge_retour($tmp));
            } else {
                tvs_log("registre_sub_querys", "BRANCHE - SESSION", $chemin);
                return ($tmp);
            }
        }
        
        // 3) Si c'est une valeur, on la retourne
        if (!is_array($tmp)) {
            if ($noeud=="") {
                tvs_log("registre_querys", "BRANCHE - SESSION", $chemin);
            } else {
                tvs_log("registre_sub_querys", "BRANCHE - SESSION", $chemin);
            }
            return($tmp);
        }
    } else {
        $tmp=array();
    }
    
    // 4) On récupère les infos du dernier élément s'ils ne sont pas fournis (c'est à dire pour la 1ere fonction mais pas pour les récursives)
    if ($noeud=="") {
        tvs_log("registre_querys", "BRANCHE - SQL", $chemin);
        tvs_log("registre_querys_sql", "BRANCHE - SQL", $chemin);
		$dernier=$this->get_node_by_chemin($chemin);
	} else {
        tvs_log("registre_sub_querys", "BRANCHE - SQL", $chemin);
        tvs_log("registre_sub_querys_sql", "BRANCHE - SQL", $chemin);
	  	$dernier=$noeud;
	}
	$dernier_ID=$dernier["ID"];
	$dernier_valeur=$dernier["valeur"];
	
	// On vérifie que ce noeud existe bien
	if (! isset($dernier["ID"])) {
	  	throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$chemin));
	}
    
    // 5) On récupère les enfants
    $enfants=$this->get_enfants($dernier_ID);
    
    // 6) Si pas d'enfants, le noeud est une valeur
    if (count($enfants) == 0) {
        if ($noeud=="") {
            $_SESSION["registre"]=maj_valeur_array($_SESSION["registre"], $chemin, $dernier_valeur);
        }
        return ($dernier_valeur);
    }
    
    // 7) Sinon, pour chaque enfant
    foreach ($enfants as $enfant) {
        $branche_enfant=$this->registre_get_branche($chemin."/".$enfant["nom"], $enfant);
        $tmp[$enfant["nom"]]=$branche_enfant;
    }
    
    // 8) on indique que cette branche est complète
    $tmp["_COMPLET"]=1;
    
    // 9) on maj $_SESSION (uniquement fonction de base, ps les récursives)
    //    et on renvoie la branche (expurgée) 
    if ($noeud == "") {
        $_SESSION["registre"]=maj_valeur_array($_SESSION["registre"], $chemin, $tmp);
        return($this->registre_expurge_retour($tmp));
        //return($tmp);
    } else { // sinon, on renvoie la branche NON expurgée
        return ($tmp);
    }
    
   
    
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Récupère juste les enfants d'un noeud (pas toute la branche)...
/// en tenant compte de $_SESSION
// valeur de [_COMPLET] : 1 -> la branche est complète
//                        2 -> seuls les enfants sont complets

function registre_get_enfants ($chemin) {
    $test=$this->get_branche_compilation ($chemin, false);
    if ($test !== false) {
        if ($noeud=="") {
            tvs_log("registre_querys", "ENFANTS - INCLUDE*", $chemin);
        } else {
            tvs_log("registre_sub_querys", "ENFANTS - INCLUDE*", $chemin);
        }
        return ($test);
    }
    
    // 1) on récupère le noeud correspondant au chemin dans $_SESSION
    $tmp=get_parametres_by_chemin($_SESSION["registre"], $chemin);
    
    if ($tmp !== false) { // si ce chemin existe dans $_SESSION
        // 2) Si le noeud (ou les enfants) est complet, on le retourne
        if ($tmp["_COMPLET"]>=1) {
            if ($noeud=="") {
                tvs_log("registre_querys", "ENFANTS - SESSION", $chemin);
            } else {
                tvs_log("registre_sub_querys", "ENFANTS - SESSION", $chemin);
            }
            return($this->registre_expurge_retour($tmp));
        }
        
        // 3) Si c'est une valeur, on la retourne
        if (!is_array($tmp)) {
            if ($noeud=="") {
                tvs_log("registre_querys", "ENFANTS - SESSION", $chemin);
            } else {
                tvs_log("registre_sub_querys", "ENFANTS - SESSION", $chemin);
            }
            return($tmp);
        }
    } else {
        $tmp=array();
    }
    
    if ($noeud=="") {
        tvs_log("registre_querys", "ENFANTS - SQL", $chemin);
    } else {
        tvs_log("registre_sub_querys", "ENFANTS - SQL", $chemin);
    }
    
    // on récupère les enfants
    $dernier=$this->get_node_by_chemin($chemin);
    $enfants=$this->get_enfants($dernier["ID"]);
    
	
	// On vérifie que ce noeud existe bien
	if (! isset($dernier["ID"])) {
	  	throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$chemin));
	}
    
   
    // 6) Si pas d'enfants, le noeud est une valeur
    if (count($enfants) == 0) {
        $_SESSION["registre"]=maj_valeur_array($_SESSION["registre"], $chemin, $dernier["valeur"]);
        return ($dernier["valeur"]);
    }
    
    // 7) Pour chaque enfant...
    //   on ajoute soit la valeur soit une array()
    //   si que des valeurs alors la branche mère est complète
    $bool_complet=1;
    foreach ($enfants as $enfant) {
        if (isset($tmp[$enfant["nom"]])) { // Si l'enfant est déjà dans $_SESSION...
            if ($tmp[$enfant["nom"]]["_COMPLET"]!=1) {
                $bool_complet=0;
            } 
        } else { // Si non présent dans $_SESSION...
            $nb_petits_enfants=$this->get_nb_enfants($enfant["ID"]);
            if ($nb_petits_enfants == 0) { // branche complète (valeur)
                $tmp[$enfant["nom"]]=$enfant["valeur"];
            } else { // branche non complète (array)
                $bool_complet=0;
                $tmp[$enfant["nom"]]=array();
            }
        }
    }
    
    // 8) Si tous les enfants sont complets, la branche est complète
    if ($bool_complet == 1) {
        $tmp["_COMPLET"]=1;
    } else {
        $tmp["_COMPLET"]=2;
    }
    
    // 9) on maj $_SESSION et on renvoie la branche (expurgée) 
    $_SESSION["registre"]=maj_valeur_array($_SESSION["registre"], $chemin, $tmp);
    return($this->registre_expurge_retour($tmp));
    //return($tmp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 

function get_branche_compilation ($chemin, $bool_include) {
    $chemin_array=explode("/", $chemin);
    $tmp=get_parametres_by_chemin($GLOBALS["registre_include"], $chemin);
    
    // Si chemin non défini, on renvoie faux
    if ($tmp === false) {
        //tvs_log("registre_querys", "get_branche_compilation", "NON TROUVE");
        if ($bool_include === false) {
            $a_inclure=$this->get_dico($chemin_array);
            if ($a_inclure == "") {
                return (false);
            }
            include_once($a_inclure);
            $tmp=$this->get_branche_compilation($chemin, true);
            return ($tmp);
        } else {
            return (false);
        }
    }
    
    // si chemin défini et pas array, OK
    if (! is_array($tmp)) {
        //tvs_log("registre_querys", "get_branche_compilation", "VALEUR");
        return ($this->registre_expurge_retour($tmp));
    }
    
    // Si array est [_COMPLET] == 0 on renvoie faux
    if ($tmp["_COMPLET"]==0) {
        if ($bool_include === false) {
            $a_inclure=$this->get_dico($chemin_array);
            if ($a_inclure == "") {
                return (false);
            }
            include_once($a_inclure);
            $tmp=$this->get_branche_compilation($chemin, true);
            return ($tmp);
        } else {
            return (false);
        }
    }
    
    // sinon OK
    return ($this->registre_expurge_retour($tmp));
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction nettoie un tableau de registre de tous les "_COMPLET"
function registre_expurge_retour ($tableau) {
    
    //return ($tableau); // ----> fonction désactivée (pour gagner en performances ??)
  	if (! is_array($tableau)) {
	    return($tableau);
	}
	$retour=array();
	foreach ($tableau as $idx => $element) {
	  	if ($idx !== "_COMPLET") { // ATTENTION ne pas mettre != MAIS !== sinon ça plante quand $idx == 0. Ne me demandez pas pourquoi, je n'y comprends rien !
		    $element=$this->registre_expurge_retour($element);
		    $retour[$idx]=$element;
		} else {
		}
	}
	return($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction crée un nouveau noeud à partir d'un chemin et d'une valeur
// Elle peut créer récursivement son chemin si celui-ci n'existe pas
// si le noeud existe déjà, il est mis à jour
// la fonction est récursive (si le chemin n'est pas trouvé immédiatement. Au début, $depart=array() puis àchaque fois contient plus de noeuds non trouvés
function create_node_chemin ($depart, $chemin, $valeur, $description) {
  	if ($chemin=="") { // si on est au bout
	    $ddbl_noeud=array("ID"=>0);
	} else {
  		$ddbl_noeud=$this->get_node_by_chemin($chemin); // On essaye de récupérer le noeud
  	}
  	if ($ddbl_noeud != false) { // s'il existe, MAJ
  	  	// On crée les noeuds intermédiaires si nécessaire (cas où on utilise la récursivité)
  	  	$parent_ID=$ddbl_noeud["ID"];
		while (count($depart) > 0) {
		  	$element = array_pop($depart);
		  	$parent_ID=$this->niv2_create_node (array("parent"=>$parent_ID, "nom"=>$element));
		}

		// On récupère le dernier noeud et on le maj
		$noeud_maj=$this->get_node_by_ID($parent_ID);
		$noeud_maj["valeur"]=$valeur;
		$noeud_maj["description"]=$description;
        $noeud_maj["bool_force_vide_valeur"]=1;
		$this->niv2_update_node($noeud_maj);
	} else { // sinon, on appelle récursivement la fonction
		$chemin_array=$this->analyse_chemin($chemin);
		array_push($depart, array_pop($chemin_array));
		$chemin=implode("/",$chemin_array);
		$this->create_node_chemin ($depart, $chemin, $valeur, $description);
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction copie une branche à un autre endroit de l'arbre
// Si $copie_contenu on copie le CONTENU de la branche, sinon, on copie toute la branche (avec sa racine)
// parametres : $destination_str et $modele_str : le modele et la destination sous fore de chemin (aa/bb/cc) (premier appel de la fonction)
//              $destination (ID) et $modele (ID) : le modele et la destination  (utilisés ensuite par la récursivité)
//              $copie_contenu : si=1 on ne copie pas le noeud acine du modèle, mais seulement ses enfants (toujours à 0 en récursivité)
function copy_branche ($parametres) {
  	extract($parametres);
  	if (isset($modele_str)) { // si on a fourni des chemins, on récupère les noeuds
  		if (!$modele2=$this->get_node_by_chemin($modele_str)) {
		    throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$modele_str));
		}
  		if (!$destination=$this->get_node_by_chemin($destination_str)) {
		    throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$destination_str));
		}
		$destination=$destination["ID"];
		$modele=$modele2["ID"];
	} else {
	  	$copie_contenu=1; // en récursivité, on ne copie toujours QUE les enfants
	  	
	}
	if ($copie_contenu == 0) {
	  	$enfants=array($modele2);
	} else {
		$enfants=$this->get_enfants($modele);
	}
	foreach ($enfants as $enfant) {
	  	$enfant["parent"]=$destination;
	  	$enfant["chemin"]="";
	  	$ID=$this->niv2_create_node ($enfant);
	  	$this->copy_branche (array("modele"=>$enfant["ID"], "destination"=>$ID));
	}
	return(1);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction exporte une branche en modeaffichage (pour visualiser l'arborescence)
// la f° est appelée la première fois avec un chemin. Les itérations suivantes, c'est le noeud lui-même qui est passé

function exporter_branche_visuel ($chemin, $racine, $marge) {
  	if ($chemin != "") {
		$racine=$this->get_node_by_chemin($chemin);
	}
  	$retour="+ ".$racine["nom"];
  	if ($racine["valeur"] != "") {
	    $retour.="<b> => </b>".$racine["valeur"];
	}
	$retour.="<br>";
	$enfants=$this->get_enfants($racine["ID"]);
	foreach ($enfants as $enfant) {
		$str_enfant=$this->exporter_branche_visuel ("", $enfant, $marge."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		$retour.=$marge.$str_enfant;
	}
	return($retour);  	
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction exporte une branche qui pourra ensuite être importée par la fonction niv2_create_node()
// la f° est appelée la première fois avec un chemin. Les itérations suivantes, c'est le noeud lui-même qui est passé
// si $bool_delete_node==1 on rajoutera une ligne posur supprimer le noeud (avant de le recréer)
// retourne sous forme d'array (à convertir en json pour pouvoir être traité)

function exporter_branche_maj ($chemin, $racine, $bool_delete_node) {
    $majs=array();
    
  	if ($chemin != "") {
		$racine=$this->get_node_by_chemin($chemin);
	}
    $ID=$racine["ID"];
    $nom=$racine["nom"];
    $chemin=$racine["chemin"];
    $valeur=$racine["valeur"];
    $description=$racine["description"];
    
    $chemin_array=$this->analyse_chemin($chemin);
    $onsenfout=array_pop($chemin_array);
    $chemin_parent=implode("/", $chemin_array);
    
    // on supprime le noeud (si bool_delete_node vaut 1)
    if ($bool_delete_node==1) {
        $majs[]=array("type"=>"supprimer_noeud", "chemin"=>$chemin, "nom"=>$nom, "valeur"=>$valeur, "description"=>$description);
    }
    
    // on crée le noeud
    $majs[]=array("type"=>"niv2_create_node", "chemin"=>$chemin_parent, "nom"=>$nom, "valeur"=>$valeur, "description"=>$description);
    
    // traitement des sous-noeuds
	$enfants=$this->get_enfants($ID);
	foreach ($enfants as $enfant) {
		$tmp=$this->exporter_branche_maj ("", $enfant, 0);
        foreach ($tmp as $elem) {
            $majs[]=$elem;
        }
	}
    
	return($majs);  	
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 

function exporter_branche_compilation ($chemin, $infos) {
    $retour="";
    
    $chemin_str='$GLOBALS["registre_include"]';
    $chemin_array=$this->analyse_chemin($chemin);
  	foreach ($chemin_array as $noeud) {
	    $chemin_str.="[\"$noeud\"]";
	}
	
    if ($infos=="") {
        $infos=$this->get_node_by_chemin($chemin);
        $str_noeud='$GLOBALS["registre_include"]';
        foreach ($chemin_array as $noeud) {
            $str_noeud.="[\"$noeud\"]";
            $tmp="if (!isset($str_noeud)) {\n    ".$str_noeud." = array(); \n    ".$str_noeud."['_COMPLET']=0; \n}\n";
            $retour.=$tmp;
        }
    }
    $ID=$infos["ID"];
    $valeur=$infos["valeur"];
    
    $enfants=$this->get_enfants($ID);
    
    if (count($enfants)==0) {
        $valeur=str_replace('\\', '\\\\', $valeur);
        $valeur=str_replace('"', '\"', $valeur);
        $retour.=$chemin_str." = \"".$valeur."\";\n"; // !!!! ATTENTION gérer " et \
        return ($retour);
    }
    $retour.=$chemin_str." = array();\n";
    $retour.=$chemin_str."['_COMPLET']=1;\n";
    foreach ($enfants as $enfant) {
        $nom_enfant=$enfant["nom"];
        $tmp=$this->exporter_branche_compilation($chemin."/".$nom_enfant, $enfant);
        $retour.=$tmp;
    }
    return ($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// On utilise $ID ou $chemin

function get_branches_a_compiler ($chemin, $ID) {
print ("get_branches_a_compiler : $chemin - $ID");
    $retour=array();
    if ($ID=="") {
        $tmp=$this->get_node_by_chemin($chemin);
        $ID=$tmp["ID"];
    }
    $enfants=$this->get_enfants($ID);
    foreach ($enfants as $enfant) {
        $nom=$enfant["nom"];
        $ID_enfant=$enfant["ID"];
        $chemin_enfant=$enfant["chemin"];
        if ($nom == "_compile") {
            array_push ($retour, $chemin);
        } else {
            $tmp_retour=$this->get_branches_a_compiler($chemin_enfant, $ID_enfant);
            foreach ($tmp_retour as $a_compiler) {
                array_push ($retour, $a_compiler);
            }
        }
    }
    return ($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function compilation ($chemin) {
print ("compilation() : $chemin ");
    $dir_compilation=$GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"];
    $a_compiler=$this->get_branches_a_compiler($chemin, "");
    $nb=count($a_compiler);
    $idx=0;
    foreach ($a_compiler as $element) {
        $idx++;
        print ("$idx / $nb : $element <br>\n");
        flush();
        $script=$this->exporter_branche_compilation($element, "");
        $tmp="<?PHP \n\n".$script."\n\n ?>";
        $nom=str_replace("/", "_", $element);
        $nom.=".php";
        $fichier=$dir_compilation."/".$nom;
        $file=fopen($fichier, "w");
        fwrite($file, $tmp);
        fclose($file);
        $GLOBALS["dico_registre_include"][$element]=$fichier;
    }
    $this->genere_dictionnaire();
    set_registre ("system/timestamps/compilation", time(), "timestamp de la derniere compilation");
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function compilation_incrementielle () {
    $dir_compilation=$GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"];
    $a_compiler=array();
    $a_supprimer=array();
    $derniere=get_registre("system/timestamps/compilation");
    $sql="select * from historique_registre where timestamp > $derniere order by ID";
    $liste=sql_as_array(array("sql"=>$sql, "contexte"=>"tvs_registre::compilation_incrementielle()"));
    foreach ($liste as $maj) {
        $chemin=$maj["chemin"];
        $type_operation=$maj["type_operation"];
        print ("$type_operation $chemin <br> \n");
        flush();
        $tmp=explode("/", $chemin);
        $nom=array_pop($tmp);
        $chemin2=implode("/", $tmp);
        if ($nom == "_compile") {
            if ($type_operation == "delete") {
                array_push ($a_supprimer, $chemin2);
                print ("---> $chemin2 (supprimer) <br> \n");
                flush();
            } else {
                array_push ($a_compiler, $chemin2);
                print ("---> $chemin2 (compiler) <br> \n");
                flush();
            }
        } else {
            $parents_a_compiler=$this->get_parents_a_compiler($chemin2);
            foreach ($parents_a_compiler as $toto) {
                array_push ($a_compiler, $toto);
                print ("---> $toto (compiler) <br> \n");
                flush();
            }
        }
    }
    $a_compiler=array_unique($a_compiler);
    $a_supprimer=array_unique($a_supprimer);
    
    print ("<b>SUPPRESSIONS</b><br>");    
    foreach ($a_supprimer as $element) {
        print ("$element <br> \n");
        unset($GLOBALS["dico_registre_include"][$element]);
        $nom=str_replace("/", "_", $element);
        $nom.=".php";
        $fichier=$dir_compilation."/".$nom;
        unlink($fichier);
    }
    
    $nb=count($a_compiler);
    $idx=0;
    print ("<b>COMPILATIONS</b><br>"); 
    foreach ($a_compiler as $element) {
        $idx++;
        print ("$idx / $nb : $element <br>\n");
        flush();
        $script=$this->exporter_branche_compilation($element, "");
        $tmp="<?PHP \n\n".$script."\n\n ?>";
        $nom=str_replace("/", "_", $element);
        $nom.=".php";
        $fichier=$dir_compilation."/".$nom;
        $file=fopen($fichier, "w");
        fwrite($file, $tmp);
        fclose($file);
        $GLOBALS["dico_registre_include"][$element]=$fichier;
    }
    $this->genere_dictionnaire();
    set_registre ("system/timestamps/compilation", time(), "timestamp de la derniere compilation");
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_parents_a_compiler ($chemin) {
    $retour=array();
    $tmp=explode("/", $chemin);
    if (count($tmp)<=1) {
        return ($retour);
    }
    $nom=array_pop($tmp);
    $chemin2=implode("/", $tmp);
    //print (" @@@@ $chemin2  /  $nom <br> \n");
    $test=$this->get_node_by_chemin($chemin2."/_compile");
    if ($test !== false) {
        //print ("_compile touve ! <br>\n");
        array_push ($retour, $chemin2);
    }
    $liste=$this->get_parents_a_compiler($chemin2);
    foreach ($liste as $toto) {
        array_push ($retour, $toto);
    }
    return ($retour);
    
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function genere_dictionnaire () {
    $dir_compilation=$GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"];
    $fichier=$dir_compilation."/dictionnaire.php";
    $file=fopen($fichier, "w");
    $tmp="";
    foreach ($GLOBALS["dico_registre_include"] as $chemin => $a_inclure) {
        $tmp.='$GLOBALS["dico_registre_include"]["'.$chemin.'"]="'.$a_inclure.'";'."\n";
    }
    $tmp="<?PHP \n\n".$tmp."\n\n ?>";
    fwrite($file, $tmp);
    fclose($file);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_dico ($chemin_array) {
    $str=implode($chemin_array, "/");
    if (isset ($GLOBALS["dico_registre_include"][$str])) {
        return ($GLOBALS["dico_registre_include"][$str]);
    }
    if (count($chemin_array)<=1) {
        return("");
    }
    array_pop($chemin_array);
    $retour=$this->get_dico($chemin_array);
    return ($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// analyse les descriptions du noeud et de ses parents et regarde si on doit l'exporter pour une maj
// @mwb_export@ OU @mwb_non_export@
// remonte de parent en parent jusqu'à ce qu'il trouve un tag (autorisant ou refusant l'export). Si rien trouvé on autorise

function metawb_is_node_exportable ($chemin) {
    // on ne logue que si on est sur un site master
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["mwb_bool_master"] != 1) {
        return ("mwb_non_export");
    }
    
    $chemin_array=$this->analyse_chemin($chemin);
    while (count($chemin_array)>0) {
        $chemin2=implode("/", $chemin_array);
        $node=$this->get_node_by_chemin($chemin2);
        $description=$node["description"];
        if (strpos($description, "@mwb_export@") !== false) {
            return ("mwb_export");
        } elseif (strpos($description, "@mwb_non_export@") !== false) {
            return ("mwb_non_export");
        }
        array_pop($chemin_array);
    }
    return ("mwb_export"); // par defaut
    
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Repère et supprime les éventuels doublons de registre qui peuvent survenir quans on a malencontreusement lancé 2 mises à jour simultanément
// si $parametres["bool_test"]==1 les noeuds ne sont pas supprimés (simple test)

function ddbl_registre ($parametres) {
    $bool_test=$parametres["bool_test"];
    $messages=array();
    $log=array();
    $sql="SELECT COUNT(chemin) AS nbr_doublon, chemin FROM tvs_registre GROUP BY chemin HAVING COUNT(chemin) > 1";

    $doublons=sql_as_array(array("sql"=>$sql, "contexte"=>"tvs_registre::ddbl_registre()"));
    $nb_doublons=count($doublons);
    array_push($log, "$nb_doublons detectes");
    // Pour chaque chemin de doublon
    foreach ($doublons as $doublon) {
        $chemin=$doublon["chemin"];
        $sql2="select * from tvs_registre where chemin = \"$chemin\"";
        $elements=sql_as_array(array("sql"=>$sql2, "contexte"=>"tvs_registre::ddbl_registre()"));
        $nb_elements=count($elements);
        $ids_a_supprimer=array();
        $bool_suppr=0;
        $bool_garde=0;
        array_push($log, "$chemin : $nb_elements doublons");
        // On analyse quels ID sont vides pour être supprimés
        foreach ($elements as $idx => $elem) {
            $ID=$elem["ID"];
            $valeur=$elem["valeur"];
            $nb_enfants=$this->get_nb_enfants($ID);
            if ($valeur=="" AND $nb_enfants==0) {
                array_push($ids_a_supprimer, $ID);
                $bool_suppr=1;
            } else {
                $bool_garde++;
            }
        }
        
        // Contrôle de cohérence et suppression des doublons
        if ($bool_garde==1 AND $bool_suppr==1) {
            foreach ($ids_a_supprimer as $id_a_supprimer) {
                if ($bool_test != 1) {
                    $this->delete_node($id_a_supprimer);
                }
            }
        } elseif ($bool_garde==0 AND $bool_suppr==1) {
            array_push($messages, "Aucun des $nb_elements doublons du noeud $chemin n'a de valeur ni d'enfants. On conserve quand meme un noeud");
            array_pop($ids_a_supprimer); // on conserve un noeud même vide
            foreach ($ids_a_supprimer as $id_a_supprimer) {
                if ($bool_test != 1) {
                    $this->delete_node($id_a_supprimer);
                }
            }
        } elseif ($bool_garde > 1) {
            array_push($messages, "<font color='red'>$bool_garde noeuds sur les $nb_elements doublons du noeud $chemin ont une valeur et/ou des enfants. Les doubons ne peuvent etre supprimes</font>");
        } else {
            array_push($messages, "<font color='red'>probleme avec le doublon chemin : les doublons n'ont pas ete supprimes. bool_garde=$bool_garde. bool_suppr=$bool_suppr</font>");
        }
    } // fin du pour chaque chemin de doublon
    
    $resultat=array("messages"=>$messages, "log"=>$log);
    return($resultat);
}










} // fin de la classe
?>