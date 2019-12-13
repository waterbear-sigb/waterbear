<?php

function plugin_transactions_resas_traite_resa_retour ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
       
    $bureau=$parametres["bureau"];
    
    
    $plugin_get_resa=$parametres["plugin_get_resa"];
    //$plugin_maj_sur_place=$parametres["plugin_maj_sur_place"];
    //$plugin_maj_transit=$parametres["plugin_maj_transit"];
    $plugin_modif_200=$parametres["plugin_modif_200"];
    $plugin_440_420=$parametres["plugin_440_420"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $duree_affectation=$parametres["duree_affectation"];
    $plugin_add_message=$parametres["plugin_add_message"];
    $message=$parametres["message"];
    $code=$parametres["code"];
    
    $bib=$_SESSION["system"]["bib"];
    
    $exemplaire=$bureau["infos_exemplaire"];
    $ID_exemplaire=$exemplaire["ID"];
    $bib_destination_transit=$bureau["bib_destination"]; // bib de destination de l'exe
    
    
    // 1) on récupère la résa en cours s'il y en a un
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
    
   
    // 2) extraction des infos de la résa
    $bib_destination=$ligne["a_bib_destination"];
    $etat=$ligne["a_etat"];
    $lecteur=$ligne["a_nom_complet_lecteur"];
    $notice=$ligne["xml"];
    $ID=$ligne["ID"];
    $ID_exe_affecte=$ligne["a_id_exe_affecte"];
    if ($bib_destination == "" AND $bib_destination_transit != "") {
        $bib_destination=$bib_destination_transit;
    }
    
    // 2bis) si exe déjà affecté et que ce n'est pas le même on sort
    if ($ID_exe_affecte != "" AND $ID_exe_affecte != $ID_exemplaire) {
        $retour["resultat"]["bureau"]=$bureau;
        return ($retour);
    }
    
    // 3) les dates
    $date_affectation=date("Y-m-d");
    $date_fin_affectation=date("Y-m-d", time()+($duree_affectation*24*60*60));
    
    // 4) maj bureau/bib_destination (si transit) et Message réa dispo (sauf si transit)
    if ($bib != $bib_destination AND $bib_destination != "") {
        $bureau["bib_destination"]=$bib_destination;
    } else { // si sur place
        $bureau["bib_destination"]=""; // on annule les messages de transit
        $message=str_replace("_lecteur_", $lecteur, $message);
        $tmp=applique_plugin($plugin_add_message, array("bureau"=>$bureau, "message"=>$message, "code"=>$code));
        $bureau=$tmp["resultat"]["bureau"];
    }
    
    // 5) maj champ 200
    //dbg_log("ID_exe_affecte : $ID_exe_affecte ; bib : $bib ; bib_destination : $bib_destination");
    // je désactive ce bloc dont je ne sais pa trop quel était son intérêt. il avait pour conséquence qu'une résa en transit
    // passait automatiquement en statut disponible :/
    /**if ($ID_exe_affecte != "") {
        if ($etat != 25) {
            $tmp=applique_plugin($plugin_modif_200, array("notice"=>$notice,  "etat"=>"25", "date_affectation"=>$date_affectation, "date_fin_affectation"=>$date_fin_affectation, "bib_destination"=>$bib_destination));
            if ($tmp["succes"]!=1) {
                return($tmp);
            }
            $notice=$tmp["resultat"]["notice"];
        }
    } else
    **/
    if ($bib != $bib_destination AND $bib_destination != "") { // transit
        $tmp=applique_plugin($plugin_modif_200, array("notice"=>$notice,  "etat"=>"20", "date_affectation"=>"0000-00-00", "date_fin_affectation"=>"0000-00-00", "bib_destination"=>$bib_destination));
        if ($tmp["succes"]!=1) {
            return($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
    } else {
        $tmp=applique_plugin($plugin_modif_200, array("notice"=>$notice,  "etat"=>"25", "date_affectation"=>$date_affectation, "date_fin_affectation"=>$date_fin_affectation, "bib_destination"=>$bib_destination));
        if ($tmp["succes"]!=1) {
            return($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
    }
    
    // 6) ajout champ 440 (exe affecte) et suppression champs 420 (exes demandés)
    if ($ID_exe_affecte != "") {
        // on ne fait rien si l'exe a déjà été affecté
    } else {
        $tmp=applique_plugin($plugin_440_420, array("notice"=>$notice, "ID_exemplaire"=>$ID_exemplaire));
        if ($tmp["succes"]!=1) {
            return($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
    }
    
    // 7) enregistrement de la notice résa
    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID));
    if ($tmp["succes"]!=1) {
        return($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    

    
    $retour["resultat"]["bureau"]=$bureau; 
    return ($retour);
}



?>