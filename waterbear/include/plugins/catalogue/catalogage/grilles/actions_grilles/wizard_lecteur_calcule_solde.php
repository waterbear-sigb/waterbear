<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_calcule_solde()
 * 
 * @param mixed $parametres
 * @param [ID_lecteur] => ID de la notice lecteur
 * @param [plugin_recherche_paiements] => plugin de recherche des paiements de ce lecteur
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_calcule_solde ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_lecteur=$parametres["ID_lecteur"];
    $plugin_recherche_paiements=$parametres["plugin_recherche_paiements"];
    
    // 1) On recherche les paiements de ce lecteur via le plugin ad hoc    
    $tmp=applique_plugin($plugin_recherche_paiements, array("ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notices=$tmp["resultat"]["notices"];
    
    $solde=0;
    foreach ($notices as $notice) {
        $credit=$notice["a_credit"];
        $debit=$notice["a_debit"];
        $solde += $credit;
        $solde -= $debit;
        
    }
    
    $solde=round($solde, 2);
    
    //$str=var_export($notices, true);
    $retour["resultat"]["solde"]=$solde;
    
    return ($retour);
}


?>