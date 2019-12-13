<?php

/**
 * plugin_transactions_bureau_traite_abonnements()
 * 
 * Ce plugin analyse les abonnements et les quotas d'une carte.
 * Il retourne une liste éventuelle de messages d'erreurs (abonnement dépassé, quota inexistant...)
 * et l'arbre vierge des quotas, obtenu en fusionnant les différents quotas
 * <NON> Si un abonnement est dépassé, les différents quotas qui en dépendent auront une clef [_bloque]==1 </NON>
 * ** modif 15/04/2015 ** on ne gère plus le _bloque=1 car c'est ingérable si on veut forcer le prêt d'un abonnement dépassé
 * ** c'est géré au moment du passage de la carte lecteur : si on décide de forcer le prêt à ce moment il n'y aura pas d'avertissement au moment du prêt
 * 
 * le plugin vérifie égalemetn que les dates d'abonnements du chef de famille ne sont pas dépassées
 * Envoie également un message d'erreur
 * 
 * @param mixed $parametres
 * @param [infos_abos]
 * @param [infos_quotas]
 * @param [dates_abos_famille] => chaine concaténant les dates d'abonnement du chef de famille
 * @param [message_abo_depasse] => message à afficher si l'abonnement est dépassé
 * @param [code_abo_depasse] => niveau d'erreur à associer au message
 * @param [message_quota_inexistant] => message à afficher si le code quota n'est pas défini
 * @param [code_quota_inexistant] => niveau d'erreur à associer au message
 * @param [message_abo_depasse_famille] => message à afficher si l'abonnement est dépassé (famille)
 * @param [code_abo_depasse_famille] => niveau d'erreur à associer au message (famille)
 * @param [code_abo_bientot] => niveau d'erreur pour un abonnement bientôt dépassé
 * @param [message_abo_bientot] =>message d'erreur pour un abonnement bientôt dépassé
 * @param [message_abo_bientot_famille] =>idem pour abo famille
 * @param [delai_abo_bientot] => nombre de jour avant fin de l'abo pour afficher que l'abo sera bientôt dépasse''
 * 
 * 
 * @return array
 * @return [messages] => liste de messages à afficher avec pour chacun [code] => niveau d'erreur et [message] => message à proprement parler
 * @return [arbre] => arbre des quotas vierge, mais enrichi avec des clefs [_compteur]=0 et [_bloque]
 */
function plugin_transactions_bureau_traite_abonnements ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["messages"]=array();
    $retour["resultat"]["arbre"]=array();
    //$retour["resultat"]["code_carte"]="1";
    
    $dates_abos_famille=$parametres["dates_abos_famille"];
    $infos_abos=$parametres["infos_abos"];
    $infos_quotas=$parametres["infos_quotas"];
    $message_abo_depasse=$parametres["message_abo_depasse"];
    $code_abo_depasse=$parametres["code_abo_depasse"];
    $message_quota_inexistant=$parametres["message_quota_inexistant"];
    $code_quota_inexistant=$parametres["code_quota_inexistant"];
    $message_abo_depasse_famille=$parametres["message_abo_depasse_famille"];
    $code_abo_depasse_famille=$parametres["code_abo_depasse_famille"];
    $code_abo_bientot=$parametres["code_abo_bientot"];
    $message_abo_bientot=$parametres["message_abo_bientot"];
    $message_abo_bientot_famille=$parametres["message_abo_bientot_famille"];
    $delai_abo_bientot=$parametres["delai_abo_bientot"];
    
    
    // abonnements famille
    $dates_abos_famille_array=explode(" ", $dates_abos_famille);
    foreach ($dates_abos_famille_array as $date) {
        $date=trim($date);
        if ($date != "") {
            $timestamp_fin=date_us_2_timestamp($date);
            if ($timestamp_fin < time()) { 
                array_push ($retour["resultat"]["messages"], array("code"=>$code_abo_depasse_famille, "message"=>get_intitule("", $message_abo_depasse_famille, array("code_abo"=>"", "date_fin"=>$date))));
            } else if ($timestamp_fin-($delai_abo_bientot*24*60*60) < time()) {
                array_push ($retour["resultat"]["messages"], array("code"=>$code_abo_bientot, "message"=>get_intitule("", $message_abo_bientot_famille, array("code_abo"=>"", "date_fin"=>$date))));
            }
        }
    }
    
    foreach ($infos_abos as $abo) { // pour chaque abonnement...
        $code_abo=$abo["a_abo"];
        $fin=$abo["a_fin"];
        $code_quota=$abo["a_quota"];
        // 1) on regarde si l'abonnement est dépassé
        $timestamp_fin=date_us_2_timestamp($fin);
        $bool_abo_depasse=0;
        if ($timestamp_fin < time()) { 
            array_push ($retour["resultat"]["messages"], array("code"=>$code_abo_depasse, "message"=>get_intitule("", $message_abo_depasse, array("code_abo"=>$code_abo, "date_fin"=>$fin))));
            $bool_abo_depasse=1;
        } else if ($timestamp_fin-($delai_abo_bientot*24*60*60) < time()) {
            array_push ($retour["resultat"]["messages"], array("code"=>$code_abo_bientot, "message"=>get_intitule("", $message_abo_bientot, array("code_abo"=>$code_abo, "date_fin"=>$fin))));
        }
        
        // 2) on récupère le quota
        if (! isset($infos_quotas["arbres"][$code_quota])) {
            array_push ($retour["resultat"]["messages"], array("code"=>$code_quota_inexistant, "message"=>get_intitule("", $message_quota_inexistant, array("code_quota"=>$code_quota))));
        } else {
            $quota=$infos_quotas["arbres"][$code_quota];
            $quota=plugin_transactions_bureau_traite_abonnements_init_quota ($quota, $bool_abo_depasse);
            $retour["resultat"]["arbre"]=array_merge_recursive($retour["resultat"]["arbre"], $quota);
        }  
    } // fin du pour chaque abonnement
    
    return ($retour);
    
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function plugin_transactions_bureau_traite_abonnements_init_quota ($quota, $bool_bloque) {
    foreach ($quota as $nom => $element) {
        if (substr($nom, 0, 1) != "_") {
            $tmp=plugin_transactions_bureau_traite_abonnements_init_quota ($element, $bool_bloque);
            $quota[$nom]=$tmp;
        }
    }
    $quota["_compteur"]=0;
    //$quota["_bloque"]=$bool_bloque;
    $quota["_bloque"]=0; // on désactive le _bloque=1 car ingérable au niveau du prêt quand on veut forcer le prêt sur un abonnement dépassé
    return ($quota);
}

?>