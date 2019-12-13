<?php

function mwb_exporte_registre ($timestamp) {
    $sql="SELECT * FROM metawb_log_registre WHERE timestamp >= $timestamp";
    $tableau=sql_as_array(array("sql"=>$sql, "contexte"=>"metawb::mwb_exporte_registre"));
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $chaine=$json->encode($tableau);
    return($chaine);
}

function mwb_exporte_objets ($timestamp) {
    $sql="SELECT * FROM metawb_log_objets WHERE timestamp >= $timestamp";
    $tableau=sql_as_array(array("sql"=>$sql, "contexte"=>"metawb::mwb_exporte_objets"));
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $chaine=$json->encode($tableau);
    return($chaine);
}

function mwb_exporte_paniers () {
    $sql="SELECT * FROM tvs_paniers where chemin_parent like 'waterbear%'";
    $tableau=sql_as_array(array("sql"=>$sql, "contexte"=>"metawb::mwb_exporte_paniers"));
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $chaine=$json->encode($tableau);
    return($chaine);
}

function mwb_exporte ($parametres) {
    $nelle_version=$parametres["version"];
    $descriptif=$parametres["descriptif"];
    $registre=new tvs_registre();
    
    $node=$registre->get_node_by_chemin ("system/metawb/master/timestamp");
	$timestamp=$node["valeur"];
	$id_timestamp=$node["ID"];
    
    $node=$registre->get_node_by_chemin ("system/version/ID");
	$version=$node["valeur"];
	$id_version=$node["ID"];
    
    $node=$registre->get_node_by_chemin ("system/version/nom");
	$nom=$node["valeur"];
	$id_nom=$node["ID"];
    
    $chaine_registre=mwb_traite_chaine(mwb_exporte_registre($timestamp));
    $chaine_objets=mwb_traite_chaine(mwb_exporte_objets($timestamp));
    $chaine_paniers=mwb_traite_chaine(mwb_exporte_paniers());
    $descriptif=mwb_traite_chaine($descriptif);
    
    $timestamp2=time();
    $version2=$version+1;
    
    print ("<?PHP \n");
    print ("// ancien timestamp : $timestamp \n");
    print ("// ancienne version : $version \n");
    print ("// ancien nom : $nom \n");
    print ("// Pour faire une maj : copier le contenu de cette page et le coller dans un fichier maj".$version2.".php dans le repertoire include/maj_version \n");
    print ("// Ne pas oublier de modifier le script conf/version.php pour indiquer la derniere version \n");
    print ("\n\n");
    print ("\$chaine_registre='$chaine_registre';\n");
    print ("\$chaine_objets='$chaine_objets';\n");
    print ("\$chaine_paniers='$chaine_paniers';\n");
    print ("\$descriptif='$descriptif';\n");
    print ("\$nom='$nelle_version';\n");
    print ("\$version='$version2';\n");
    print ("mwb_importe_registre(\$chaine_registre);\n");
    print ("mwb_importe_objets(\$chaine_objets);\n");
    print ("mwb_importe_paniers(\$chaine_paniers);\n");
    print ("?> \n");
    
    $date=date("Y-m-d");
    set_registre ("system/version/ID", $version2, "version master maj le $date"); // on maj la version
    set_registre ("system/metawb/master/timestamp", $timestamp2, "version master maj le $date - ancien timestamp : $timestamp"); // on maj la version
	
	//$registre->niv2_update_node (array("ID"=>$id, "valeur"=>$valeur));
    
}

function mwb_traite_chaine ($chaine) {
    $chaine=str_replace ('\\', '\\\\', $chaine);
    $chaine=str_replace ('\'', '\\\'', $chaine);
    return($chaine);
}

function mwb_importe_registre ($chaine) {
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $registre=new tvs_registre();
    $lignes=$json->decode($chaine);
    foreach ($lignes as $ligne) {
        $type=$ligne["type"];
        $chemin=$ligne["chemin"]; // chemin d'origine pour copy_branche
        $nom=$ligne["nom"];
        $valeur=$ligne["valeur"]; // chemin de destination pour copy_branche
        $description=$ligne["description"]; // ou copie_contenu pour copy_branche
print ("\n<br><font color=red>$type - $chemin - $nom - $valeur - $description</font><br>\n");
        
        try {
            if ($type=="niv2_update_node") {
                $noeud=$registre->get_node_by_chemin($chemin);
                $ID_noeud=$noeud["ID"];
                $registre->niv2_update_node (array("ID"=>$ID_noeud, "nom"=>$nom,  "description"=>$description, "valeur"=>$valeur, "bool_force_vide"=>1));
            } elseif ($type=="niv2_create_node") {
                $noeud=$registre->get_node_by_chemin($chemin);
                $ID_parent=$noeud["ID"];
                $registre->niv2_create_node (array("nom"=>$nom, "parent"=>$ID_parent, "description"=>$description, "valeur"=>$valeur));
            } elseif ($type=="supprimer_noeud") {
                $noeud=$registre->get_node_by_chemin($chemin);
                $ID_noeud=$noeud["ID"];
                $registre->delete_tree($ID_noeud);
            } elseif ($type=="copy_branche") {
                //$registre->copy_branche (array("modele_str"=>$chemin, "destination_str"=>$valeur, "copie_contenu"=>$description));
                // on n'exécute pas les copy_branche, car en fait, ils sont constitués d'autant de niv2_create_node que de noeuds
            }
        } catch (tvs_exception $e) {
            $erreur=utf8_encode(get_exception($e->get_infos()));
            print ("mwb_importe_registre::ERREUR : $type - $chemin - $nom - $valeur - $description - $erreur <br>\n");
    	  	//$retour["succes"]=0;
    	  	//$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
        }
    } // fin du pour chaque ligne
}

function mwb_importe_objets ($chaine) {
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $registre=new tvs_registre();
    $lignes=$json->decode($chaine);
    foreach ($lignes as $ligne) {
        $type=$ligne["type"];
        $type_objet=$ligne["type_objet"];
        $nom=$ligne["nom"];
        $nom_colonne=$ligne["nom_colonne"];
        $ancien_nom_colonne=$ligne["ancien_nom_colonne"];
        $type_colonne=$ligne["type_colonne"];
        $description_colonne=$ligne["description_colonne"];
        $type_index=$ligne["type_index"];
        $multivaleurs=$ligne["multivaleurs"];
        try {
            $gestion_objets=new gestion_objets_db(array("type_objet"=>$type_objet));
            if ($type=="create_objet") {
                $gestion_objets->create_objet();
            } elseif ($type == "delete_objet") {
                $gestion_objets->delete_objet();
            } elseif ($type == "acces_valide_form") {
                $gestion_objets->acces_valide_form(array("nom"=>$nom, "nom_colonne"=>$nom_colonne, "ancien_nom_colonne"=>$ancien_nom_colonne, "type_colonne"=>$type_colonne, "description_colonne"=>$description_colonne, "type_index"=>$type_index, "multivaleurs"=>$multivaleurs));
            } elseif ($type == "tri_valide_form") {
                $gestion_objets->tri_valide_form(array("nom"=>$nom, "nom_colonne"=>$nom_colonne, "ancien_nom_colonne"=>$ancien_nom_colonne, "type_colonne"=>$type_colonne, "description_colonne"=>$description_colonne));
            } elseif ($type == "acces_delete") {
                $gestion_objets->acces_delete_column ($ancien_nom_colonne);
            } elseif ($type == "tri_delete") {
                $gestion_objets->tri_delete_column ($ancien_nom_colonne);
            }
        } catch (tvs_exception $e) {
            print ("mwb_importe_objets::ERREUR : $type - $type_objet - $nom - $nom_colonne - $type_colonne - $description - $type_index - $multivaleurs <br>\n");
    	  	//$retour["succes"]=0;
    	  	//$retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
        }
    }
}

function mwb_importe_paniers ($chaine) {
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $lignes=$json->decode($chaine);
    // suppression des anciens paniers
    $sql="DELETE FROM tvs_paniers where chemin_parent like 'waterbear%'";
    sql_query(array("sql"=>$sql, "contexte"=>"metawb::mwb_importe_paniers"));
    
    // 
    foreach ($lignes as $ligne) {
        $ligne=secure_sql($ligne);
        $nom=$ligne["nom"];
        $description=$ligne["description"];
        $chemin_parent=$ligne["chemin_parent"];
        $type=$ligne["type"];
        $type_obj=$ligne["type_obj"];
        $contenu=$ligne["contenu"];
        $date_creation=$ligne["date_creation"];
        $proprietaire=$ligne["proprietaire"];
        $sql="INSERT INTO tvs_paniers values('', '$nom', '$description', '$chemin_parent', '$type', '$type_obj', 0, '$date_creation', '$proprietaire', '$contenu')";
        sql_query(array("sql"=>$sql, "contexte"=>"metawb::mwb_importe_paniers"));
        
    } // fin de pour chaque ligne
}



?>