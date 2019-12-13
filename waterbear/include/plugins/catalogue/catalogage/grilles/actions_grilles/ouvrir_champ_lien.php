<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_ouvrir_champ_lien ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
        
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $type_ss_champ_lien=$parametres["type_ss_champ_lien"];
    $nom_grille=$parametres["nom_grille"];
    
    if ($nom_grille == "") {
        $nom_grille="unimarc_standard";
    }

    
    // 1) On récupère l'ID du ss-champ de lien de ce champ (généralement $3)
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    $valeur_ss_champ_lien=$liste_ss_champs[0]["valeur"];

    
    // 2) on ouvre la fenêtre
    array_push($retour["resultat"], "this_formulator.appel('bib.php?module=catalogue/catalogage/grilles/$type_ss_champ_lien/$nom_grille&ID_notice=$valeur_ss_champ_lien', $ID_element, 'maj_champ_lien');");
    //array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
    
    return ($retour);
    
    
}


?>