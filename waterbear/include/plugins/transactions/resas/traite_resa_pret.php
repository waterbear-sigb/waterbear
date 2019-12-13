<?php

function plugin_transactions_resas_traite_resa_pret ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // paramètres   
    $bureau=$parametres["bureau"];
    $plugin_get_resa=$parametres["plugin_get_resa"];
    $plugin_add_message=$parametres["plugin_add_message"];
    $message=$parametres["message"];
    $code=$parametres["code"];
    $code_resa_affectee=$parametres["code_resa_affectee"];
    
    $ID_exemplaire=$bureau["infos_exemplaire"]["ID"];
    $ID_lecteur=$bureau["infos_lecteur"]["ID"];
    
    // 1) on nettoie le bureau
    $bureau["infos_resa"]=array();
    
    // 2) On récupère la résa en cours pour cet exemplaire (s'il y en a)
    $tmp=applique_plugin($plugin_get_resa, array("ID_exemplaire"=>$ID_exemplaire));
    if ($tmp["succes"]!=1) {
        return($tmp);
    }
    $nb_notices_resa=$tmp["resultat"]["nb_notices"];
    $notices_resa=$tmp["resultat"]["notices"];
    
    if ($nb_notices_resa == 0) {
        $retour["resultat"]["bureau"]=$bureau;
        return ($retour);
    } else {
        $ligne=$notices_resa[0];
    }
    $ID_lecteur_resa=$ligne["a_id_lecteur"];
    $etat_resa=$ligne["a_etat"];
    
    // 3) test
    if ($ID_lecteur == $ID_lecteur_resa) {
        $bureau["infos_resa"]=$ligne;
    } else {
        if ($etat_resa == 25) {
            $code=$code_resa_affectee;
        }
        $tmp=applique_plugin($plugin_add_message, array("bureau"=>$bureau, "message"=>$message, "code"=>$code));
        $bureau=$tmp["resultat"]["bureau"];
    }
    
    $retour["resultat"]["bureau"]=$bureau;
    
    
    
    return ($retour);   
}


?>