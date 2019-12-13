<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/google_place.php");

function plugin_div_google_place_autocomplete ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $key=$parametres["key"];
    $location=$parametres["location"];
    $radius=$parametres["radius"];
    $chaine=$parametres["chaine"];
    $villes=$parametres["villes"];
    $components=$parametres["components"];
    
    if ($villes == "") {
        $villes=array();
    } else {
        $villes=explode(",", $villes);
    }
    
    $resultats=array();
    foreach ($villes as $ville) {
        $ville=trim($ville);
        $google_place = new google_place(array("key"=>$key, "location"=>$location, "radius"=>$radius, "ville"=>$ville, "components"=>$components, "action"=>"autocomplete"));
        $tmp=$google_place->google_autocomplete($chaine);
        $resultats=array_merge($resultats, $tmp);
    }
    
    if (count($resultats) == 0 AND count($villes) > 0) { // si on arien trouv� en sp�cifiant les villes (mais qu'on a bien sp�cifi� des villes), on essaye sans en sp�cifier au cas o� l'utilisateur saisirait la ville manuellement
        $google_place = new google_place(array("key"=>$key, "location"=>$location, "radius"=>$radius, "components"=>$components, "action"=>"autocomplete"));
        $resultats=$google_place->google_autocomplete($chaine);
    }
   
    $retour["resultat"]=$resultats;
    return ($retour);   
}


?>