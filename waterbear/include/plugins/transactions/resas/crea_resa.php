<?php

function plugin_transactions_resas_crea_resa ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["message"]="";
    $retour["resultat"]["reservable"]=1;
    
    $ID_doc=$parametres["ID_doc"];
    $ID_lecteur=$parametres["ID_lecteur"];
    $bib=$parametres["bib"];
    $validation_message=$parametres["validation_message"];
    $teste=$parametres["teste"]; // si vaut 1, on ne crée pas la résa
    $bool_reserver_disponibles=$parametres["bool_reserver_disponibles"];
    
    $plugin_recherche_exemplaires=$parametres["plugin_recherche_exemplaires"];
    $plugin_teste_dispo_long_terme=$parametres["plugin_teste_dispo_long_terme"];
    $plugin_teste_dispo_court_terme=$parametres["plugin_teste_dispo_court_terme"];
    $plugin_teste_quotas_doc=$parametres["plugin_teste_quotas_doc"];
    $plugin_teste_quotas_reservataire=$parametres["plugin_teste_quotas_reservataire"];
    $plugin_param_exemplaire=$parametres["plugin_param_exemplaire"];
    $plugin_param_resa=$parametres["plugin_param_resa"];
    $plugin_marcxml=$parametres["plugin_marcxml"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    
   
    
    $OK=1;
    $message="";
    
    // 0) on récupère $ID_famille
    $notice_lecteur=get_objet_by_id("lecteur", $ID_lecteur);
    $ID_famille=$notice_lecteur["a_id_famille"];
    
    // 1) Récupération des exemplaires
    $tmp=applique_plugin($plugin_recherche_exemplaires, array("ID_doc"=>$ID_doc));
    if ($tmp["succes"] == 0) {
        return($tmp);
    }
    $exemplaires=$tmp["resultat"]["notices"];
    $nb_exemplaires=$tmp["resultat"]["nb_notices"];
    
    // 2) lister les exemplaires réservables (en prêt ou dispos)
    $exemplaires_reservables=array();
    $exemplaires_reservables_dispos=array();
    $duree=0;
    foreach ($exemplaires as $idx_exemplaire => $exemplaire) {
        $etat=$exemplaire["a_etat"]; // pret ou dispo
        $en_pret=0;
        if ($etat == "pret") {
            $en_pret=1;
        }
        $reservable=0; // bool pour dispo court et long terme
        // Teste des QUOTAS
        if ($idx_exemplaire == 0) { // on ne teste les quotas que pour le 1er exemplaire
            // a) teste quotas doc (nb de résas faites par le lecteur pour ce type de doc)
            $tmp=applique_plugin($plugin_teste_quotas_doc, array("exemplaire"=>$exemplaire, "ID_lecteur"=>$ID_lecteur, "ID_famille"=>$ID_famille, "validation_message"=>$validation_message));
            if ($tmp["succes"] == 0) {
                return($tmp);
            }
            $duree=$tmp["resultat"]["duree"];
            $depassement_doc=$tmp["resultat"]["depassement"];
            
            
            // b) teste quotas réservataire (nb de lecteurs ayant réservé ce doc)
            $tmp=applique_plugin($plugin_teste_quotas_reservataire, array("exemplaire"=>$exemplaire, "ID_notice"=>$ID_doc));
            if ($tmp["succes"] == 0) {
                return($tmp);
            }
            $depassement_reservataire=$tmp["resultat"]["depassement"];
            
        } // fin du 1er exemplaire
        
        // c) teste de la dispo long terme
        $tmp=applique_plugin($plugin_teste_dispo_long_terme, array("exemplaire"=>$exemplaire));
        if ($tmp["succes"] == 0) {
            return($tmp);
        }
        if ($tmp["resultat"]["reservable"]==1) {
            $reservable=1;
        } else {
            $reservable=0;
        }
        
        // d) teste de la dispo court terme
        $tmp=applique_plugin($plugin_teste_dispo_court_terme, array("exemplaire"=>$exemplaire, "en_pret"=>$en_pret, "bool_reserver_disponibles"=>$bool_reserver_disponibles));
        if ($tmp["succes"] == 0) {
            return($tmp);
        }
        if ($tmp["resultat"]["reservable"]==1) { // Réservable
            $reservable=1;
        } elseif ($tmp["resultat"]["reservable"]==2) { // Réservable mais pas disponible (ex. en traitement)
            $reservable=1;
            $en_pret=1;
        } else {
            $reservable=0;
        }
        
        // e) on ajoute l'exemplaire à la liste des exemplaires reservables
        if ($reservable == 1) {
            array_push($exemplaires_reservables, $exemplaire);
            if ($en_pret==0) {
                array_push($exemplaires_reservables_dispos, $exemplaire);
            }
        }
        
    } // fin du pour chaque exemplaire
    
    // causes d'échec
    if ($validation_message != "oui") {
        if ($depassement_doc != "") {
            $retour["resultat"]["message"]="Vous ne pouvez reserver ce document car vous avez deja effectue trop de reservations";
            $retour["resultat"]["reservable"]=0;
            return($retour);
        }
        if ($depassement_reservataire != "") {
            $retour["resultat"]["message"]="Vous ne pouvez reserver ce document car il a deja ete reserve par trop de lecteurs";
            $retour["resultat"]["reservable"]=0;
            return($retour);
        }
        if (count($exemplaires_reservables) == 0) {
            $retour["resultat"]["message"]="Aucun exemplaire de ce document ne peut etre reserve";
            $retour["resultat"]["reservable"]=0;
            return($retour);
        }
        if (count($exemplaires_reservables_dispos) >= 1 AND $bool_reserver_disponibles == 0) {
            $retour["resultat"]["message"]="Au moins un exemplaire de ce document est disponible. Vous ne pouvez donc pas le reserver";
            $retour["resultat"]["reservable"]=0;
            return($retour);
        }
    }
    
    // Creéation de la réservation (sauf si teste)
    if ($teste != 1) {
        // 1) création des paramètres pour les champs exemplaires (champs répétables)
        $param_exes=array();
        $nouv_etat=10;
        if (count($exemplaires_reservables_dispos) > 0) {
            $exemplaires_reservables=$exemplaires_reservables_dispos;
            $nouv_etat=15;
        }
        foreach($exemplaires_reservables as $exemplaire_reservable) {
            $tmp=applique_plugin($plugin_param_exemplaire, array("exemplaire"=>$exemplaire_reservable));
            if ($tmp["succes"] == 0) {
                return($tmp);
            }
            array_push($param_exes, $tmp["resultat"]);
        }
        
        // 2) On récupère les paramètres de création de l'objet résa (sans les exemplaires)
        $date=date("Y-m-d");
        $date_fin=date("Y-m-d", time()+($duree*24*60*60));
        $tmp=applique_plugin($plugin_param_resa, array("ID_doc"=>$ID_doc, "ID_lecteur"=>$ID_lecteur, "date"=>$date, "date_fin"=>$date_fin, "bib"=>$bib, "etat"=>$nouv_etat));
        if ($tmp["succes"] == 0) {
            return($tmp);
        }
        $param_resa=$tmp["resultat"];
        
        //3) On injecte les paramètres d'exemplaires dans les paramètres de resa
        foreach($param_exes as $param_exe) {
            array_push($param_resa["definition"], $param_exe);
        }
        
        // 4) on génère l'objet marcxml correspondant à la notice résa
        $tmp=applique_plugin($plugin_marcxml, $param_resa);
        if ($tmp["succes"] == 0) {
            return($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
        
        // 5) On enregistre la notice dans la db
        $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice));
        if ($tmp["succes"] == 0) {
            return($tmp);
        }
        $ID_notice=$tmp["resultat"]["ID_notice"];
    } // fin du si teste != 1
    
    $retour["resultat"]["ID_notice"]=$ID_notice;
    $retour["resultat"]["reservable"]=1;
    return ($retour);
} // fin du plugin


?>