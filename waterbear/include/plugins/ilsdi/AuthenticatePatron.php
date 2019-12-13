<?php

function plugin_ilsdi_AuthenticatePatron ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $username=$_REQUEST["username"];
    $password=$_REQUEST["password"];
    
    $plugin_get_lecteur=$parametres["plugin_get_lecteur"];
    
    $tmp=applique_plugin($plugin_get_lecteur, array("username"=>$username, "password"=>$password));
    if ($tmp["succes"]!=1) {
        
    }
    $notices=$tmp["resultat"]["notices"];
    $nb_notices=$tmp["resultat"]["nb_notices"];
    
    $ID_lecteur="";
    if ($nb_notices == 1) {
        $ID_lecteur=$notices[0]["ID"];
    }
    
    
    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml.="<AuthenticatePatron>\n";
    $xml.="<patronId>".$ID_lecteur."</patronId>";
    $xml.="</AuthenticatePatron>\n";
    
    $retour["resultat"]["xml"]=$xml;
    return ($retour);    
}

?>