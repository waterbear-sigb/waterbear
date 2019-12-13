<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_refresh_solde()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [plugin_calcule_solde] => pour calculer le solde du lecteur
 * 
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_refresh_solde ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $ID_element=$parametres["ID_element"];
    
    $plugin_calcule_solde=$parametres["plugin_calcule_solde"];
    
    // 1) On récupère ID_notice du lecteur
    $ID_notice=$_SESSION["operations"][$ID_operation]["ID_notice"];
    if ($ID_notice == "") {
         $retour["succes"]=0;
         $retour["erreur"]="@&Vous devez au prealable enregistrer la notice";
         return ($retour); 
    }
    
    // 2) On calcule le solde grâce au plugin
    $tmp=applique_plugin ($plugin_calcule_solde, array("ID_lecteur"=>$ID_notice));
    if ($tmp ["succes"] != 1) {
        return ($tmp);
    }
    $solde=$tmp["resultat"]["solde"];
    
    // 3) On récupère l'ID du sous-champ 9b
    $tmp=$formulator->get_ss_champs_by_nom($ID_element, "9b");
    if (count($tmp) == 0) {
         $retour["succes"]=0;
         $retour["erreur"]="@& Pas de sous-champ 9b";
         return ($retour); 
    }
    $ID_ss_champ_9b=$tmp[0]["id"];
    
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_ss_champ_9b.'].set_valeur("'.$solde.'");');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_ss_champ_9b.'].validation();');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_ss_champ_9b.'].test_valeur();');
    
    // 4) On enregistre la notice
    array_push($retour["resultat"], 'this_formulator.enregistrer()');
    
    return($retour);
}


?>