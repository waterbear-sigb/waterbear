<?php

function plugin_catalogue_marcxml_formate_mv ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["mot"]="";
    $retour["resultat"]["chaine"]="";
    
    // variables
    $motif=trim($parametres["motif"]);
    $chaine=trim($parametres["chaine"]); // on rejoute un espace devant
    
    $motif=str_replace ("'", " ", $motif);
    $motif=str_replace ("-", " ", $motif);
    $motif=str_replace ("_", " ", $motif);
    $motif=str_replace (".", " ", $motif);
    $motif=str_replace (",", " ", $motif);
    $motif=str_replace (";", " ", $motif);
    $motif=str_replace ("(", " ", $motif);
    $motif=str_replace (")", " ", $motif);
    $motif=str_replace ("[", " ", $motif);
    $motif=str_replace ("]", " ", $motif);
    
    //$motif=str_replace ("[^A-Za-z0-9]", " ", $motif);
    
    if (strlen($motif) <= 1) {
        return ($retour);
    }
    
    
    $segments=array();
    
    // 1) séquençage de $motif
    $mots=explode(" ", $motif);
    
    // 2) on récupère la chaine sans accents
    $chaine2=xxx_accent($chaine);
    
    $chaine2=str_replace ("'", " ", $chaine2);
    $chaine2=str_replace ("-", " ", $chaine2);
    $chaine2=str_replace ("_", " ", $chaine2);
    $chaine2=str_replace (".", " ", $chaine2);
    $chaine2=str_replace (",", " ", $chaine2);
    $chaine2=str_replace (";", " ", $chaine2);
    $chaine2=str_replace ("(", " ", $chaine2);
    $chaine2=str_replace (")", " ", $chaine2);
    $chaine2=str_replace ("[", " ", $chaine2);
    $chaine2=str_replace ("]", " ", $chaine2);
    
    //$chaine2=str_replace ("[^A-Za-z0-9]", " ", $chaine2);
    $tableau=explode ("|", $chaine);
    $tableau2=explode("|", $chaine2);
    
    // 3) Pour chaque mot, on récupère et on dédoublonne le segment...
    foreach ($mots as $mot) {
        $segment="";
        $tmp_segment=plugin_catalogue_marcxml_formate_mv_get_segment($tableau2, $mot);
        $idx_segment=$tmp_segment["idx"];
        $retour["resultat"]["mot"].=" ".$tmp_segment["mot"]; // on ne garde que le dernier
        if ($idx_segment !== false) {
            $segment=trim($tableau[$idx_segment])." ";
            if ($segment != "" AND ! in_array($segment, $segments)) {
                array_push ($segments, $segment);
            }
        }
    } // fin du pour chaque mot
    
    // 4) On concatène les segments
    if (count($segments)==0) {
        $retour["resultat"]["chaine"]="? $chaine";
    } else {
        $retour["resultat"]["chaine"]=implode (" ; ", $segments);
    }
    $retour["resultat"]["mot"]=trim($retour["resultat"]["mot"]);
    return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function plugin_catalogue_marcxml_formate_mv_get_segment($tableau, $mot) {
    if (strlen($mot) <= 1) {
        return (false);
    }
    $mot2=xxx_accent($mot);
    $mot2=" ".$mot2;
    foreach ($tableau as $idx=>$element) {
        $element=" ".$element;
        $pos=mb_stripos($element, $mot2);
        if ($pos !== false) {
            $mot=plugin_catalogue_marcxml_formate_mv_get_mot ($element, $pos);
            $retour=array("idx"=>$idx, "mot"=>$mot);
            return ($retour);
        }
    }

    return (false);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function plugin_catalogue_marcxml_formate_mv_get_mot($chaine, $pos) {
    $longueur=strlen($chaine);
    $debut=$pos+1;
    $fin=$longueur;
    // on cherche le début
    
    
    
    for ($i=$debut ; $i <= $longueur ; $i++) {
        $car=substr($chaine, $i, 1);
        if ($car==" ") {
            $fin=$i-1;
            break;
        }
    }
    
    $longueur=($fin-$debut)+1;
    $mot=substr($chaine, $debut, $longueur);
    $mot=trim($mot);
    return ($mot);

}
/**function xxx_accent($mot) {
    $mot=str_ireplace (utf8_encode("é"), "e", $mot);
    $mot=str_ireplace (utf8_encode("è"), "e", $mot);
    $mot=str_ireplace (utf8_encode("ê"), "e", $mot);
    $mot=str_ireplace (utf8_encode("ç"), "c", $mot);
    $mot=str_ireplace (utf8_encode("à"), "a", $mot);
    return ($mot);
}
**/

function xxx_accent($string)
{
    $string = utf8_decode($string);
    $string = strtr($string,    'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                                'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    $string = utf8_encode($string);                 
    return $string;
};


?>