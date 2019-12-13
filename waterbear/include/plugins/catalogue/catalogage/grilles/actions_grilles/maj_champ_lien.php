<?php

function plugin_catalogue_catalogage_grilles_actions_grilles_maj_champ_lien ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
        
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    
    $ID_notice_liee=$_REQUEST["ID_notice_liee"];
    
    // 0) cas d'une suppression totale 
    if ($ID_notice_liee == 0) { // cas d'une suppression totale de notice
        $_SESSION["operations"][$ID_operation]["formulator"]->delete_element ($ID_element);
        array_push($retour["resultat"], 'this_formulator.delete_element('.$ID_element.', '.$infos["ID_parent"].');');
        return ($retour);
    }
    
    // 1) On récupère l'ID du ss-champ de lien de ce champ (généralement $3)
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    
    // 2) on met à jour formulator_server
    $update=array("valeur"=>$ID_notice_liee);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($id_ss_champ_lien, $update);
    
    // 3) on maj formulator js et on lance la maj du champ entier
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_notice_liee.'");');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
    
    return ($retour);
    
    
}


?>