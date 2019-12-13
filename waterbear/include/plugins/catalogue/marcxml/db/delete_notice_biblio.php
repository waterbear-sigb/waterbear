<?php

/**
 * plugin_catalogue_marcxml_db_delete_notice_biblio()
 * 
 * Ce plugin supprime une notice biblio, en vérifiant qu'il n'y a aucun exemplaire rattaché à la notice (même des exemplaires inactifs)
 * Si OK, on supprimera la notice en appelant un plugin de type delete_notice_autorite qui vérifiera pour sa part que la notice biblio
 * n'a pas d'objets liés implicitement (prêts, résas...)
 * 
 * @param mixed $parametres
 * @return void
 */
function plugin_catalogue_marcxml_db_delete_notice_biblio ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID=$parametres["ID"];
    $plugin_delete=$parametres["plugin_delete"];
    
    // 1) on regarde si cette notice biblio a des exemplaires (actifs ou non)
    
    $sql="select count(*) from obj_biblio_liens where type_objet='exemplaire' AND ID=$ID";
    try {
        $nb=sql_as_value(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_delete_notice_biblio::recupere les exemplaires lies a la notice $ID "));
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
        return ($retour);
    }
    
    if ($nb > 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/delete_notice", "exemplaires_lies", array());
        return ($retour);
    }
    
    // 2) Si aucun objet lié, on peut supprimer la notice (après avoir vérifié qu'aucun lien implicite ne subsiste (prêt, résa...))
    $tmp=applique_plugin ($plugin_delete, array("ID"=>$ID, "type_obj"=>"biblio"));
    return ($tmp);
    
    
    
    
    
}



?>