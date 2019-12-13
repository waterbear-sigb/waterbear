<?php

/**
 * plugin_transactions_resas_affiche_liste_resas()
 * 
 * le plugin formate sert à générer un affichage. Il devra retourner qqchse comme nom_du_champ:valeur_duchamp|nom_du_champ2:valeur_du_champ2|...
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_transactions_resas_affiche_liste_resas ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $plugin_recherche=$parametres["plugin_recherche"];
    $plugin_formate=$parametres["plugin_formate"];
    $plugin_ajoute_ligne=$parametres["plugin_ajoute_ligne"];
    
    $ID_lecteur=$bureau["infos_lecteur"]["ID"];
    
    // 1) on recherche toutes les résas en cours de l'utilisateur
    $tmp=applique_plugin($plugin_recherche, array("ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"]!=1) {
        return($tmp);
    }
    $lignes_resas=$tmp["resultat"]["notices"];

    if ($nb_resas_dispos > 0) {
        $bureau["bool_resas_dispos"]=1;
    } else {
        $bureau["bool_resas_dispos"]=0;
    }

    $nb_resas_dispos=0;
    // 2) pour chaque résa, on formate et on affiche
    foreach ($lignes_resas as $ligne_resa) { // pour chaque résa
        //2.a) on formate : 
        $tmp=applique_plugin($plugin_formate, array("notice"=>$ligne_resa["xml"]));
        $ID_resa=$ligne_resa["ID"];
        $tmp_infos=$tmp["resultat"]["texte"];
        
        // 2.b) on extrait les informations de la chaine
        $infos=array("ID"=>$ID_resa);
        $tmp_infos2=explode("|", $tmp_infos);
        foreach ($tmp_infos2 as $tmp_infos3) { // pour chaque champ 
            $tmp_infos4=explode(":", $tmp_infos3, 2);
            $nom_champ=$tmp_infos4[0];
            $valeur_champ=$tmp_infos4[1];
            $infos[$nom_champ]=$valeur_champ;
        }

        
        // 2.c) on affiche
        $tmp=applique_plugin($plugin_ajoute_ligne, array("bureau"=>$bureau, "infos"=>$infos));
        $bureau=$tmp["resultat"]["bureau"];
        
        // 2.d) gestion de l'affichage des résas dispos dans le prêt
        if ($ligne_resa["a_etat"]==25) {
            $nb_resas_dispos++;
        }        
    }
    
    if ($nb_resas_dispos > 0) {
        $bureau["bool_resas_dispos"]=1;
    } else {
        $bureau["bool_resas_dispos"]=0;
    }

    $retour["resultat"]["bureau"]=$bureau;
    return($retour);
}


?>