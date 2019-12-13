<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/google_map_geocoding.php");

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_champ_200()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [autoplugin_120_l]
 * @param 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_geolocalisation ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    
    // 1) On récupère les infos d'adresse
    $ss_champ_a="";
    $ss_champ_b="";
    $ss_champ_c="";
    $ss_champ_d="";
    $liste_a=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "a");
    $liste_b=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "b");
    $liste_c=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "c");
    $liste_d=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "d");
    if (count($liste_a) > 0) {
        $ss_champ_a=$liste_a[0]["valeur"];
    }
    if (count($liste_b) > 0) {
        $ss_champ_b=$liste_b[0]["valeur"];
    }
    if (count($liste_c) > 0) {
        $ss_champ_c=$liste_c[0]["valeur"];
    }
    if (count($liste_d) > 0) {
        $ss_champ_d=$liste_d[0]["valeur"];
    }
    $chaine=$ss_champ_a." ".$ss_champ_b." ".$ss_champ_c." ".$ss_champ_d;
    
    // 2) Géolocalisation
    $google_map = new google_map_geocoding(array());
    $google_map->set_adresse($chaine);
    $coordonnees=$google_map->geocode();
    //array_push($retour["resultat"], "alert ('$toto');");
    
    // 4) on supprime le ss-champ $l
    $liste_l=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "l");
    if (count($liste_l) > 0) {
        $ID_ss_champ_l=$liste_l[0]["id"];
        $_SESSION["operations"][$ID_operation]["formulator"]->delete_element($ID_ss_champ_l);
        array_push($retour["resultat"], 'this_formulator.delete_element('.$ID_ss_champ_l.', '.$ID_element.');');
    }
    
    // 5) on crée un ss-champ $l
    $tmp=applique_plugin($parametres["autoplugin_120_l"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    } 
    
    $tmp["resultat"]["valeur"]=$coordonnees;
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    

    return ($retour); 
}


?>