<?php

class google_place {

var $google_url;    
var $json;
var $key;
var $location; // coordonnées autour desquelles rechercher
var $radius; // diamètre en mètres de recherche
var $ville; // en option on peut rajouter une ville (ou un CP ou autre chose) à ajouter à la chaine de recherche
var $components;
    
    
function __construct ($parametres) {
    $action=$parametres["action"];
    if ($action == "autocomplete") {
        $this->google_url="https://maps.googleapis.com/maps/api/place/autocomplete/json?";
    } elseif ($action == "details") {
        $this->google_url="https://maps.googleapis.com/maps/api/place/details/json?";
    }
    $this->json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $this->key=$parametres["key"];
    $this->location=$parametres["location"];
    $this->radius=$parametres["radius"];
    $this->ville=$parametres["ville"];
    $this->components=$parametres["components"];
    if ($this->radius == "") {
        $this->radius=5000; // par défaut : 5Km
    }
}


function google_ws ($requete) {
    $chaine=file_get_contents($requete);
    if ($chaine === false) {
        //die ("FALSE");  
    }
    $tableau=$this->json->decode($chaine);
    return($tableau);
}

function google_autocomplete ($chaine) {
    if ($this->ville != "") {
        $chaine=$this->ville." ".$chaine;
    }
    $chaine=urlencode($chaine);
    $url=$this->google_url."input=".$chaine."&sensor=false&location=".$this->location."&radius=".$this->radius."&key=".$this->key."&types=geocode&components=".$this->components;
    //$url=$this->google_url."input=".$chaine."&sensor=false&radius=".$this->radius."&key=".$this->key."&types=geocode&components=".$this->components;
    //print("\n".$url."\n");
    $infos=$this->google_ws ($url);

    if ($infos["status"] != "OK") {
        return (array());
    }
    $retour=array();
dbg_log("*************** AUTOCOMPLETE ****************");
dbg_log($infos);
    foreach ($infos["predictions"] as $prediction) {
        array_push($retour, array("nom"=>$prediction["description"], "id"=>"ref:".$prediction["place_id"]));
    }
    
    return ($retour);
}

function google_details ($reference) {
    $url=$this->google_url."input=".$chaine."&sensor=false&key=".$this->key."&placeid=".$reference;
dbg_log("*************** google_details() ****************");
dbg_log("*************** URL ****************");
dbg_log($url);
    $infos=$this->google_ws ($url);

dbg_log("*************** RETOUR GOOGLE ****************");
dbg_log($infos);

    if ($infos["status"] != "OK") {
        return (array());
    }
    $retour=array();
    
    // récupération des infos d'adresse
    foreach ($infos["result"]["address_components"] as $elem) {
        $types=$elem["types"];
        $nom=$elem["long_name"];
        foreach ($types as $type) {
            if ($type == "route") {
                $retour["rue"]=$nom;
            } elseif ($type == "locality") {
                $retour["ville"]=$nom;
            } elseif ($type == "postal_code") {
                $retour["CP"]=$nom;
            } elseif ($type == "sublocality") {
                $retour["lieudit"]=$nom;
            }
        }
    }
    
    if ($retour["rue"] == "" AND $retour["lieudit"] != "") {
        $retour["rue"]=$retour["lieudit"];
    }
    
    // récupération des coordonnées
    $retour["latitude"]=$infos["result"]["geometry"]["location"]["lat"];
    $retour["longitude"]=$infos["result"]["geometry"]["location"]["lng"];
dbg_log("*************** RETOUR FONCTION ****************");
dbg_log($retour);
    
    return ($retour);
}






/**
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
    $coordonnees=$lat." | ".$lng;
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
**/




    
    
    
} // fin de la classe


?>