<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_exe_champ_500()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [auto_plugin_500_f] => plugin qui va générer le champ 500$f
 * @param [auto_plugin_500_g] => plugin qui va générer le champ 500$g
 * @param [auto_plugin_500_h] => plugin qui va générer le champ 500$h
 * 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_exe_champ_500 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    
    $prix_public=0;
    $tx_tva=0;
    $tx_remise=0;
    $prix_remise_deduite=0;
    $tva=0;
    $prix_ht=0;
    $remise=0;
    $quantite=1;
    $prix_remise_deduite_total=0;
    $tva_totale=0;
    $prix_ht_total=0;
    
    // On récupère prix public, tx remise et tx tva
    $liste_b=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "b");
    $liste_c=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "c");
    $liste_d=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "d");
    $liste_e=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "e");
    $liste_f=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "f");
    $liste_g=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "g");
    $liste_h=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "h");
    $liste_i=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "i");
    $liste_j=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "j");
    $liste_k=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "k");
    
    if (count($liste_b) > 0) {
        $quantite=$liste_b[0]["valeur"];
    }
    
    if (count($liste_c) > 0) {
        $prix_public=$liste_c[0]["valeur"];
    } 
    
    if (count($liste_d) > 0) {
        $tx_tva=$liste_d[0]["valeur"];
    } 
    
    if (count($liste_e) > 0) {
        $tx_remise=$liste_e[0]["valeur"];
    } 
    
    // on calcule les valeurs
    $remise=($prix_public*$tx_remise)/100;
    $prix_remise_deduite=$prix_public-$remise;
    $tva=($prix_public*$tx_tva)/100;
    $prix_ht=$prix_public-$tva;
    $prix_remise_deduite_total=$prix_remise_deduite*$quantite;
    $tva_totale=$tva*$quantite;
    $prix_ht_total=$prix_ht*$quantite;
    
    // on supprime les champs
    $liste_a_supprimer=array_merge ($liste_f, $liste_g, $liste_h, $liste_i, $liste_j, $liste_k);
    foreach ($liste_a_supprimer as $a_supprimer) {
        $_SESSION["operations"][$ID_operation]["formulator"]->delete_element($a_supprimer["id"]);
        array_push($retour["resultat"], 'this_formulator.delete_element('.$a_supprimer["id"].', '.$ID_element.');');
    }
    
    // on recrée les champs
    $tmp=applique_plugin($parametres["auto_plugin_500_f"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $tmp["resultat"]["valeur"]=round($prix_remise_deduite,2);
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    
    $tmp=applique_plugin($parametres["auto_plugin_500_g"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $tmp["resultat"]["valeur"]=round($tva,2);
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    
    $tmp=applique_plugin($parametres["auto_plugin_500_h"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $tmp["resultat"]["valeur"]=round($prix_ht,2);
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    
    $tmp=applique_plugin($parametres["auto_plugin_500_i"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $tmp["resultat"]["valeur"]=round($prix_remise_deduite_total,2);
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    
    $tmp=applique_plugin($parametres["auto_plugin_500_j"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $tmp["resultat"]["valeur"]=round($tva_totale,2);
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    
    $tmp=applique_plugin($parametres["auto_plugin_500_k"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $tmp["resultat"]["valeur"]=round($prix_ht_total,2);
    $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
    array_push($retour["resultat"],$infos);
    array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
    
    
    
    return ($retour); 
}


?>