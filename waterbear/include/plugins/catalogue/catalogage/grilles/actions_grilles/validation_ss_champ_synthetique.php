<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_validation_ss_champ_synthetique ()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [nom_ss_champ_lien] => nom ($a, $b...) du sous champ de lien à modifier
 * 
 * @return array
 * 
 * Ce plugin permet de valider un sous-champ synthétique. Outre le fait de simplement mettre à jour le ss-champ synthétique lui-même
 * il met à jour le sous-champ de lien lié avec le numéro de notice liée trouvée et lance la validation de ce sous-champ de lien
 * 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_validation_ss_champ_synthetique ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
        
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $ID_parent=$infos["ID_parent"];
 
    $ID_notice_liee=$_REQUEST["valeur"]; // => ID notice liée
    $intitule=$_REQUEST["intitule"]; // => intitulé du ss-champ synthétique

    // on maj le ss-champ synthétique coté serveur 
    $update=array("valeur"=>$intitule);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    // on récupère l'id du ss-champ de lien
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_parent, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    
    // on maj le ss-champ de lien
    $update=array("valeur"=>$ID_notice_liee);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($id_ss_champ_lien, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_notice_liee.'");');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');

    return ($retour);
    
    
    
    
}



?>