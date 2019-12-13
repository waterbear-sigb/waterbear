<?php

/**
 * plugin_transactions_bureau_add_messages()
 * 
 * Ce plugin alimente le tableau bureau["messages"] qui contient les messages qui devront être affichés par le client
 * Chaque message contient un [code] : 1 => ne s'affiche pas // 2 => simple info (on fait OK) // 3 => demande confirmation (accepter ou refuser) // 4 => on ne peut que refuser
 * et un [message]
 * 
 * Met à jour automatiquement bureau["niveau_message"]
 * 
 * La fonction peut soit recevoir un simple message ou une array de messages
 * 
 * @param mixed $parametres
 * @param [bureau] => le bureau
 * @param [messages] => liste des messages à ajouter SOIT UN seul message SOIT une liste [0,1,2...]
 * @param ---------- [code] => 1->4
 * @param ---------- [message] 
 * @return
 */

function plugin_transactions_bureau_add_messages ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $messages=$parametres["messages"];
    
    if (! is_array($bureau["messages"])) {
        $bureau["messages"]=array();
    }
    
    // Regarde s'il y a un seul message ou plusieurs
    if (isset($messages["message"]) AND $messages["message"] != "") {
        array_push ($bureau["messages"], $messages);
        if ($bureau["niveau_message"] < $messages["code"]) {
            $bureau["niveau_message"] = $messages["code"];
        }
    } else {
        foreach ($messages as $message) {
            if ($message["message"] != "") {
                array_push ($bureau["messages"], $message);
                if ($bureau["niveau_message"] < $message["code"]) {
                    $bureau["niveau_message"] = $message["code"];
                }
            }
        }
    }
    
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);
    
}

?>