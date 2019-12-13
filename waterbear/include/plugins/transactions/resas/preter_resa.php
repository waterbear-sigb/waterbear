<?php

function plugin_transactions_resas_preter_resa ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // paramètres   
    $bureau=$parametres["bureau"];
    $plugin_maj_resa=$parametres["plugin_maj_resa"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    
    $resa=$bureau["infos_resa"];
    $ID_pret=$bureau["id_pret"];
    $notice=$resa["xml"];
    $ID_resa=$resa["ID"];
    
    // 0) On récupère l'objet prêt complet
    
    // 1) Si pas de résa, on ne fait rien
    if ($resa["ID"]=="") {
        $retour["resultat"]["bureau"]=$bureau;
        return($retour);  
    }
    
    // 2) Sinon, on maj la notice
    $tmp=applique_plugin($plugin_maj_resa, array("notice"=>$notice, "ID_pret"=>$ID_pret));
    if ($tmp["succes"]!=1) {
        return($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 3) et on maj la notice
    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID_resa));
    if ($tmp["succes"]!=1) {
        return($tmp);
    }
    
    
    
    
    
    $retour["resultat"]["bureau"]=$bureau;
    return($retour);   
}

?>