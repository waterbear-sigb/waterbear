<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_cmde_maj_etat ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    //$ID_champ_700=$infos["ID_parent"];
    
    $plugin_recherche_lignes_commande=$parametres["plugin_recherche_lignes_commande"];
    $plugin_valide=$parametres["plugin_valide"];
    $plugin_solde=$parametres["plugin_solde"];
    $plugin_notice_2_db_exemplaire=$parametres["plugin_notice_2_db_exemplaire"];
    $plugin_calcule_totaux=$parametres["plugin_calcule_totaux"];
    
    $etat_new=$_REQUEST["valeur"];
    
    // 1) On récupère ID_notice commande
    $ID_commande=$_SESSION["operations"][$ID_operation]["ID_notice"];
    if ($ID_commande == "") {
         $retour["succes"]=0;
         $retour["erreur"]="@&Vous devez au prealable enregistrer la commande";
         return ($retour); 
    }
    
    // 2) On récupère l'état existant
    //$liste_5=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "5");
    //$etat=$liste_5[0]["valeur"];
//dbg_log($_SESSION["operations"][$ID_operation]["formulator"]->onglets);
//dbg_log($_SESSION["operations"][$ID_operation]["formulator"]->elements);

    $ss_champ_700_5=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champ_simple("700", "5");
    $ss_champ_200_d=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champ_simple("200", "d");
    $ss_champ_200_e=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champ_simple("200", "e");
    $infos_ss_champ=$_SESSION["operations"][$ID_operation]["formulator"]->get_infos_element($ss_champ_700_5["id"]);
    $ID_champ_700=$infos_ss_champ["ID_parent"];
    $etat=$ss_champ_700_5["valeur"];
//array_push($retour["resultat"], 'alert ("'.$etat.'");');
//return ($retour);
    
    // 3) Vérifier qu'on peut bien passer au nouvel état, en fonction de l'ancien
    $avancement_etat=array("cours"=>10, "valide"=>20, "solde"=>30);
    if ($avancement_etat[$etat_new] < $avancement_etat[$etat]) {
        $retour["succes"]=0;
        $retour["erreur"]="Vous ne pouvez pas passer de l'etat $etat a l'etat $etat_new";
        return ($retour);
    }
    
    
    // 4) Récupérer les lignes de commande
    $tmp=applique_plugin ($plugin_recherche_lignes_commande, array("ID_commande"=>$ID_commande));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $liste_lignes_commande=$tmp["resultat"]["notices"];
    
    // 5) pour chaque ligne de commande
    foreach ($liste_lignes_commande as $ligne_commande) {
        // 5.a on modifie la ligne de commande (exemplaire)
        $notice_ligne=$ligne_commande["xml"];
        $montant_commande=$ligne_commande["a_prix_remise_total"];
        $nb_commande=$ligne_commande["a_nb_exe"];
        $ID_ligne_commande=$ligne_commande["ID"];
        if ($etat_new == "valide") {
            $tmp=applique_plugin ($plugin_valide, array("notice"=>$notice_ligne, "montant_commande"=>$montant_commande, "nb_commande"=>$nb_commande));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
        } elseif ($etat_new == "solde") {
            $tmp=applique_plugin ($plugin_solde, array("notice"=>$notice_ligne));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
        }
        $notice_ligne=$tmp["resultat"]["notice"];
        
        // 5.b) on enregistre la ligne de commande dans la db
        $tmp=applique_plugin ($plugin_notice_2_db_exemplaire, array("notice"=>$notice_ligne, "ID_notice"=>$ID_ligne_commande));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
    }
    
    // 6) On calcule les totaux
    $tmp=applique_plugin ($plugin_calcule_totaux, array("ID_commande"=>$ID_commande));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $totaux=$tmp["resultat"]["variables"];
    
    // 7) On maj la notice
    // 7.a) le champ 700 (infos budgétaires et état)
    if ($etat_new == "valide") {
        $a_modifier=array("5"=>"valide", "a"=>$totaux["prix_remise"], "b"=>$totaux["prix_remise"], "c"=>"0", "d"=>$totaux["nb_exe"], "e"=>$totaux["nb_exe"], "f"=>"0");
    } elseif ($etat_new == "solde") {
        $a_modifier=array("5"=>"solde", "b"=>"0", "e"=>"0");
    }
    
    //$_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ss_champ_700_5["id"], array("valeur"=>$etat_new));
    
    foreach ($a_modifier as $nom_ss_champ => $valeur_ss_champ) {
        $liste=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_champ_700, $nom_ss_champ);
        if (count($liste) != 1) {
            $retour["succes"]=0;
            $retour["erreur"]="Il devrait y avoir 1 et 1 seul sous champ $nom_ss_champ dans le champ 700 de la commande $ID_commande";
            return ($retour);
        }
        $ID_ss_champ=$liste[0]["id"];
        $update=array("valeur"=>$valeur_ss_champ);
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_ss_champ, $update);
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_ss_champ.'].set_valeur("'.$valeur_ss_champ.'");');
    }
    
    // 7.b) champ 200 (date de validation et solde)
    if ($etat_new == "valide") {
        $update=array("valeur"=>date("Y-m-d"));
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ss_champ_200_d["id"], $update);
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ss_champ_200_d["id"].'].set_valeur("'.date("Y-m-d").'");');
    } elseif ($etat_new == "solde") {
        $update=array("valeur"=>date("Y-m-d"));
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ss_champ_200_e["id"], $update);
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ss_champ_200_e["id"].'].set_valeur("'.date("Y-m-d").'");');
    }
    
    // 8) On enregistre la notice
    array_push($retour["resultat"], 'this_formulator.enregistrer()');
    array_push($retour["resultat"], 'alert ("OK");');
    
    return ($retour);
    
    

    
    
    
}



?>