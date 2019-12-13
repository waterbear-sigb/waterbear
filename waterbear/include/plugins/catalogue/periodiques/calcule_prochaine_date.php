<?php

function plugin_catalogue_periodiques_calcule_prochaine_date ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["date"]=date("Y-m-d"); // valeur du jour par défaut
    
    $date_dernier_no=$parametres["date_dernier_no"];
    $parait_jours=$parametres["parait_jours"];
    $parait_mois=$parametres["parait_mois"];
    $mode_parution=$parametres["mode_parution"];
    
   
    $liste_jours=explode ("|", $parait_jours);
    $liste_mois=explode ("|", $parait_mois);

    if ($parait_jours == "") {
        $liste_jours=array(1,2,3,4,5,6,7);
    }
    if ($parait_mois == "") {
        $liste_mois=array(1,2,3,4,5,6,7,8,9,10,11,12);
    }

    
    // 1) on détermine les délais de parution en fonction du mode de parution
    $delais=30*24*60*60; // par défaut 30 jours (même irrégulier); 
    if ($mode_parution == "HEBD") {
        $delais=7*24*60*60;
    } elseif ($mode_parution == "QUOT") {
        $delais=1*24*60*60;
    } elseif ($mode_parution == "BIMENS") {
        $delais=2*7*24*60*60;
    } elseif ($mode_parution == "MENS") {
        $delais=28*24*60*60; // pour les mensuels on part sur 28 jours, puis on ajuste jusqu'à tomber sur le même jour de la semaine
    }
    
  
    // 2) on convertit la date en timestamp
    $tmp=explode("-", $date_dernier_no);
    if (count($tmp) != 3) {
        return ($retour);
    }
    $annee_dernier_no=$tmp[0];
    $mois_dernier_no=$tmp[1];
    $jour_dernier_no=$tmp[2];
    $timestamp_dernier_no=mktime(0, 0 ,0, $mois_dernier_no, $jour_dernier_no, $annee_dernier_no);
    $jour_semaine_ancien_no=date("N", $timestamp_dernier_no);
    
    // 3) on ajoute le délais
    $timestamp_prochain_no=$timestamp_dernier_no;
    $bool_continue=1;
    while ($bool_continue != 0) {
        $bool_continue++;
        $timestamp_prochain_no+=$delais;
        $jour_prochain_no=date("N", $timestamp_prochain_no); // de 1 à 7 sans "0"
        $mois_prochain_no=date("n", $timestamp_prochain_no); // de 1 à 12 sans "0" 
        if ($mode_parution == "MENS") {
            $idx=0;
            while (($jour_prochain_no != $jour_semaine_ancien_no) OR $idx >= 7) {
                $idx++;
                $timestamp_prochain_no+=60*60*24;//on rajoute un jour
                $jour_prochain_no=date("N", $timestamp_prochain_no); // de 1 à 7 sans "0"
                $mois_prochain_no=date("n", $timestamp_prochain_no); // de 1 à 12 sans "0" 
            }
        }
        if (!in_array($jour_prochain_no, $liste_jours) OR !in_array($mois_prochain_no, $liste_mois)) {
            if ($bool_continue > 15) {
                $retour["resultat"]["date"]=date("Y-m-d");
                return($retour);
            }
            continue;
        }
        $retour["resultat"]["date"]=date("Y-m-d", $timestamp_prochain_no);  
        return ($retour);      
    }
    
    // TODO !!! aligner sur le jour de parution, pour éviter le décallage progressif des mensuels
    
    return ($retour);

}



?>