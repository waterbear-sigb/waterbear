<?php

/**
 * plugin_catalogue_marcxml_db_delete_notice_autorite()
 * 
 * ce plugin est utilisé pour supprimer des notices autorités (auteurs, sujets...) génériques
 * Elle vérifie qu'il n'y a pas de liens implicites (par exemple pour une notice auteur, qu'aucune notice biblio ne l'utilise)
 * si c'est le cas, suppression, sinon, erreur
 * 
 * @param mixed $parametres
 * @return void
 */
function plugin_catalogue_marcxml_db_delete_notice_autorite ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $objets_lies=$parametres["objets_lies"];
    $ID=$parametres["ID"];
    $type_obj=$parametres["type_obj"];
    $plugin_delete=$parametres["plugin_delete"];
    
    // 1) on regarde, pour chaque type d'objet lié, s'il y a des liens implicites
    foreach ($objets_lies as $objet_lie) {
        $table="obj_".$objet_lie."_liens";
        $sql="select count(*) from $table where type_objet='$type_obj' AND ID_lien=$ID";
        try {
            $nb=sql_as_value(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_delete_notice_autorite::recupere objets de type $objet_lie ayant des liens avec l'objet de type $type_obj et d'ID $ID "));
        } catch (tvs_exception $e) {
            $retour["succes"]=0;
            $retour["erreur"]=$e->get_exception();
            return ($retour);
        }
        
        if ($nb > 0) {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/delete_notice", "notice_liee", array("objet_lie"=>$objet_lie));
            return ($retour);
        }
    }
    
    // 2) Si aucun objet lié, on peut supprimer la notice
    $tmp=applique_plugin ($plugin_delete, array("ID"=>$ID, "type_obj"=>$type_obj));
    return ($tmp);
    
    
    
}



?>