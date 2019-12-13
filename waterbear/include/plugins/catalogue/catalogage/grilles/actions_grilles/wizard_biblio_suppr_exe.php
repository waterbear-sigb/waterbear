<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_biblio_suppr_exe ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
        
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$parametres["infos"]["ID_parent"];
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $plugin_delete_notice_exemplaire=$parametres["plugin_delete_notice_exemplaire"];
    $plugin_enregistrer_notice=$parametres["plugin_enregistrer_notice"];
    $message_alerte_pilon=$parametres["message_alerte_pilon"];
    
 
    // 0) On enregistre la notice
    $tmp=applique_plugin($plugin_enregistrer_notice, array("ID_operation"=>$ID_operation));
    if ($tmp["succes"] != 1) {
        return($tmp);
    }
    
    // 1) On récupère l'ID du ss-champ de lien de ce champ (généralement $3)
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    $valeur_ss_champ_lien=$liste_ss_champs[0]["valeur"];
    
    // 1bis) si champ vierge, on se contente de supprimer le champ
    if ($valeur_ss_champ_lien==0) {
        $_SESSION["operations"][$ID_operation]["formulator"]->delete_element ($ID_element);
        $retour["resultat"][0]='this_formulator.delete_element('.$ID_element.', '.$ID_parent.');';
        return ($retour);
    }
    
    //2) on applique le plugin de suppression de l'exemplaire
    $tmp=applique_plugin($plugin_delete_notice_exemplaire, array("ID"=>$valeur_ss_champ_lien));
    if ($tmp["succes"] != 1) {
        $message=$tmp["erreur"];
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    
    //3) si exe supprimé on efface le ss-champ
    if ($tmp["resultat"]["ID_notice"]==0) {
        $_SESSION["operations"][$ID_operation]["formulator"]->delete_element ($ID_element);
        $retour["resultat"][0]='this_formulator.delete_element('.$ID_element.', '.$ID_parent.');';
        return ($retour);    
        
    } else { // si notice non supprimée
        //$message=utf8_encode("Cet exemplaire ne peut être simplement supprimé car il est lié à des prêts ou des réservations. A la place, Waterbear lui associe le statut PILON"); // temp : utiliser un intitulé
        array_push($retour["resultat"], 'alert("'.$message_alerte_pilon.'")');
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
        return ($retour);
    }
    
    return ($retour);
    
    
}


?>