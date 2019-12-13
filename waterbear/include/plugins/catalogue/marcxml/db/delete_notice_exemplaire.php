<?php

/**
 * plugin_catalogue_marcxml_db_delete_notice_exemplaire()
 * 
 * pour supprimer un exemplaire : 
 * 
 * 1. Regarde s'il y a des prêts ou des résas  liés à cet exemplaire (lien implicite)
 * 1.a si NON : suppression classique
 * 1.b si OUI, maj des ss-champs état, prêtable... et enregistrement de la notice
 * 2. Mise à jour de la notice biblio : suppression du champ 997 et enregistrement
 * 
 * @param mixed $parametres
 * @return void
 */
function plugin_catalogue_marcxml_db_delete_notice_exemplaire ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $objets_lies=$parametres["objets_lies"]; // normalement pret et resa
    $ID=$parametres["ID"];
    $type_obj=$parametres["type_obj"]; // normalement exemplaire
    $plugin_delete=$parametres["plugin_delete"];
    $plugin_maj_exe=$parametres["plugin_maj_exe"]; // plugin utilisé pour mettre à jour l'exemplaire q'il ne peut pas être simplement supprimé (état = pilon, prêtable = non ...)'
    $plugin_notice_2_db_exe=$parametres["plugin_notice_2_db_exe"];
    $plugin_maj_biblio=$parametres["plugin_maj_biblio"]; // plugin pour supprimer les champs 997 et 998 de la notice biblio si suppression partielle
    $plugin_notice_2_db_biblio=$parametres["plugin_notice_2_db_biblio"];
    
    $bool_objets_lies=0;

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
            $bool_objets_lies=1;
        }
    }
    
    // 2) Si aucun objet lié, on peut supprimer la notice
    if ($bool_objets_lies == 0) {

        $tmp=applique_plugin ($plugin_delete, array("ID"=>$ID, "type_obj"=>$type_obj));
        
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        
        // on maj la notice biblio (suppression du champ 997 ou 998)
        $notices_liees=get_objets_xml_lies("biblio", "implicite", "", $ID, "exemplaire", 0);
        foreach ($notices_liees as $notice_liee) { // normalement une seule
            $ID_notice_biblio=$notice_liee["ID"];
            $notice_biblio=get_objet_xml_by_id("biblio", $ID_notice_biblio);
            $tmp=applique_plugin($plugin_maj_biblio, array("notice"=>$notice_biblio, "ID_exe"=>$ID));
            if ($tmp["succes"] != 1) {
                return($tmp);
            }
            $notice_biblio=$tmp["resultat"]["notice"];

            $tmp=applique_plugin($plugin_notice_2_db_biblio, array("notice"=>$notice_biblio, "ID_notice"=>$ID_notice_biblio));
            if ($tmp["succes"] != 1) {
                return($tmp);
            }
        }
        $retour["resultat"]["ID_notice"]=0;
        
    } else { // si impossible de supprimer la notice, on maj l'exemplaire (et ça maj aussi automatiquement la notice biblio)'
        $notice_exe=get_objet_xml_by_id($type_obj, $ID);
        $tmp=applique_plugin($plugin_maj_exe, array("notice"=>$notice_exe));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $notice_exe=$tmp["resultat"]["notice"];
        $tmp=applique_plugin($plugin_notice_2_db_exe, array("notice"=>$notice_exe, "ID_notice"=>$ID, "type_objet"=>$type_obj));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $retour["resultat"]["ID_notice"]=$ID;
        
    } // fin du "si impossible de supprimer la notice"
    
    // 3) On supprime le champ 997 de la notice biblio (si suppression complète)
    
    
    
    return ($retour);

}

?>