<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_valide_paiement()
 * 
 * Ce plugin est utilisé pour valider un paiement.
 * 1) Il crée l'objet paiement 
 * 2) Il calcule le solde
 * 3) Il met à jour le champ 610 du lecteur (ss-champ 9b avec le solde et ermise à 0 des autres)
 * 
 * @param mixed $parametres
 * 
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [plugin_creation_paiement] => plugin pour créer l'objet paiement à partir des infos des ss-champs
 * @param [plugin_calcule_solde] => pour calculer le solde du lecteur
 * @param [liste_ss_champs_RAZ] => liste des ss-champs à réinitialiser après la transaction (avec valeur)
 *                             [c]=>0, [mode]=>"", ...         
 * 
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_valide_paiement ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $plugin_creation_paiement=$parametres["plugin_creation_paiement"];
    $plugin_calcule_solde=$parametres["plugin_calcule_solde"];
    $ID_element=$parametres["ID_element"];
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $liste_ss_champs_RAZ=$parametres["liste_ss_champs_RAZ"];
    
    // 1) On vérifie qu'on a bien un n° de notice (donc que la notice a été sauvegardée)
    $ID_notice=$_SESSION["operations"][$ID_operation]["ID_notice"];
    if ($ID_notice == "") {
         $retour["succes"]=0;
         $retour["erreur"]="@&Vous devez au prealable enregistrer la notice";
         return ($retour); 
    }
    
    
    // 2) on appelle le plugin pour créer l'objet paiement
    $tmp=applique_plugin($plugin_creation_paiement, array("ID_element"=>$ID_element, "ID_operation"=>$ID_operation));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    // 2) On calcule le solde
    
    $tmp=applique_plugin($plugin_calcule_solde, array("ID_lecteur" => $ID_notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $solde=$tmp["resultat"]["solde"];
    
    
    // 3) On met à jour $9b avec le solde. Pour tous les autres ss-champs on RAZ
    $liste_ss_champs=$formulator->get_ss_champs_by_nom($ID_element, "");
    foreach ($liste_ss_champs as $ss_champ) {
        $nom=$ss_champ["nom"];
        $id=$ss_champ["id"];
        if ($nom == "9b") {
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$id.'].set_valeur("'.$solde.'");');
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$id.'].validation();');
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$id.'].test_valeur();');
        } else {
            foreach ($liste_ss_champs_RAZ as $ss_champ_RAZ => $valeur_RAZ) {
                if ($nom == $ss_champ_RAZ) {
                    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id.'].set_valeur("'.$valeur_RAZ.'");');
                    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id.'].validation();');
                }
            }
        }
        
    }
    
    // 4) On enregistre la notice
    array_push($retour["resultat"], 'this_formulator.enregistrer()');
    
    // 5) Retour
    return ($retour);
}

?>