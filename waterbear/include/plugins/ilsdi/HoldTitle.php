<?php

function plugin_ilsdi_HoldTitle ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $patronId=$_REQUEST["patronId"];
    $bibId=$_REQUEST["bibId"];
    $pickupLocation=$_REQUEST["pickupLocation"];
    
    $plugin_crea_resa=$parametres["plugin_crea_resa"];
    $plugin_get_titre=$parametres["plugin_get_titre"];
    
    $erreur="";
    
    // 1) on récupère le titre
    $notice_biblio=get_objet_xml_by_id("biblio", $bibId);
    $tmp=applique_plugin($plugin_get_titre, array("notice"=>$notice_biblio));
    $titre=$tmp["resultat"]["texte"];
    
    // on effectue la réservation
    $tmp=applique_plugin($plugin_crea_resa, array("ID_doc"=>$bibId, "ID_lecteur"=>$patronId, "bib"=>$pickupLocation));
    if ($tmp["succes"] != 1) {
        $erreur="Une erreur est survenue : ".$tmp["erreur"];
    }
    if ($tmp["resultat"]["reservable"] != 1) {
        $erreur=$tmp["resultat"]["message"];
    }
    
    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml.="<HoldTitle>\n";
    if ($erreur != "") {
        $xml.="<error>".$erreur."</error>\n";
    } else {
        $xml.="<title>".$titre."</title>\n";
    }
    $xml.="</HoldTitle>\n";
    

    $retour["resultat"]["xml"]=$xml;
    return ($retour);
}    

?>