<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_valide_abonnement()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [nom_ss_champ_lien] : généralement $3 : ss-champ de lien lecteur:600$3 ---> abonnement
 * @param [plugin_infos_abonnement] : les infos sur l'abonnement : [A,B,C...][duree | prix...]
 * @param [plugin_crea_abonnement] : plugin pour générer un objet abonnement
 * @param [plugin_notice_2_db_abonnement] : plugin pour enregistrer dans la db l'objet abonnement
 * @param [plugin_crea_paiement] : plugin pour générer un objet paiement
 * @param [plugin_notice_2_db_paiement] : plugin pour enregistrer dans la db l'objet paiement
 * @param [code_regie] : code régie pour le paiement
 * @param [nb_jours_max_renouvellement] : Nombre max de jours autorisés pour renouveler l'abonnement par anticipation (pour éviter de faire plusieurs arenouvellements par erreur) désactivé si vaut 0 ou rien
 * 
 * 
 * 
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_valide_abonnement ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $ID_element=$parametres["ID_element"];
    
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $plugin_infos_abonnement=$parametres["plugin_infos_abonnement"];
    $plugin_crea_abonnement=$parametres["plugin_crea_abonnement"];
    $plugin_notice_2_db_abonnement=$parametres["plugin_notice_2_db_abonnement"];
    $plugin_crea_paiement=$parametres["plugin_crea_paiement"];
    $plugin_notice_2_db_paiement=$parametres["plugin_notice_2_db_paiement"];
    $code_regie=$parametres["code_regie"];
    $nb_jours_max_renouvellement=$parametres["nb_jours_max_renouvellement"];
    
    $id_ss_champ_lien="";
    $variables=array();
    
    // 1) On récupère ID_notice du lecteur
    $ID_notice=$_SESSION["operations"][$ID_operation]["ID_notice"];
    if ($ID_notice == "") {
         $retour["succes"]=0;
         $retour["erreur"]="@&Vous devez au prealable enregistrer la notice";
         return ($retour); 
    }
    $variables["ID_notice"]=$ID_notice; 
    
    // 2) On récupère la liste des sous-champs  -> [id | valeur |type | nom]
    // On génère une $variable de la forme ["ss_champ_a" => valeur, "ss_champ_b" => valeur]...
    // ATTENTION ne gère pas la possibilités d'avoir plusieurs fois le même ss-champ
    $liste_ss_champs=$formulator->get_ss_champs_by_nom($ID_element, "");
    foreach ($liste_ss_champs as $ss_champ) {
        $nom=$ss_champ["nom"];
        $valeur=$ss_champ["valeur"];
        $id=$ss_champ["id"];
        $variables["ss_champ_".$nom]=$valeur;
        if ($nom == $nom_ss_champ_lien) {
            $id_ss_champ_lien=$id;
        }
    }
    $date_debut=$variables["ss_champ_c"];
    $date_fin=$variables["ss_champ_d"];
    $ID_lecteur=$variables["ID_notice"];
    $abonnement=$variables["ss_champ_a"];
    $timestamp_fin=date_us_2_timestamp($date_fin);
    
    // On regarde s'il n'est pas trop tôt pour renouveler
    if ($nb_jours_max_renouvellement != "" AND $nb_jours_max_renouvellement != "0") {
        if ($timestamp_fin - ($nb_jours_max_renouvellement * 24 * 60 * 60) > time()) {
            $retour["succes"]=0;
            $retour["erreur"]="@&Cet abonnement est encore valable plus de $nb_jours_max_renouvellement jours. Vous ne pouvez le renouveler";
             return ($retour); 
        }
    }
    
    // 3) On récupère les infos sur l'abonnement 
    $tmp=applique_plugin ($plugin_infos_abonnement, array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $infos_abonnement=$tmp["resultat"];
    $duree=$infos_abonnement[$abonnement]["duree"];
    $prix=$infos_abonnement[$abonnement]["prix"];
    
    // 4) On détermine la date de début
    
    
    if ($date_fin != "" AND ($timestamp_fin - time()) > 0) {
        $date_debut = $date_fin;
    } else {
        $date_debut=date("Y-m-d");
    }
    
    // 4) On détermine la date de fin
    if ($duree == "") {
        $retour["succes"]=0;
        $retour["erreur"]="@& Aucune duree parametree pour l'abonnement $abonnement";
        return ($retour);
    }
    $time1=date_us_2_timestamp($date_debut);
    $time2=$time1 + ($duree * 24 * 60 * 60);
    $date_fin=date("Y-m-d", $time2);
    
    $variables["ss_champ_c"]=$date_debut;
    $variables["ss_champ_d"]=$date_fin;
    
    //5) on génère la notice abonnement
    $tmp=applique_plugin ($plugin_crea_abonnement, array("variables"=>$variables));
    if ($tmp ["succes"] != 1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 6) On crée la notice abonnement dans la DB
    $tmp=applique_plugin($plugin_notice_2_db_abonnement, array("notice"=>$notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $ID_abonnement=$tmp["resultat"]["ID_notice"];
    
    // 7) On maj le champ abonnement de la fiche lecteur
    if ($id_ss_champ_lien == "") {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $update=array("valeur"=>$ID_notice);
    $formulator->update_element ($id_ss_champ_lien, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_abonnement.'");');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
    
    // 8) On génère la notice paiement
    $variables=array();
    $variables["ID_notice"]=$ID_notice;
    $variables["ss_champ_a"]=date("Y-m-d");
    $variables["ss_champ_b"]="abo_".$abonnement;
    $variables["ss_champ_d"]=$prix;
    $variables["ss_champ_h"]=$code_regie;
    $tmp=applique_plugin ($plugin_crea_paiement, array("variables"=>$variables));
    if ($tmp ["succes"] != 1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
     // 9) On crée la notice paiement dans la DB
    $tmp=applique_plugin($plugin_notice_2_db_paiement, array("notice"=>$notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $ID_paiement=$tmp["resultat"]["ID_notice"];
    
    // 10) On rafraichit le ss-champ b du porte monnaie
    // ce ss-champ va en outre enregistrer la notice
    $tmp=$formulator->get_champs_by_nom("610");
    if (count($tmp) == 0) {
         $retour["succes"]=0;
         $retour["erreur"]="@& Pas de champ de porte-monnaie (610)";
         return ($retour); 
    }
    $ID_champ_610=$tmp[0]["id"];
    array_push($retour["resultat"], 'this_formulator.transaction("ID_element='.$ID_champ_610.'&action=champ_610_wizard_refresh_solde")');
    
    
    
    return ($retour);
}


?>