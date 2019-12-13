<?php

function plugin_transactions_resas_messages_resas ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["message"]="";
    
    $plugin_recherche=$parametres["plugin_recherche"];
    $plugin_formate_resa=$parametres["plugin_formate_resa"];
    $plugin_formate_lecteur=$parametres["plugin_formate_lecteur"];
    $plugin_formate_lecteur_mail=$parametres["plugin_formate_lecteur_mail"];
    $plugin_mail=$parametres["plugin_mail"];
    $plugin_maj_resa=$parametres["plugin_maj_resa"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $chemin_intitule_lettre=$parametres["chemin_intitule_lettre"];
    $bloc_avant=$parametres["bloc_avant"];
    $bloc_apres=$parametres["bloc_apres"];
    $politique=$parametres["politique"]; // lettre, mail, lettre_et_mail
    
    $panier=$parametres["panier"]; // restriction éventuelle de la recherche à un panier
    
    $date_message=date("Y-m-d");
    
    // 1) on récupère les résas pour lesquelles envoyer un message
    $tmp=applique_plugin($plugin_recherche, array("panier"=>$panier));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $liste_resas=$tmp["resultat"]["notices"];
    $nb_resas=$tmp["resultat"]["nb_notices"];
    
    // 2) on regroupe les résas par lecteur
    $bloc_resas="";
    $bloc_lecteur="";
    $last_lecteur=0;
    //foreach ($liste_resas as $idx_resa => $ligne_resa) {
      for ($idx_resa=0 ; $idx_resa <= $nb_resas ; $idx_resa++) { // pour chaque résa !! et on rajoute une ligne à la fin pour l'envoi du dernier message'
        $ligne_resa=$liste_resas[$idx_resa]; 
        $ID_lecteur=$ligne_resa["a_id_lecteur"];
        $resa=$ligne_resa["xml"];
        $ID_resa=$ligne_resa["ID"];
        
        // si il faut vider le cache
        if ($idx_resa == $nb_resas OR ($ID_lecteur != $last_lecteur AND $last_lecteur != 0)) { // il faut vider le cache (résas précédentes) si on change de lecteur ou qu'on arrive à la fin de la liste
            if ($bloc_resas != "") {
                $mail="";
                // on génère le bloc lecteur et le mail
                $tmp=applique_plugin($plugin_formate_lecteur, array("ID_notice"=>$last_lecteur, "type_doc"=>"lecteur"));
                $bloc_lecteur=$tmp["resultat"]["texte"];
                $tmp=applique_plugin($plugin_formate_lecteur_mail, array("ID_notice"=>$last_lecteur, "type_doc"=>"lecteur"));
                $mail=$tmp["resultat"]["texte"];
                
                // on formate l'ensemble du message
                $message=get_intitule("", $chemin_intitule_lettre, array("bloc_lecteur"=>$bloc_lecteur, "bloc_resas"=>$bloc_resas, "bloc_avant"=>$bloc_avant, "bloc_apres"=>$bloc_apres));
                
                // envoi du mail ou du courrier
                if (($politique=="mail" OR $politique=="lettre_et_mail") AND $mail != "") { // envoi d'un mail'
                    $tmp=applique_plugin($plugin_mail, array("body"=>$message, "to"=>$mail));
                }
                if ($politique=="lettre" OR $politique=="lettre_et_mail" OR $mail == "") { // envoi d'un courrier
                    $retour["resultat"]["message"].=$message."<div style='page-break-after:always;'></div>";
                }
                
                // RAZ
                $bloc_resas="";
            }
            
 
        }
        $last_lecteur=$ID_lecteur;
        // on ajoute la résa au cache
        if ($resa != "") {
            $tmp=applique_plugin($plugin_formate_resa, array("notice"=>$resa));
            $bloc_resas.=$tmp["resultat"]["texte"];
            
            // on maj la resa
            $tmp=applique_plugin($plugin_maj_resa, array("notice"=>$resa, "date_message"=>$date_message));
            $resa=$tmp["resultat"]["notice"];
            
            $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$resa, "ID_notice"=>$ID_resa));
        }
        
    } // fin du pour chaque résa
    
  
  return ($retour);
}


?>