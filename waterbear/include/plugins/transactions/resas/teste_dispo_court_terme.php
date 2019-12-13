<?php
/**
 * plugin_transactions_resas_teste_dispo_court_terme()
 * 
 * Ce plugin teste la reservabilité court terme d'un exemplaire
 * Basée sur les messages (700$c)
 * 
 * On associe une réservabilité à chacun des codes (avec une valeur par défaut)
 * 
 * @param mixed $parametres
 * @param [exemplaire] => notice exemplaire en ligne de base de données (avec les colonnes)
 * @param [en_pret] => 0 ou 1 (en pret ou pas)
 * @param [bool_reserver_disponibles] => 0 ou 1 si on peut réserver un exemplaire pas en pret
 * @param [message_2_reservable] =>  un tableau associant une réservabilité (0 ou 1) à chaque code du message ou _defaut
 * 
 * @return [reservable] (0 ou 1)
 */
function plugin_transactions_resas_teste_dispo_court_terme ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ligne=$parametres["exemplaire"];
    $en_pret=$parametres["en_pret"];
    $bool_reserver_disponibles=$parametres["bool_reserver_disponibles"];
    $message_2_reservable=$parametres["message_2_reservable"];
    
    // Si  en rayon et réservation des docs en rayon impossible
    //if ($en_pret == 0 AND $bool_reserver_disponibles == 0) {
        //$retour["resultat"]["reservable"]=0;
        //return ($retour);
    //}
    
    
    // On récupère les messages (code)
    $messages_str=$ligne["a_message_liste"];
    $messages=explode("|", $messages_str);
 
 
    // on récupère la réservabilité associée aux messages
    $reservable=array();
    foreach ($messages as $message) {
        $message=trim($message);
        if ($message != "") {
            if (isset($message_2_reservable[$message])) {
                array_push($reservable, $message_2_reservable[$message]);
            }
        }
    }
    
    // On synthétise les résultats (si plusieurs messages, il suffit d'un 0 pour annuler tous les 1)
    if (in_array(0, $reservable)) {
        $retour["resultat"]["reservable"]=0;
    } elseif (in_array(1, $reservable)) {
        $retour["resultat"]["reservable"]=1;
    } else {
         $retour["resultat"]["reservable"]=$message_2_reservable["_defaut"];
    }
    
    return ($retour);
    
    
    
    
}

?>