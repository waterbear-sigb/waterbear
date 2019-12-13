<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_champ_461()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_operation]
 * @param [ID_element]
 * @param [plugin_exporte_grille] => le plugin utilisé pour convertir la grille de saisie en notice xml
 * @param [plugin_fusion] => plugin utilisé pour fusionner les notices
 * @param [plugin_genere_grille] => génère une grille à partir de la notice fusionnée et de la définition de la grille 
 * @param [type_obj] => type d'objet lié (normalement le même que la notice elle-même)
 * @param SOIT [ID_notice_liee] => ID de la notice liée
 * @param SOIT [ss_champ_lien] => nom du ss-champ de lien (généralement $3) pour récupérer n° de notice
 * 
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_champ_461 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"]; // ID du champ 461 dans formulator
    $ID_notice=$_SESSION["operations"][$ID_operation]["ID_notice"]; // si modification de notice
    
    // 1) On génère la notice correspondant à la grille en cours de saisie
    $tmp=applique_plugin($parametres["plugin_exporte_grille"], array("ID_operation"=>$ID_operation));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice_a=$tmp["resultat"]["notice"];
    
    // 2) On récupère la notice correspondant au champ lié
    if ($ID_notice_liee == "") {
        $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, $parametres["ss_champ_lien"]);
        if (count($liste_ss_champs) == 0) {
            $retour["succes"]=0;
            $retour["erreur"]="Impossible de trouver la notice de lien";
            return ($retour);
        }
        $ID_notice_liee=$liste_ss_champs[0]["valeur"];
    }
    $notice_b=get_objet_xml_by_id($parametres["type_obj"], $ID_notice_liee);
    
    // 3) on fusionne les notices
    $tmp=applique_plugin($parametres["plugin_fusion"], array("xml_a"=>$notice_a, "xml_b"=>$notice_b, "type_obj"=>$parametres["type_obj"], "format_retour"=>"domxml", "ID_notice"=>$ID_notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice_fusion=$tmp["resultat"]["notice"];
    
    // 4) On génère une nouvelle grille
    $tmp=applique_plugin($parametres["plugin_genere_grille"], array("notice"=>$notice_fusion));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $grille=$tmp["resultat"];
    
    $_SESSION["operations"][$ID_operation]["formulator"]->init_formulator($grille);
    array_push ($retour["resultat"], "this_formulator.delete_formulaire();");
    array_push ($retour["resultat"], $grille["onglets"]);
    array_push ($retour["resultat"], "this_formulator.genere_formulaire(param);");
    
    
    return ($retour);
    
} // fin du plugin

?>