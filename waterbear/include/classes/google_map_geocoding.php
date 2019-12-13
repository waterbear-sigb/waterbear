<?php

class  google_map_geocoding {

var $google_url;    
var $adresse;
var $json;
var $plugin_get_adresse;
    
    
function __construct ($parametres) {
    $this->google_url="http://maps.googleapis.com/maps/api/geocode/json?";
    $this->json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $this->plugin_get_adresse=$parametres["plugin_get_adresse"];
}


function google_ws ($requete) {
    //print($requete);
    $chaine=file_get_contents($requete);
    if ($chaine === false) {
        // todo   
    }
    $tableau=$this->json->decode($chaine);
    return($tableau);
}

function set_adresse ($adresse) {
    $this->adresse=urlencode($adresse);
}

function geocode () {
    $url=$this->google_url."address=".$this->adresse."&sensor=false";
    $infos=$this->google_ws ($url);
    if ($infos["status"] != "OK") {
        return ("");
    }

    $lat=$infos["results"][0]["geometry"]["location"]["lat"];
    $lng=$infos["results"][0]["geometry"]["location"]["lng"];
    //$coordonnees=$lat." ".$lng;
    $coordonnees=$lat." ".$lng;
    return ($coordonnees);
}

function ID_lecteur_2_adresse ($ID_lecteur) {
    $tmp=applique_plugin ($this->plugin_get_adresse, array("ID_notice"=>$ID_lecteur, "type_doc"=>"lecteur"));
    if ($tmp["succes"] != 1) {
        return ("");
    }
    $adresse=$tmp["resultat"]["texte"];
    return ($adresse);
}




    
    
    
} // fin de la classe


?>