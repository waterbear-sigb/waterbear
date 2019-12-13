<?php

function plugin_catalogue_marcxml_get_ISBN10 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["ISBN"]="";
    
    $chaine=$parametres["chaine"];
    
    
    $clef="";

    $chaine=trim($chaine);
    $chaine=eregi_replace ("-","",$chaine);
    $chaine=eregi_replace ("_","",$chaine);
    $chaine=eregi_replace ("\.","",$chaine);
    $chaine=eregi_replace (" ","",$chaine);
    
    if (strlen($chaine)==10) { // ISBN complet avec clef de controle
        $retour["resultat"]["ISBN"]=$chaine;
        return($retour);
    } elseif (strlen($chaine)==9) { // ISBN sans clef de controle
        $clef=plugin_catalogue_marcxml_get_ISBN10_get_clef($chaine);
        $chaine=$chaine.$clef;
        $retour["resultat"]["ISBN"]=$chaine;
        return($retour);
    } elseif (strlen($chaine)==13) {  // Code barres commercial
        $chaine=substr($chaine, 3, 9);
        $clef=plugin_catalogue_marcxml_get_ISBN10_get_clef($chaine);
        $chaine=$chaine.$clef;
        $retour["resultat"]["ISBN"]=$chaine;
        return($retour);
    } else { // ISBN erronné
        return($retour);
    }
}

function plugin_catalogue_marcxml_get_ISBN10_get_clef($chaine) {
    if (eregi ("[^0-9]",$chaine)) {  // Vérifie qu'il n'y a que des chiffres 
    return("");
    }
    $total=0;
    $modulo=0;
    $chiffre=0;
    for ($i=0;$i<9;$i++) {
        $chiffre=substr($chaine, $i, 1);
        $chiffre=$chiffre*(10-$i);
        $total=$total+$chiffre;
    }
    $modulo=11-($total%11);
    if ($modulo==10) {
        $modulo="X";
    }
    if ($modulo==11) {
        $modulo="0";
    }
    return ($modulo);
}


?>