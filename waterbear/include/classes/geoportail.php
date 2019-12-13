

<?PHP

//include_once ("include/classes_ext/JSON.php");

class geoportail {

var $clef="";
var $departements=array(); // array (44, 45, 46)
var $referer="";
var $centre_x="";
var $centre_y="";
    
function __construct($parametres) {
    $this->clef=$parametres["clef"];
    $this->departements=$parametres["departements"];
    $centre=$parametres["centre"];
    $this->referer=$parametres["referer"];
    
    $tmp=explode(",", $centre);
    $this->centre_y=$tmp[0];
    $this->centre_x=$tmp[1];
}

function completion_ws ($parametres) {
    $clef=$this->clef;
    $referer=$this->referer;
    
    $texte=urlencode($parametres["texte"]);
    $maximumResponses=$parametres["maximumResponses"];
    $type=$parametres["type"];
    $terr=$parametres["terr"];
    
    $url="http://wxs.ign.fr/".$clef."/ols/apis/completion?text=".$texte."&maximumResponses=".$maximumResponses."&type=".$type."&terr=".$terr;
    //$url="http://wxs.ign.fr/".$clef."/ols/apis/completion?text=".$texte."&maximumResponses=".$maximumResponses."&type=".$type."&terr=";
    
    //$opts = array('http'=>array('method'=>"GET",'header'=>"Accept-language: en\r\n"."Cookie: foo=bar\r\n"));
    $opts = array('http'=>array('method'=>"GET",'header'=>"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0\r\n"."Referer: $referer\r\n"));
    $context = stream_context_create($opts);
    $reponse = file_get_contents($url, false, $context);
    
//print ("\n\n $url \n\n");
//print ($reponse);
//die("");    
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $toto=$json->decode($reponse);
    return($toto);
    
}


function completion ($parametres) {
    $propositions=array();
    $texte=$parametres["texte"];
    $tmp=$this->completion_ws(array("texte"=>$texte, "type"=>"StreetAddress,PositionOfInterest", "maximumResponses"=>20, "terr"=>$this->departements));
    if ($tmp["status"] != "OK") {
        $texte="rue ".$texte;
        $tmp=$this->completion_ws(array("texte"=>$texte, "type"=>"StreetAddress,PositionOfInterest", "maximumResponses"=>20, "terr"=>$this->departements));
        if ($tmp["status"] != "OK") {
            return($propositions);   
        }
    }
    $tmp=$tmp["results"];
//print_r($tmp);
    $nb_propositions=count($tmp);
    foreach ($tmp as $proposition) {
        // on calcule la distance au centre de la carte
        $distance=$this->teste_distance(array("lat"=>$proposition["y"], "lng"=>$proposition["x"]), array("lat"=>$this->centre_y, "lng"=>$this->centre_x));
        $proposition["distance"]=$distance;
        $classification=$proposition["classification"];
        //if ($classification==6 OR $classification==7) { // les codes ont dû changer, mais impossible de trouver une doc là dessus :/
            array_push($propositions, $proposition);
        //}
    }
    
    // trier le tableau par distance
    $distances=array();
    foreach ($propositions as $idx=>$proposition) {
        $distances[$idx]=$proposition["distance"];
    }
    asort($distances);
    $propositions2=array();
    foreach ($distances as $idx => $bidon) {
        array_push($propositions2, $propositions[$idx]);
    }
    
//print_r($propositions2);

    return ($propositions2);
    

}

function teste_distance ($bib, $bib2) {
    $lat1=(float)$bib["lat"];
    $lat2=(float)$bib2["lat"];
    $lng1=(float)$bib["lng"];
    $lng2=(float)$bib2["lng"];  
    $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
    $rlo1 = deg2rad($lng1);
    $rla1 = deg2rad($lat1);
    $rlo2 = deg2rad($lng2);
    $rla2 = deg2rad($lat2);
    $dlo = ($rlo2 - $rlo1) / 2;
    $dla = ($rla2 - $rla1) / 2;
    $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
    $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return (round($earth_radius * $d / 1000));
}

} // fin de la classe
//ign_ws(array("url1"=>"http://wxs.ign.fr", "url2"=>"ols/apis/completion", "clef"=>"uru7xuf49krvn25sddefop6w", "texte"=>"r pierre po", "maximumResponses"=>"20", "type"=>"StreetAddress", "referer"=>"http://waterbear.info/toto.php", "terr"=>"44"));


?>