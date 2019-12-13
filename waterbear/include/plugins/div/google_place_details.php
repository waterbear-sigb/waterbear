<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/google_place.php");

function plugin_div_google_place_details ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $key=$parametres["key"];

    $reference=$parametres["reference"];

    
    $resultats=array();
    
    $google_place = new google_place(array("key"=>$key, "action"=>"details"));
    $tmp=$google_place->google_details($reference);
    
  
    $retour["resultat"]=$tmp;
    return ($retour);   
}


?>