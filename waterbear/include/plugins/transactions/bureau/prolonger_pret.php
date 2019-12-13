<?php

/**
 * plugin_transactions_bureau_prolonger_pret()
 * 
 * Ce plugin permet de prolonger un prêt. Il vérifie que le prêt n'a pas déjà été prolongé plus de N et que la notice n'est pas réservée (sauf si bool_force == 1)
 * Puis il MAJ la notice de prêt (date de retour prévu et nb prolongations) et l'enregistre dans la DB
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_transactions_bureau_prolonger_pret ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["date_retour"]="0000-00-00";
    $retour["resultat"]["message"]="";
    
    $ID_pret=$parametres["ID_pret"];
    $bool_force=$parametres["bool_force"]; // si == 1 on ne fait pas les vérifications
    $nb_prolongations_max=$parametres["nb_prolongations_max"]; // nb max de prolongations
    $duree_prolongation=$parametres["duree_prolongation"]; // nb jours de prolongation
    $plugin_recherche_resas=$parametres["plugin_recherche_resas"];
    $plugin_calcule_date=$parametres["plugin_calcule_date"];
    $plugin_maj_notice=$parametres["plugin_maj_notice"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    
    $notice_pret=get_objet_by_id("pret", $ID_pret);
    $date_retour_prevu=$notice_pret["a_date_retour_prevu"];
    $nb_prolongations=$notice_pret["a_nb_prolongations"];
    $ID_biblio=$notice_pret["a_id_biblio"];
    $notice_xml=get_objet_xml_by_id("pret", $ID_pret);
    
    $retour["resultat"]["date_retour"]=$date_retour_prevu; // an cas de pb, pas de modif de la date de retour
    
    
    if ($bool_force != 1) { // on n'effectue les tests que si on ne force pas
        // 1) On regarde si le nb max de prolongations est dépassé
        if ($nb_prolongations >= $nb_prolongations_max) {
            $retour["resultat"]["message"].=get_intitule("plugins/transactions/prets", "nb_prolongations_max", array());
        }

    
        // 2) on regarde si la notice a des réservations en cours
        $tmp=applique_plugin($plugin_recherche_resas, array("ID_biblio"=>$ID_biblio));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $nb_resas=$tmp["resultat"]["nb_notices"];
        if ($nb_resas > 0) {
            $retour["resultat"]["message"].=get_intitule("plugins/transactions/prets", "impossible_prolonger_doc_reserve", array());
        }
        
        if ($retour["resultat"]["message"] != "") {
            return ($retour);
        }
    
    } // fin du teste bool_force != 1
    
    // 3) on calcule la nouvelle date
    $tmp=applique_plugin($plugin_calcule_date, array("duree_prolongation"=>$duree_prolongation, "date_retour_prevu"=>$date_retour_prevu));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $date_retour_prevu=$tmp["resultat"]["date"];
    
    // 4) on maj la notice
    $nb_prolongations++;
    $tmp=applique_plugin($plugin_maj_notice, array("notice"=>$notice_xml, "date_retour_prevu"=>$date_retour_prevu, "nb_prolongations"=>$nb_prolongations));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice_xml=$tmp["resultat"]["notice"];
    
    
    // 5) on enregistre la notice
    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice_xml, "ID_notice"=>$ID_pret));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    // 6) retour
    $retour["resultat"]["date_retour"]=$date_retour_prevu;
    $retour["resultat"]["nb_prolongations"]=$nb_prolongations;
    
    return($retour);   
}


?>