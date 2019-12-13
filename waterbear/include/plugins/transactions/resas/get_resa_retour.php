<?php

// DEPRECATED !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

function plugin_transactions_resas_get_resa_retour ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["ligne"]="";
    
    $plugin_recherche=$parametres["plugin_recherche"];
    $ID_exemplaire=$parametres["ID_exemplaire"];
    
    $tmp=applique_plugin($plugin_recherche, array("ID_exemplaire"=>$ID_exemplaire));
    if ($tmp["succes"] != 1) {
        return($tmp);
    }

    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    if ($nb_resas >= 1) {
        $retour["resultat"]["ligne"]=$resas[0];
    }
    
    
    return ($retour);
}



?>