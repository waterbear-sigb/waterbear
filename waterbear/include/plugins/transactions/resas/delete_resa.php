<?php

function plugin_transactions_resas_delete_resa ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_resa=$parametres["ID_resa"];
    
    $plugin_maj_resa=$parametres["plugin_maj_resa"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $plugin_retour=$parametres["plugin_retour"];
    $code_delete=$parametres["code_delete"]; // 42 => annulée par lecteur, 43 => annulée par bib
    
    if ($code_delete == "") {
        $code_delete=43;
    }
    
    $resa=get_objet_by_id("resa", $ID_resa);
    $cab_doc=$resa["a_cab_exe_affecte"];
    $notice_resa=get_objet_xml_by_id("resa", $ID_resa);
    
    // 1) on maj la notice
    $tmp=applique_plugin ($plugin_maj_resa, array("notice"=>$notice_resa, "code_delete"=>$code_delete));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice_resa=$tmp["resultat"]["notice"];
    
    // 2) on enregistre la résa dans la DB
    $tmp=applique_plugin ($plugin_notice_2_db, array("notice"=>$notice_resa, "ID_notice"=>$ID_resa));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    // 3) On effectue le retour
    if ($cab_doc != "") {
        $tmp=applique_plugin ($plugin_retour, array("mode"=>"retour", "cab_doc"=>$cab_doc));
        return ($tmp);   
    }
    
    return ($retour);
}


?>