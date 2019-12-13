<?php

function plugin_transactions_bureau_get_liste_prets ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $commandes=$bureau["commandes"];
    $arbre=$bureau["arbre"];
    $plugin_recherche_prets=$parametres["plugin_recherche_prets"];
    //$plugin_traite_element=$parametres["plugin_traite_element"];
    $plugin_pret_2_quota=$parametres["plugin_pret_2_quota"];
    $plugin_ajoute_ligne_pret=$parametres["plugin_ajoute_ligne_pret"];
    $ID_lecteur=$parametres["ID_lecteur"];
    $nb_lignes_max=$parametres["nb_lignes_max"]; // nombre max de lignes à afficher
    
    if ($nb_lignes_max == "") {
        $nb_lignes_max=100;
    }
    
    // 1) on recherche la liste des prêts en cours de ce lecteur
    $tmp=applique_plugin ($plugin_recherche_prets, array("ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $liste_prets=$tmp["resultat"]["notices"];
    
    // 2) Pour chaque ligne on applique un plugin de traitement 
    $idx=0;
    $bool_retard=0;
    $now=time();
    //$arbre=array();

    foreach ($liste_prets as $ligne) {
        $idx++;
        //dbg_log("@@@@ PRET $idx");
        //$m1=microtime(true);
        // a) on génère titre_clicable et cab_clicable
        $titre=$ligne["a_titre_biblio"];
        $ID_biblio=$ligne["a_id_biblio"];
        $cab=$ligne["a_cab_exe"];
        $ID_exe=$ligne["a_id_exe"];
        $ligne["a_titre_clicable"]="<span oncontextmenu=\"return(fn_mc(event, 'biblio', $ID_biblio, 1));\" onclick=\"return(fn_mc(event, 'biblio', $ID_biblio, 1));\" class=\"objet_cliquable\"> $titre </span>";
        $ligne["a_cab_clicable"]="<span oncontextmenu=\"return(fn_mc(event, 'exemplaire', $ID_exe, 1));\" onclick=\"return(fn_mc(event, 'exemplaire', $ID_exe, 1));\" class=\"objet_cliquable\"> $cab </span>";
        
        // b) analyse_retard
        if ($bool_retard == 0) {
            $timestamp_retour=date_us_2_timestamp($ligne["a_date_retour_prevu"]);
            if ($timestamp_retour < $now) {
                $bool_retard=1;
            }
        }
        
        // c) maj arbre
        $bureau_bidon=array("infos_pret"=>$ligne, "infos_exemplaire"=>array(), "infos_biblio"=>array());
        $tmp=applique_plugin($plugin_pret_2_quota, array("bureau"=>$bureau_bidon, "arbre"=>$arbre, "criteres"=>$bureau["infos_quotas"]["criteres"]));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $arbre=$tmp["resultat"]["arbre"];        
        
        // d) ajoute ligne pret
        if ($idx < $nb_lignes_max) {
            $bureau_bidon=array("infos_pret"=>$ligne, "infos_exemplaire"=>array(), "infos_biblio"=>array(), "commandes"=>$commandes);
            $tmp=applique_plugin($plugin_ajoute_ligne_pret, array("bureau"=>$bureau_bidon));
            if ($tmp["succes"] != 1) {
                return($tmp);
            }
            $commandes=$tmp["resultat"]["bureau"]["commandes"];  
        }
        
        
        
        //$bureau["tmp_infos_liste_prets_elem"]=$ligne;
        //$tmp=applique_plugin($plugin_traite_element, array("bureau"=>$bureau));
        //$m2=microtime(true);
        //$m=$m2-$m1;
        //dbg_log("@@@ FIN PRET $idx : $m : RAM : ".memory_get_usage(true));
        //if ($tmp["succes"] != 1) {
            //return ($tmp);
        //}
        //$bureau=$tmp["resultat"]["bureau"];
    }
    
    
    
    
    
    
    
    $bureau=array("arbre"=>$arbre, "commandes"=>$commandes, "bool_retard"=>$bool_retard);
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);
}

?>