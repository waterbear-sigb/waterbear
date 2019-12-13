<?php

/**
 * plugin_catalogue_marcxml_db_delete_notice()
 * 
 * Ce plugin supprime une notice (à partir d'un type_obj et d'un ID)
 * Il s'agit d'une fonction de bas niveau. Elle supprime les entrées dans les tables obj_xxx_acces et obj_xxx_liens
 * mais elle n'effectue aucune vérification et ne supprime pas les éventuels champs de lien.
 * 
 * @param mixed $parametres
 * @return void
 */
function plugin_catalogue_marcxml_db_delete_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID=$parametres["ID"];
    $type_obj=$parametres["type_obj"];
    
    $table_acces="obj_".$type_obj."_acces";
    $table_liens="obj_".$type_obj."_liens";
    try {
        $sql="delete from $table_acces where ID = $ID";
        sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_delete_notice::suppr obj_xxx_acces"));
        
        $sql="delete from $table_liens where ID = $ID";
        sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_delete_notice::suppr obj_xxx_liens"));
    } catch (tvs_exception $e) {
        $retour["erreur"]=$e->get_exception();
        $retour["succes"]=0;
    }
    
    $retour["resultat"]["ID_notice"]=0;
    return ($retour);
                  
    
}



?>