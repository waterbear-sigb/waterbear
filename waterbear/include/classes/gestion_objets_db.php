<?PHP
// Cette classe gère les différents objets (biblio, exemplaires, lecteurs...) au niveau de la base de données
// elle gère la modification de la structure des tables pour les accès, le tri, les liens...
// elle modifie aussi le registre en conséquence

// CREATE TABLE metawb_log_objets (timestamp INT, type VARCHAR(50), type_objet VARCHAR(250), nom VARCHAR(250), nom_colonne VARCHAR(250), ancien_nom_colonne VARCHAR(250), type_colonne VARCHAR(250), description_colonne TEXT, type_index VARCHAR(250), multivaleurs VARCHAR(250));

class gestion_objets_db {
var $type_objet;
var $table_acces;
var $table_contenu;
var $table_tri;
var $table_liens;
  
function gestion_objets_db($parametres) {
	$this->type_objet=$parametres["type_objet"];
	$this->table_acces="obj_".$this->type_objet."_acces";
	$this->table_contenu="obj_".$this->type_objet."_contenu";
	$this->table_tri="obj_".$this->type_objet."_acces"; // idem que l'acces
	$this->table_liens="obj_".$this->type_objet."_liens";
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// fonctions de bas niveau pour modifier la DB (surtout du SQL)
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function create_objet () {
  	$sql=array();
  	$sql[0]="CREATE TABLE ".$this->table_acces." (ID INT NOT NULL PRIMARY KEY AUTO_INCREMENT, contenu TEXT)";
  	$sql[1]="CREATE TABLE ".$this->table_liens." (ID INT NOT NULL, ID_lien INT NOT NULL, type_objet VARCHAR(250), type_lien VARCHAR(250))";
	$sql[2]="ALTER TABLE ".$this->table_liens." ADD INDEX ID_idx (ID)";
	$sql[3]="ALTER TABLE ".$this->table_liens." ADD INDEX ID_lien_idx (ID_lien)";
  	foreach($sql as $sql2) {
  		$retour=sql_query (array("sql"=>$sql2, "contexte"=>"gestion_objets_db::create_objet"));
  	}
  	set_registre ("profiles/defaut/objets/".$this->type_objet, "", "Objets de type ".$this->type_objet);
  	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers", "", "Caractéristiques des tables de la base de données permettant de gérer les objets de type ".$this->type_objet, "", "Objets de type ".$this->type_objet);
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces", "", "Description des colonnes de la table ".$this->table_acces." permettant de gérer les acces aux objets de type ".$this->type_objet);
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste", "", "Liste des accès aux objets de type ".$this->type_objet);
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri", "", "Description des colonnes de la table ".$this->table_tri." permettant de gérer le tri des objets de type ".$this->type_objet);
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste", "", "liste des critères de tri des objets de type ".$this->type_objet);
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/liens", "", "Description des colonnes de la table ".$this->table_liens." permettant de gérer les liens entre les objets de type ".$this->type_objet." et les autres objets");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/contenu", "", "Description des colonnes de la table ".$this->table_contenu." permettant de stocker en XML le détail des objets de type ".$this->type_objet);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function delete_objet () {
  	$sql=array();
  	$sql[0]="DROP TABLE ".$this->table_acces;
  	$sql[1]="DROP TABLE ".$this->table_liens;
  	foreach($sql as $sql2) {
  		$retour=sql_query (array("sql"=>$sql2, "contexte"=>"gestion_objets_db::create_objet"));
  	}
  	unset_registre ("profiles/defaut/objets/".$this->type_objet);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function empty_objet () {
    $sql=array();
  	$sql[0]="DELETE FROM ".$this->table_acces;
  	$sql[1]="DELETE FROM ".$this->table_liens;
    foreach($sql as $sql2) {
  		$retour=sql_query (array("sql"=>$sql2, "contexte"=>"gestion_objets_db::empty_objet"));
  	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function add_column ($nom_table, $nom_colonne, $type_colonne) {
  	$sql="ALTER TABLE $nom_table ADD COLUMN $nom_colonne $type_colonne";
  	sql_query (array("sql"=>$sql, "contexte"=>"gestion_objets_db::add_column"));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function change_column ($nom_table, $ancien_nom_colonne, $nom_colonne, $type_colonne) {
  	$sql="ALTER TABLE $nom_table CHANGE COLUMN $ancien_nom_colonne $nom_colonne $type_colonne";
  	sql_query (array("sql"=>$sql, "contexte"=>"gestion_objets_db::change_column"));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function drop_column ($nom_table, $nom_colonne) {
  	$sql="ALTER TABLE $nom_table DROP COLUMN $nom_colonne";
  	sql_query (array("sql"=>$sql, "contexte"=>"gestion_objets_db::drop_column"));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function add_index ($nom_table, $nom_colonne, $type_index) {
    if ($type_index=="none") {
        return("");
    }
  	$length="";
  	if (is_numeric($type_index)) { // cas spécial : index classique sur une colonne de type TXT : nécessite une longueur
	    $length="($type_index)";
	    $type_index="";
	}
  	$sql="CREATE $type_index INDEX ".$nom_colonne."_idx ON $nom_table ($nom_colonne $length)";
  	sql_query (array("sql"=>$sql, "contexte"=>"gestion_objets_db::add_index"));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function drop_index ($nom_table, $nom_colonne) {
  	$sql="ALTER TABLE $nom_table DROP INDEX $nom_colonne"."_idx";
  	sql_query (array("sql"=>$sql, "contexte"=>"gestion_objets_db::drop_index"));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function change_index ($nom_table, $nom_colonne, $type_index) {
  	$this->drop_index ($nom_table, $nom_colonne); // on supprime l'index existant
  	$this->add_index ($nom_table, $nom_colonne, $type_index); // on le recrée
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Vérifie qu'un type d'index donné est compatible avec un type de colonne donné
function teste_index ($type_colonne, $type_index) {
  	if ($type_colonne=="TEXT" AND $type_index=="") {
	    return(false);
	}
    if ($type_colonne=="BLOB" AND $type_index == "none") {
        return(true);
    }
  	if ($type_colonne!="TEXT" AND $type_colonne!="VARCHAR(250)" AND $type_index!="") {
	    return(false);
	}
    if ($type_colonne=="BLOB" AND $type_index != "none") {
        return(false);
    }
  	return(true);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// fonctions de niveau 2 gérant également le registre, la création simultanée de la colonne et de l'index...
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// *********************** GENERAL ********************************
// Récupère tous les objets (et paramètres) à partir du registre
function get_liste_objets () {
  	try {$objets=get_registre("profiles/defaut/objets");}
  	catch (Exception $e) {return (false);}
  	return($objets);
}

// *********************** ACCES **********************************

function acces_add_column ($nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs) {
  	// Vérifie compatibilité type_colonne / type_index
  	if (! $this->teste_index($type_colonne, $type_index)) {
	    throw new tvs_exception ("objets/index_incompatible", array());
	}
  	// Création de la colonne
  	try {$this->add_column ($this->table_acces, $nom_colonne, $type_colonne);}
	catch (Exception $e) {throw new tvs_exception ("objets/erreur_crea_col", array("colonne"=>$nom_colonne, "table"=>$this->table_acces));}
	// Création de l'index
	try {$this->add_index ($this->table_acces, $nom_colonne, $type_index);} 
	catch (Exception $e) {throw new tvs_exception ("objets/erreur_crea_idx", array("colonne"=>$nom_colonne, "table"=>$this->table_acces));}
	// MAJ du registre
	$this->acces_update_registre ($nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function acces_update_column ($nom_colonne, $ancien_nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs) {
  	// Vérifie compatibilité type_colonne / type_index
  	if (! $this->teste_index($type_colonne, $type_index)) {
	    throw new tvs_exception ("objets/index_incompatible", array());
	}
	// On récupère les données du registre
	$registre=get_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$ancien_nom_colonne");
	// Est-ce qu'il faut supprimer l'index ? (pas de try...catch pour pas qu'il bloque s'il n'y a pas d'index)
	if ($ancien_nom_colonne != $nom_colonne OR $registre["type_colonne"] != $type_colonne OR $registre["type_index"] != $type_index) {
	  	$this->drop_index ($this->table_acces, $ancien_nom_colonne);
	}
	// Est-ce qu'il faut MAJ la table
	if ($ancien_nom_colonne != $nom_colonne OR $registre["type_colonne"] != $type_colonne) {
	  	try {$this->change_column ($this->table_acces, $ancien_nom_colonne, $nom_colonne, $type_colonne);}
		catch (Exception $e) {throw new tvs_exception ("objets/erreur_update_col", array("colonne"=>$ancien_nom_colonne, "table"=>$this->table_acces));}
	}
	// Est-ce qu'il faut recréer l'index ?
	if ($ancien_nom_colonne != $nom_colonne OR $registre["type_colonne"] != $type_colonne OR $registre["type_index"] != $type_index) {
	  	try {$this->add_index ($this->table_acces, $nom_colonne, $type_index);}
	  	catch (Exception $e) {throw new tvs_exception ("objets/erreur_crea_idx", array("colonne"=>$nom_colonne, "table"=>$this->table_acces));}
	}
	// On MAJ le registre
	unset_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$ancien_nom_colonne");
	$this->acces_update_registre ($nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function acces_update_registre ($nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs) {
  	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$nom_colonne/type_colonne", $type_colonne, "type de la colonne (INT, VARCHAR, TEXT, DATE)");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$nom_colonne/type_index", $type_index, "type de l'index FULLTEXT ou vide (index normal)");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$nom_colonne/nom", $nom, "Dénomination de cet accès");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$nom_colonne/description_colonne", $description_colonne, "Description de l'accès'");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$nom_colonne/multivaleurs", $multivaleurs, "Cet accès est-il de type multivaleurs (contient des valeurs multiples)");	
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function acces_delete_column ($nom_colonne) {
  	// Suppression de la colonne
  	try {$this->drop_column ($this->table_acces, $nom_colonne);}
	catch (Exception $e) {throw new tvs_exception ("objets/erreur_suppr_col", array("colonne"=>$nom_colonne, "table"=>$this->table_acces));}
	// MAJ du registre
	unset_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/acces/liste/$nom_colonne");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction analyse les données fournies par le formulaire
// voit si c'est création ou MAJ et voit quelles sont les données modifiées (à mettre à jour

function acces_valide_form ($parametres) {
  	extract($parametres);
  	if ($ancien_nom_colonne == "") { // CREATION
	    $this->acces_add_column ($nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs);
	} else { // MAJ
	  	$this->acces_update_column ($nom_colonne, $ancien_nom_colonne, $type_colonne, $description_colonne, $type_index, $nom, $multivaleurs);
	}
}

// *********************** TRI **********************************

function tri_add_column ($nom_colonne, $type_colonne, $description_colonne, $nom) {
  	// Création de la colonne
  	try {$this->add_column ($this->table_tri, $nom_colonne, $type_colonne);}
	catch (Exception $e) {throw new tvs_exception ("objets/erreur_crea_col", array("colonne"=>$nom_colonne, "table"=>$this->table_tri));}
	// MAJ du registre
	$this->tri_update_registre ($nom_colonne, $type_colonne, $description_colonne, $nom);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function tri_update_column ($nom_colonne, $ancien_nom_colonne, $type_colonne, $description_colonne, $nom) {
	// On récupère les données du registre
	$registre=get_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste/$ancien_nom_colonne");
	// Est-ce qu'il faut MAJ la table
	if ($ancien_nom_colonne != $nom_colonne OR $registre["type_colonne"] != $type_colonne) {
	  	try {$this->change_column ($this->table_tri, $ancien_nom_colonne, $nom_colonne, $type_colonne);}
		catch (Exception $e) {throw new tvs_exception ("objets/erreur_update_col", array("colonne"=>$ancien_nom_colonne, "table"=>$this->table_tri));}
	}
	// On MAJ le registre
	unset_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste/$ancien_nom_colonne");
	$this->tri_update_registre ($nom_colonne, $type_colonne, $description_colonne, $nom);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function tri_update_registre ($nom_colonne, $type_colonne, $description_colonne, $nom) {
  	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste/$nom_colonne/type_colonne", $type_colonne, "type de la colonne (INT, VARCHAR, TEXT, DATE)");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste/$nom_colonne/nom", $nom, "Dénomination de cet accès");
	set_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste/$nom_colonne/description_colonne", $description_colonne, "Description de l'accès'");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function tri_delete_column ($nom_colonne) {
  	// Suppression de la colonne
  	try {$this->drop_column ($this->table_tri, $nom_colonne);}
	catch (Exception $e) {throw new tvs_exception ("objets/erreur_suppr_col", array("colonne"=>$nom_colonne, "table"=>$this->table_tri));}
	// MAJ du registre
	unset_registre ("profiles/defaut/objets/".$this->type_objet."/param_fichiers/tri/liste/$nom_colonne");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction analyse les données fournies par le formulaire
// voit si c'est création ou MAJ et voit quelles sont les données modifiées (à mettre à jour

function tri_valide_form ($parametres) {
  	extract($parametres);
  	if ($ancien_nom_colonne == "") { // CREATION
	    $this->tri_add_column ($nom_colonne, $type_colonne, $description_colonne, $nom);
	} else { // MAJ
	  	$this->tri_update_column ($nom_colonne, $ancien_nom_colonne, $type_colonne, $description_colonne, $nom);
	}
}

  
} // fin de la classe



?>