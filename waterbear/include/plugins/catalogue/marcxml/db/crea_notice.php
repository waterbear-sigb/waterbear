<?php

/**
 * plugin_catalogue_marcxml_db_crea_notice()
 * 
 * @param mixed $parametres 
 * @param ["contenu"] => le contenu
 * @param ["acces"] => les acces sous la forme ["nom_acces"] => "valeur acces"
 * @param ["tri"] => les tris (idem)
 * @param ["type"] => Type d'objet (biblio...)'
 * @param ["ID"] *option* => ID de la notice (si maj)
 * @param ["liens"] => les liens sous la forme ["ID_lien"] => ID de la notice liée, ["type_objet"] => type d'objet lié, ["type_lien"] => type de lien
 * 
 * Ce plugin enregistre dans la DB les données fournies en paramètre (sous forme d'array)
 * 
 * @return
 */
function plugin_catalogue_marcxml_db_crea_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID=$parametres["ID"];
    if ($parametres["contenu"]=="") {
        return (array("succes"=>0, "erreur"=>get_intitule("plugins/catalogue/marcxml/db/crea_notice", "contenu_vide", array())));
    }
    // On génère SQL pour accès
    $sql_acces="";
    if (is_array($parametres["acces"])) {
        foreach ($parametres["acces"] as $nom_acces => $valeur_acces) {
            if ($sql_acces != "") {
                $sql_acces.=", ";
            }
            $sql_acces.=$nom_acces." = '".secure_sql($valeur_acces)."' ";
        } 
    }
    
    // On génère SQL pour tri
    $sql_tri="";
    if (is_array($parametres["tri"])) {
        foreach ($parametres["tri"] as $nom_tri => $valeur_tri) {
            if ($sql_tri != "" OR $sql_acces != "") {
                $sql_tri.=", ";
            }
            $sql_tri.=$nom_tri." = '".secure_sql($valeur_tri)."' ";
        } 
    }
    
    
    // TABLE OBJ_XXX_ACCES
    try {
        if ($ID != "") { // MAJ
            $sql="update obj_".$parametres["type"]."_acces SET contenu = '".secure_sql($parametres["contenu"])."', $sql_acces  $sql_tri where ID=$ID";
            sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_crea_notice::maj obj_xxx_acces"));
            //$retour["resultat"]["sql"]=$sql;
        } else { // CREATION
            $sql="insert into obj_".$parametres["type"]."_acces SET ID = '', contenu = '".secure_sql($parametres["contenu"])."', $sql_acces  $sql_tri";
            sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_crea_notice::crea obj_xxx_acces"));
            $ID=mysql_insert_id();
            
            // on crée le champ 001 et on modifie la notice
            /**
            $notice=new DOMDocument();
            $notice->preserveWhiteSpace = false;
            $notice->loadXML($parametres["contenu"]);
            $notice=add_champ_000($notice, $ID, $parametres["type"]);
            $contenu=$notice->saveXML();
            $sql="update obj_".$parametres["type"]."_acces SET contenu = '".secure_sql($contenu)."' where ID=$ID";
            sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_crea_notice::maj obj_xxx_acces pour insertion champ 001"));
            **/
            
            
        }
    } catch (tvs_exception $e) {
        return (array("succes"=>0, "erreur"=>$e->get_exception()));
    }
     $retour["resultat"]["ID_notice"]=$ID;
    
    
    
    // TABLE OBJ_XXX_LIENS
    
    // si maj, on supprime les liens existants avant de les recréer
    if ($ID != "")
        try {
            $sql="delete from obj_".$parametres["type"]."_liens where ID = $ID";
            sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_crea_notice::suppr obj_xxx_liens"));
        } catch (tvs_exception $e) {
            return (array("succes"=>0, "erreur"=>$e->get_exception()));
        }
    
    
    try {
        /**$types_liens=array();**/
        if (is_array($parametres["liens"])) {
            foreach ($parametres["liens"] as $lien) {
                $ID_lien=$lien["ID_lien"];
                $type_lien=$lien["type_lien"];
                $type_objet=$lien["type_objet"];
                if ($ID_lien == "" OR $type_objet == "" OR !is_numeric($ID_lien)) {
                    continue;
                }
                /**
                // a) on supprime les liens de ce type déjà existants (une seule fois par type) 
                if ($types_liens[$type_objet."_".$type_lien] != 1) {
                    $sql="delete from obj_".$parametres["type"]."_liens where ID = $ID and type_objet='$type_objet' and type_lien='$type_lien'";
                    sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_crea_notice::suppr obj_xxx_liens"));
                    $types_liens[$type_objet."_".$type_lien]=1;
                }
                **/
                
                // b) on crée le lien
                $sql="insert into obj_".$parametres["type"]."_liens values ($ID, $ID_lien, '$type_objet', '$type_lien')";
                sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_crea_notice::crea obj_xxx_liens"));
            }
        }
    } catch (tvs_exception $e) {
        return (array("succes"=>0, "erreur"=>$e->get_exception()));
    }
    
    
    return ($retour);
    
    
}



?>