<?php
/**
 * plugin_transactions_resas_teste_dispo_court_terme()
 * 
 * Ce plugin teste la reservabilit� court terme d'un exemplaire
 * Bas�e sur les messages (700$c)
 * 
 * On associe une r�servabilit� � chacun des codes (avec une valeur par d�faut)
 * 
 * @param mixed $parametres
 * @param [exemplaire] => notice exemplaire en ligne de base de donn�es (avec les colonnes)
 * @param [en_pret] => 0 ou 1 (en pret ou pas)
 * @param [bool_reserver_disponibles] => 0 ou 1 si on peut r�server un exemplaire pas en pret
 * @param [message_2_reservable] =>  un tableau associant une r�servabilit� (0 ou 1) � chaque code du message ou _defaut
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
    
    // Si  en rayon et r�servation des docs en rayon impossible
    //if ($en_pret == 0 AND $bool_reserver_disponibles == 0) {
        //$retour["resultat"]["reservable"]=0;
        //return ($retour);
    //}
    
    
    // On r�cup�re les messages (code)
    $messages_str=$ligne["a_message_liste"];
    $messages=explode("|", $messages_str);
 
 
    // on r�cup�re la r�servabilit� associ�e aux messages
    $reservable=array();
    foreach ($messages as $message) {
        $message=trim($message);
        if ($message != "") {
            if (isset($message_2_reservable[$message])) {
                array_push($reservable, $message_2_reservable[$message]);
            }
        }
    }
    
    // On synth�tise les r�sultats (si plusieurs messages, il suffit d'un 0 pour annuler tous les 1)
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