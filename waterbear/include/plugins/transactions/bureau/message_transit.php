<?php

function plugin_transactions_bureau_message_transit ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $plugin_add_message=$parametres["plugin_add_message"];
    $message=$parametres["message"];
    $code=$parametres["code"];
    
    $bib_destination=$bureau["bib_destination"];
    $bib=$_SESSION["system"]["bib"];
    
    // on intègre la bib dans le message
    $message=str_replace("_bib_", $bib_destination, $message);
    
    if ($bib != $bib_destination AND $bib_destination != "") {
        $tmp=applique_plugin($plugin_add_message, array("bureau"=>$bureau, "message"=>$message, "code"=>$code));
        $bureau=$tmp["resultat"]["bureau"];
    }
    
    
    
    $retour["resultat"]["bureau"]=$bureau;
    return($retour);
}
?>