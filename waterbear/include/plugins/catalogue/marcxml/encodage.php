<?php

/**
 * plugin_catalogue_marcxml_encodage()
 * 
 * Ce plugin encode ou décode une chaine de caractères en fonction d'une liste de caracteres de
 * substitution fournis. L'encodage peut se faire dans un sens ou dans l'autre (paramètre "sens")
 * Les caractères sont fournis sous la forme des codes ASCII éventuellement séparés par des espaces
 * 
 * @param mixed $parametres
 * @param [chaine] => la chaine à modifier
 * @param [sens] => par défaut de a vers b sauf si vaut "ba" (de b vers a)
 * @param [utf8_decode] => si 1, la chaine sera utf8_decodée avant d'être traitée
 * @param [utf8_encode] => si 1, la chaine sera utf8_encodée après avoir été traitée
 * @param [caracteres] => liste des caracteres à substituer
 * @param             [a] et [b] caracteres à substituer 
 * @param             [sens_unique] => ** option ** peut être "ab" ou "ba" pour indiquer qu'on ne doit faire cette conversion qu'en import (ba) ou en export (ab) Si vide, la conversion est effectuée dans les 2 sens
 * @return
 */
function plugin_catalogue_marcxml_encodage ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["chaine"]="";
    $chaine=$parametres["chaine"];

    if ($chaine=="") {
        return($retour);
    }
    // dbg
    //$retour["resultat"]["chaine"]=$chaine;
    //return ($retour);
    // fin dbg
    
    if ($parametres["utf8_decode"]==1) {
        $chaine=utf8_decode($chaine);
    }

// Décommenter pour faire un serailize de paramètres    
//$toto=serialize($parametres["caracteres"]);
//die($toto);

    if (isset($parametres["caracteres_serialize"])) {
        $parametres["caracteres"]=unserialize($parametres["caracteres_serialize"]) OR die ("ERREUR");
    }


    
    if (is_array($parametres["caracteres"])) {
        $long_chaine=strlen($chaine);
        $chaine_retour="";

        
        
        $array_remplace=array();
        foreach ($parametres["caracteres"] as $caractere) {
            //Vérification du sens unique (conversion qu'on ne fait qu'en import (ba) ou en export (ab))
            $sens_unique=$caractere["sens_unique"];
            if ($sens_unique != "" AND ($sens_unique != $parametres["sens"])) {
                continue;
            }
            $a="";
            $tmp=explode (" ", $caractere["a"]);
            foreach ($tmp as $elem) {
                $a.=chr(hexdec($elem));
            }
            $b="";
            $tmp=explode (" ", $caractere["b"]);
            foreach ($tmp as $elem) {
                $b.=chr(hexdec($elem));
            }
    
            if ($parametres["sens"]=="ba") {
                $motif=$b;
                $remplace=$a;
            } else {
                $motif=$a;
                $remplace=$b;
            }
            $array_remplace[$motif]=$remplace;
        }
        $chaine=strtr($chaine, $array_remplace);
    }
   
  
    
    
    //
    if ($parametres["utf8_encode"]==1) {
        $chaine=utf8_encode($chaine);
    }
    


    $retour["resultat"]["chaine"]=$chaine;
    return ($retour);
}

?>