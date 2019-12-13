<?php

/**
 * plugin_transactions_resas_traite_resas_depassees()
 * 
 * Lors d'un retour, regarde si des résas avec un état 35 (délai dépassé) sont rattachées à cet exemplaire
 * Si c'est le cas, il modifie l'état en 41 et enregistre la résa
 * Il retourne aussi un message
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_transactions_resas_traite_resas_depassees ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
       
    $bureau=$parametres["bureau"];
    $plugin_recherche=$parametres["plugin_recherche"];
    $plugin_maj=$parametres["plugin_maj"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $plugin_add_message=$parametres["plugin_add_message"];
    
    $ID_exemplaire=$bureau["infos_exemplaire"]["ID"];
    
    // 1) on recherche les résas correspondant à cet exemplaire avec un statut de 35
    $tmp=applique_plugin($plugin_recherche, array("ID_exemplaire"=>$ID_exemplaire));
    if ($tmp["succes"] != 1) {
        return($tmp);
    }
    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    
    foreach ($resas as $ligne) {
        $resa=$ligne["xml"];
        $ID_resa=$ligne["ID"];
        
        // 2) On maj la notice
        $tmp=applique_plugin($plugin_maj, array("notice"=>$resa, "etat"=>"41"));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
        
        // 3) On enregistre la notice
        $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID_resa));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
    }
    
    // 4) Si résas supprimées, on envoie un message
    if ($nb_resas > 0) {
        $tmp=applique_plugin($plugin_add_message, array("bureau"=>$bureau));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
    }
    $bureau=$tmp["resultat"]["bureau"];
    
    
    
    
    
    $retour["resultat"]["bureau"]=$bureau; 
    return ($retour);   
}


?>