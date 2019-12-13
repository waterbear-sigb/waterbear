<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_catalogage_direct ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
        
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$infos["ID_parent"];
    
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $type_obj=$parametres["type_obj"];
    $plugin_modif=$parametres["plugin_modif"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $bool_maj_champ_lien=$parametres["bool_maj_champ_lien"]; // si vaut 1, le champ de lien sera mis à jour après que la notice liée aura été modifiée
    $bool_autorise_modif_champ_non_lie=$parametres["bool_autorise_modif_champ_non_lie"]; // si vaut 1 on peut modifier des ss-champs même si aucune notice liée n'existe
    
    $valeur=$_REQUEST["valeur"];
    //$intitule=$_REQUEST["intitule"];
    
    // 1) on récupère le n° de notice liée
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_parent, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $ID_notice_liee=$liste_ss_champs[0]["valeur"];
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    
    // Si ID_notice liée == "" (champ vide) => on se contente de mettre à jour le formulator, mais on ne modifie aucun aobjet
    // NON !!! On renvoie une erreur et on RAZ le champ
    // SI !! mais au choix selon $bool_autorise_modif_champ_non_lie
    if ($ID_notice_liee == "") {
        if ($bool_autorise_modif_champ_non_lie == 1) {
            $update=array("valeur"=>$valeur);
            $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
        } else {
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");');
            array_push($retour["resultat"], "alert(\"Vous ne pouvez mettre a jour ce sous-champ car cet objet n'a pas ete cree \")");
        }
        return ($retour);
    }
    
    // 2) On récupère la notice liée
    $notice=get_objet_xml_by_id($type_obj, $ID_notice_liee);
    
    // 3) on maj la notice
    //$tmp=applique_plugin($plugin_modif, array("notice"=>$notice, "intitule"=>$intitule, "valeur"=>$valeur));
    $tmp=applique_plugin($plugin_modif, array("notice"=>$notice, "valeur"=>$valeur));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 4) on enregistre la notice
    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID_notice_liee));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    // 5) on maj le ss-champ coté serveur (seulement si pas eu d'erreur avant)
    $update=array("valeur"=>$valeur);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    // 6) si $bool_maj_champ_lien == 1, on maj le champ
    if ($bool_maj_champ_lien == 1) {
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
    }
    
    
    return ($retour);
    
}



?>