<?php

/**
 * @author Quentin CHEVILLON
 * @copyright 2012
 */

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_biblio_get_notice_mel ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"]; // ID du champ 010 dans formulator
    $auto_grille=$parametres["auto_grille"];
    if ($auto_grille == "") {
        $auto_grille="catalogue/catalogage/grilles/biblio/unimarc_standard";
    }
    $plugin_get_notice_mel=$parametres["plugin_get_notice_mel"];
    
    // on récupère le cab (010$a)
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "a");
    if (count($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]="Impossible de trouver le sous-champ a dans le champ 010";
        return ($retour);
    }
    $cab=$liste_ss_champs[0]["valeur"];
    
    $tmp=applique_plugin ($plugin_get_notice_mel, array("EAN"=>$cab));
    if ($tmp["succes"] != 1) {
        $message=$tmp["erreur"];
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    $ID_notice=$tmp["resultat"]["ID_notice"];
    
    array_push($retour["resultat"], 'window.location.href="bib.php?module='.$auto_grille.'&ID_notice='.$ID_notice.'";');
    return ($retour);
    
}

?>