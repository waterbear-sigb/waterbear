<?php

/**
 * plugin_div_convertir_code()
 * 
 * Ce plugin effectue des conversions entre un jeu de codes et un autre (par exemple les codes support 
 * de la rec995 et les codes supports  * spécifiques à Waterbear)
 * Cette convesation peut se faire dans un sens ou dans l'autre suivant la valeur de [sens]
 * Le tableau de conversion est fourni dans [liste_codes] il a la forme {"code1" => "codea", "code2" => "codeb" ...}
 * Il pourra être saisi directement dans la def du plugin ou intégré via un plugin_inclus
 * Le code à convertir est fourni via [code]
 * 
 * Les paramètres [defaut_code] et [defaut_decode] fournissent des valeurs par défaut dans un sens ou dans l'autre
 * 
 * La meilleure façon d'utiliser ce plugin est de définir [liste_codes] et [defaut_xxx] dans lun plugin dynamique, et d'indiquer 
 * [code] et [sens] dans le point d'accès
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_div_convertir_code ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();

    // fourni dans le plugin
    $liste_codes=$parametres["liste_codes"];
    $defaut_code=$parametres["defaut_code"];
    $defaut_decode=$parametres["defaut_decode"];
    
    // fourni dans le PA
    $code=$parametres["code"];
    $sens=$parametres["sens"];
    
    if ($sens=="") {
        $sens="code";
    }
    
    if ($code=="") {
        $code="_defaut";
    }
    
    if ($sens=="code") {
        if (isset($liste_codes[$code])) {
            $retour["resultat"]["texte"]=$liste_codes[$code];
        } else {
            $retour["resultat"]["texte"]="";
        }
        if ($retour["resultat"]["texte"] == "") {
            $retour["resultat"]["texte"]=$defaut_code;
        }
    } else {
        foreach ($liste_codes as $clef => $valeur) {
            if ($valeur==$code) {
                $retour["resultat"]["texte"]=$clef;
            } 
        }
        if ($retour["resultat"]["texte"] == "") {
            $retour["resultat"]["texte"]=$defaut_decode;
        }
    }
    
    return ($retour);
    
}


?>