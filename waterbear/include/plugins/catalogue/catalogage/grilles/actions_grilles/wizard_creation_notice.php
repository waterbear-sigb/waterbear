<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_creation_notice()
 * 
 * Ce plugin permet de créer un objet à partir d'une chaine de caractères fournie dans $_REQUEST["chaine"]
 * Il va d'abord analyser la chaine pour en extraire les infos : [plugin_analyse_chaine]
 * Puis générer une notice XMl en incorporant ces infos via des alias : [plugin_crea_objet]
 * Enfin, enregistrer cette notice dans la base : [plugin_notice_2_db]
 * Une fois la notice créée, il en récupère l'ID et va mettre à jour le champ de lien fourni [nom_ss_champ_lien]
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [nom_ss_champ_lien] => nom ($a, $b...) du sous champ de lien à modifier
 * @param [plugin_analyse_chaine] => plugin qui va analyser la chaine fournie et en retourner les éléments sous formes de tableau
 * @param [plugin_crea_objet] => plugin utilisé pour créer l'objet
 * @param [plugin_notice_2_db] => plugin utilisé pour enregistrer la notice dans la DB
 *  
 * @paramrequete : chaine => la chaine à analyser
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_creation_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $ID_parent=$infos["ID_parent"];

    $chaine=$_REQUEST["chaine"]; // => chaine à analyser
    
    // 1) On analyse la chaine 
    $tmp=applique_plugin ($parametres["plugin_analyse_chaine"], array("chaine"=>$chaine));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $variables=$tmp["resultat"]["variables"];
    
    // 2) Dédoublonnage ??? => TODO
    
    // 3) On crée un objet en y intégrant les variables (sous forme d'alias)
    $tmp=applique_plugin($parametres["plugin_crea_objet"], array("variables"=>$variables));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 4) On crée la notice
    $tmp=applique_plugin($parametres["plugin_notice_2_db"], array("notice"=>$notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $ID_notice=$tmp["resultat"]["ID_notice"];
    
    // 5) On récupère l'ID du champ de lien
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_parent, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    $update=array("valeur"=>$ID_notice);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($id_ss_champ_lien, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_notice.'");');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
    
    return ($retour);
    
}

?>