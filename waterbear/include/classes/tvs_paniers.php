<?php
// CREATE TABLE tvs_paniers (ID INT PRIMARY KEY AUTO_INCREMENT, nom VARCHAR(250), description TEXT, chemin_parent TEXT, type VARCHAR(250), type_obj VARCHAR(250), nb INT, date_creation DATE, proprietaire VARCHAR(250), contenu TEXT);
class tvs_paniers {
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // crée un panier vide
    function create_node ($parametres) {
        $retour=array();
        $retour["succes"]=1;
        $retour["resultat"]=array();
        
        // facultatif... nom et description
        if ($parametres["nom"]=="") {
            $nom="Nouveau panier";
        } else {
            $nom=$parametres["nom"];
        }
        if ($parametres["description"]=="") {
            $description="";
        } else {
            $description=$parametres["description"];
        }
        
        // obligatoire : chemin_parent, type, type_obj
        $chemin_parent=$parametres["chemin_parent"];
        $type=$parametres["type"];
        $type_obj=$parametres["type_obj"];
        $contenu=$parametres["contenu"];
        if ($type=="" OR $type_obj=="") {
            $retour["erreur"]="Pas de type d'objet ou de type de panier";
            $retour["succes"]=0;
            return ($retour);
        }
        
        // On regarde si un panier de même nom existe déjà
        $tmp=$this->get_panier_by_nom ($chemin_parent, $nom, $type_obj);
        if ($tmp != "") {
            $retour["erreur"]="Il existe deja un panier avec le nom $nom";
            $retour["succes"]=0;
            return ($retour);
        }
        
        // On regarde s'il y a bien un répertoire parenr
        if ($chemin_parent != "") {
            $panier_parent=$this->get_panier_by_chemin($chemin_parent, $type_obj);
            if ($panier_parent == "") {
                $retour["erreur"]="Le répertoire $chemin_parent n'existe pas pour les objets $type_obj";
                $retour["succes"]=0;
                return ($retour);
            }
        }
        
        // on crée le panier dans a DB
        $nom=secure_sql($nom);
        $description=secure_sql($description);
        $chemin_parent=secure_sql($chemin_parent);
        $type=secure_sql($type);
        $type_obj=secure_sql($type_obj);
        $contenu=secure_sql($contenu);
        $sql="insert into tvs_paniers values ('', '$nom', '$description', '$chemin_parent', '$type', '$type_obj', 0, CURRENT_DATE, '', '$contenu')";
        sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:create_panier()"));
        $ID=sql_insert_id();
        
        $retour["resultat"]["ID"]=$ID;
        $retour["resultat"]["nom"]=$nom;
        $retour["resultat"]["chemin_parent"]=$chemin_parent;
        return($retour);
    }
    
      
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Récupère un panier à partir de son nom (+chemin parent et type_obj)
    function get_panier_by_nom ($chemin_parent, $nom, $type_obj){
        $chemin_parent=secure_sql($chemin_parent);
        $nom=secure_sql($nom);
        $type_obj=secure_sql($type_obj);
        
        $sql="select * from tvs_paniers where chemin_parent='$chemin_parent' AND type_obj='$type_obj' AND nom='$nom'";
        $retour=sql_as_array(array("sql"=>$sql, "contexte"=>"tvs_paniers::get_panier_by_nom"));
        return ($retour[0]);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // comme précédent, mais on fournit le chemin complet (il faut faire un explode pour trouver le nom et le chemin_parent)
    function get_panier_by_chemin ($chemin, $type_obj){
        $tmp=$this->chemin_2_infos($chemin);
        $chemin_parent=$tmp["chemin_parent"];
        $nom=$tmp["nom"];
        $retour=$this->get_panier_by_nom($chemin_parent, $nom, $type_obj);
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Récupère un panier à partir de son ID
    function get_panier_by_ID ($ID) {
        $sql="select * from tvs_paniers where ID=$ID";
        $retour=sql_as_array(array("sql"=>$sql, "contexte"=>"tvs_paniers::get_panier_by_ID"));
        return ($retour[0]);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Récupère le contenu d'un répertoire 
    function get_contenu_repertoire ($chemin_parent, $type_obj) {
        $chemin_parent=secure_sql($chemin_parent);
        $type_obj=secure_sql($type_obj);
        
        $sql="select * from tvs_paniers where chemin_parent='$chemin_parent' AND type_obj='$type_obj' ORDER by type, nom";
        $retour=sql_as_array(array("sql"=>$sql, "contexte"=>"tvs_paniers::get_contenu_repertoire"));
        return($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Récupère le contenu d'un répertoire à partir de son ID
    function get_contenu_repertoire_by_id ($ID, $type_obj) {
        $panier=$this->get_panier_by_ID($ID);
        $chemin_complet=$panier["chemin_parent"]."/".$panier["nom"];
        $contenu=$this->get_contenu_repertoire($chemin_complet, $type_obj);
        return ($contenu);
        
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // MAJ les chemins parents des enfants d'un répertoire quand son nom change
    
    function update_enfants_repertoire ($type_obj, $ancien_chemin, $nouveau_chemin) {
        $liste=$this->get_contenu_repertoire($ancien_chemin, $type_obj);
        foreach ($liste as $noeud) {
            $nom_noeud=$noeud["nom"];
            $type_noeud=$noeud["type"];
            $ancien_chemin=secure_sql($ancien_chemin);
            $nouveau_chemin=secure_sql($nouveau_chemin);
            $sql="update tvs_paniers set chemin_parent='$nouveau_chemin' where chemin_parent='$ancien_chemin' AND type_obj='$type_obj'";
            sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:update_enfants_repertoire()"));
            if ($type_noeud == "repertoire") {
                $this->update_enfants_repertoire($type_obj, $ancien_chemin."/".$nom_noeud, $nouveau_chemin."/".$nom_noeud);
            }
        }
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Update un noeud (nom et description)
    function save ($ID, $nom, $description) {
        // on supprime les caractères spéciaux du nom du panier
        $nom=str_replace("'", " ", $nom);
        $nom=str_replace("\"", " ", $nom);
        $nom=str_replace("<", " ", $nom);
        $nom=str_replace("&", " ", $nom);
        $nom=trim($nom);
        if ($nom=="") {
            $retour["erreur"]="Nom de panier invalide";
            $retour["succes"]=0;
            return ($retour);
        }
        
        // 1) On regarde si le panier est un repertoire ou pas
        $panier=$this->get_panier_by_ID($ID);
        $type=$panier["type"];
        $ancien_nom=$panier["nom"];
        $type_obj=$panier["type_obj"];
        $chemin_parent=$panier["chemin_parent"];
        
        // 1bis) on vérifie qu'il n'y a pas de doublon
        $test=$this->get_panier_by_nom($chemin_parent, $nom, $type_obj);
        if ($test["ID"] != "") {
            $retour["erreur"]="Il existe deja un panier avec le nom $nom";
            $retour["succes"]=0;
            return ($retour);
        }
        if ($type == "repertoire" AND $ancien_nom != $nom) {
            if ($chemin_parent=="") {
                $ancien_chemin=$ancien_nom;
                $nouveau_chemin=$nom;
            } else {
                $ancien_chemin=$chemin_parent."/".$ancien_nom;
                $nouveau_chemin=$chemin_parent."/".$nom;
            }
            $this->update_enfants_repertoire ($type_obj, $ancien_chemin, $nouveau_chemin);
        }
        
        
        $nom=secure_sql($nom);
        $description=secure_sql($description);

        $sql="update tvs_paniers set nom='$nom', description='$description' where ID=$ID";
        try {
            sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:save()"));
        } catch (tvs_exception $e) {
            $retour["erreur"]="Erreur SQL : $sql";
            $retour["succes"]=0;
            return ($retour);
        }
        $retour["resultat"]="";
        $retour["succes"]=1;
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Supprimer un panier (pas un répertoire)
    
    function delete_panier ($ID) {
        $retour=array();
        $retour["succes"]=1;
        $retour["resultat"]="";
        
        $panier=$this->get_panier_by_ID($ID);
        if ($panier["type"]=="repertoire") {
            $sous_paniers=$this->get_contenu_repertoire_by_id($ID, $panier["type_obj"]);
            if (count($sous_paniers) > 0) {
                $retour["succes"]=0;
                $retour["erreur"]=get_intitule("bib_ws/catalogue/paniers", "impossible_supprimer_repertoire", array());
                return ($retour);
            }
        }
        $retour["resultat"]["chemin_parent"]=$panier["chemin_parent"];
        $panier_parent=$this->get_panier_by_chemin($panier["chemin_parent"], $panier["type_obj"]);
         $retour["resultat"]["ID_parent"]=$panier_parent["ID"];
        $sql="delete from tvs_paniers where ID=$ID";
        sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:delete_panier()"));
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Ajouter un contenu dynamique à un panier
    
    function add_dynamique ($ID, $contenu) {
        $retour=array();
        $retour["succes"]=1;
        $retour["resultat"]="";
        $panier=$this->get_panier_by_ID($ID);
        if ($panier["type"] != "dynamique") {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("bib_ws/catalogue/paniers", "impossible_non_dynamique", array());
            return ($retour);
        }
        $contenu=secure_sql($contenu);
        $sql="update tvs_paniers set contenu='$contenu' where ID=$ID";
        sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:add_dynamique()"));
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Ajouter un contenu statique
    
    function add_statique ($ID, $contenu) {
        $retour=array();
        $retour["succes"]=1;
        $retour["resultat"]="";
        $panier=$this->get_panier_by_ID($ID);
        if ($panier["type"] != "statique") {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("bib_ws/catalogue/paniers", "impossible_non_statique", array());
            return ($retour);
        }
        $contenu_ancien=$panier["contenu"];
        if ($contenu_ancien=="") {
            $sql="update tvs_paniers set contenu='$contenu' where ID=$ID";
        } else {
            $tableau_ancien=explode(",", $contenu_ancien);
            $tableau_nouveau=explode(",", $contenu);
            $tableau_diff=array_values(array_diff($tableau_nouveau, $tableau_ancien));
            
            if (count($tableau_diff)>0) {
                $contenu_diff=implode(",", $tableau_diff);
                $contenu_resultat=$contenu_ancien.",".$contenu_diff;
            } else {
                $contenu_resultat = $contenu_ancien;
            }
            $sql="update tvs_paniers set contenu='$contenu_resultat' where ID=$ID";
        }
        sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:add_statique()"));
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Retirer un contenu statique
    
    function remove_statique ($ID, $contenu) {
        $retour=array();
        $retour["succes"]=1;
        $retour["resultat"]="";
        $panier=$this->get_panier_by_ID($ID);
        if ($panier["type"] != "statique") {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("bib_ws/catalogue/paniers", "impossible_non_statique", array());
            return ($retour);
        }
        $contenu_ancien=$panier["contenu"];
        if ($contenu_ancien == "" OR $contenu == "") {
            return ($retour);
        }
        $tableau_ancien=explode(",", $contenu_ancien);
        $tableau_nouveau=explode(",", $contenu);
        $tableau_diff=array_values(array_diff($tableau_ancien, $tableau_nouveau));
        
        if (count($tableau_diff)>0) {
            $contenu_resultat=implode(",", $tableau_diff);
            //$contenu_resultat=$contenu_diff.", ";
        } else {
            $contenu_resultat = "";
        }
        $sql="update tvs_paniers set contenu='$contenu_resultat' where ID=$ID";
        sql_query(array("sql"=>$sql, "contexte"=>"tvs_paniers:add_statique()"));
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Crée un nouveau panier avec un n° incrémentiel
    function panier_auto ($parametres) {
        $compteur=get_compteur("id_panier_auto");
        $nom="panier_".$compteur;
        $parametres["nom"]=$nom;
        $retour=$this->create_node($parametres);
        return ($retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Analyse le chemin complet et renvoie le chemin du parent et le nom
    function chemin_2_infos ($chemin) {
        $tmp=explode("/",$chemin);
        $nb=count ($tmp);
        if ($nb == 1) {
            $chemin_parent="";
            $nom=$tmp[0];
        } else {
            $nom=array_pop($tmp);
            $chemin_parent=implode("/", $tmp);
        }
        return (array("chemin_parent"=>$chemin_parent, "nom"=>$nom));
        
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Copie ou déplace (si $bool_suppr==1) un panier vers un répertoire de destination
    function copie_panier ($ID_panier, $chemin_dest, $bool_supr) {
        $retour=array();
        $retour["succes"]=1;
        $retour["resultat"]=array();
        // 1) on récupère le panier
        $panier=$this->get_panier_by_ID($ID_panier);
        $type_obj=$panier["type_obj"];
        $type=$panier["type"];
        $chemin_parent=$panier["chemin_parent"];
        $nom=$panier["nom"];
        $chemin_complet=$chemin_parent."/".$nom;
        
        $retour["resultat"]["chemin_parent"]=$chemin_parent;
        
        // 2) on récupère le répertoire de destination
        $rep=$this->get_panier_by_chemin($chemin_dest, $type_obj);
        if ($rep["type"] != "repertoire") {
            $retour["succes"]=0;
            $retour["erreur"]="$chemin_dest n'est pas un repertoire";
            return ($retour);
        }
        
        // 3) On copie le panier (ou réperoire)
        $resultat=$this->create_node(array("chemin_parent"=>$chemin_dest, "nom"=>$nom, "type"=>$type, "type_obj"=>$type_obj, "description"=>$panier["description"], "contenu"=>$panier["contenu"]));
        if ($resultat["succes"] != 1) {
            return ($resultat);
        }
        
        // 4) Si répertoire, on copie les enfants
        if ($type == "repertoire") {
            $enfants=$this->get_contenu_repertoire($chemin_complet, $type_obj);
            foreach ($enfants as $enfant) {
                if ($enfant["ID"] != "") {
                    $dest=$chemin_dest."/".$nom;
                    $resultat=$this->copie_panier($enfant["ID"], $dest, $bool_supr);
                    if ($resultat["succes"] != 1) {
                        return ($resultat);
                    }
                }
            }
        } 
        
        // 5) on supprime le panier si déplacer
        if ($bool_supr == 1) {
            $resultat=$this->delete_panier($ID_panier);
            if ($resultat["succes"] != 1) {
                return ($resultat);
            }
        }
        return ($retour);
    }
    
} // fin de la classe


?>