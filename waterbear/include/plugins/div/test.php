<?php

function plugin_div_test ($parametres) {
    $retour = array ();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID=$parametres["ID"];
    $ID2=$parametres["ID2"];
    
    $retour["resultat"]["POPO"]["PIPI"]["TRUTRU"]="ID vaut $ID";
    $retour["resultat"]["PUPU"]="ID2 vaut $ID2";
    return ($retour);
}

?>