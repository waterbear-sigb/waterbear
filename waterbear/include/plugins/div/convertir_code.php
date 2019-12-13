<?php

/**
 * plugin_div_convertir_code()
 * 
 * Ce plugin effectue des conversions entre un jeu de codes et un autre (par exemple les codes support 
 * de la rec995 et les codes supports  * sp�cifiques � Waterbear)
 * Cette convesation peut se faire dans un sens ou dans l'autre suivant la valeur de [sens]
 * Le tableau de conversion est fourni dans [liste_codes] il a la forme {"code1" => "codea", "code2" => "codeb" ...}
 * Il pourra �tre saisi directement dans la def du plugin ou int�gr� via un plugin_inclus
 * Le code � convertir est fourni via [code]
 * 
 * Les param�tres [defaut_code] et [defaut_decode] fournissent des valeurs par d�faut dans un sens ou dans l'autre
 * 
 * La meilleure fa�on d'utiliser ce plugin est de d�finir [liste_codes] et [defaut_xxx] dans lun plugin dynamique, et d'indiquer 
 * [code] et [sens] dans le point d'acc�s
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