<?php

/**
 * plugin_transactions_bureau_traite_transit()
 * 
 * Ce plugin permet de déterminer la bibliothèque de transit dans le cas d'un retour de document rendu dans bib autre que sa bib d'origine (il y a un autre plugin pour les retours de résas)
 * Il peut aussi bloquer le retour si on ne souhaite pas gérer le transit
 * ATTENTION : si on souhaite interdire le transit mais laisser la possibilité de forcer, il faut mettre 2 dans [niveau_message_transit] et mettre 3 dans le plugin [message_transit]
 * 
 * @param mixed $parametres
 * @param [bureau]
 * @param [niveau_message_transit] => niveau du message pour interdire le transit (moins de 2 = pas de message) ATTENTION mettre à 2 ou 4, mais pas à 3 (message de confirmation)
 *                                    Si on veut avoir un message avec confirmation, mettre 3 dans le plugin message_transit ce qui reviendra au même
 * @param [plugin_add_message] => le plugin qui ajoutera le message avec comme niveau la valeur fournie dans [niveau_message_transit] 
 * 
 * @return
 */
function plugin_transactions_bureau_traite_transit ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $niveau_message_transit=$parametres["niveau_message_transit"]; // si vaut 3 demandera confirmation en cas de transit. Si vaut 4 : échec
    $plugin_add_message=$parametres["plugin_add_message"];
    
    $ligne=$bureau["infos_exemplaire"];
    $bib_destination=$ligne["a_bibliotheque"];
    $bib=$_SESSION["system"]["bib"];
    $validation_message=$bureau["param_script"]["validation_message"];
    
    $bureau["bib_destination"]=""; // on réinitialise
    
    if ($bib != $bib_destination) {
        if ($niveau_message_transit >= 3) { // transit interdit
            $tmp=applique_plugin($plugin_add_message, array("bureau"=>$bureau, "code"=>$niveau_message_transit));
            $bureau=$tmp["resultat"]["bureau"];
        } 
        
        if ($niveau_message_transit <= 2 OR $validation_message == "oui") { // transit autorisé
            $bureau["bib_destination"]=$bib_destination;
        }
        
    }
    
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);
}



?>