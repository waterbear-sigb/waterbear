<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_creation_notice_ss_champs()
 * 
 * Ce plugin permet de cr�er un objet li� � partir d'infos fournies dans les diff�rents ss-champs. C'est une alternative � l'utilisation d'un ss-champ 9a avec un formatage des infos
 * Ici, on recup�res les veleurs des sous-champs, et on va g�n�rer un nouvel objet (ATTENTION : pour l'instant pas de d�doublonnage)
 * On peut optionnellement effectuer un certain nombre de traitements sur les valeurs des ss-champs : plugin_traitements_specifiques
 * On peut exiger que la notice m�re ait un n� de notice ( = ait �t� enregistr�e) : bool_exige_ID_notice
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [plugin_traitements_specifiques] => plugin utilis� pour effectuer des transformations sur les variables g�n�r�es depuis les ss-champs avant de cr�er la notice
 *                                            par exemple pour les abonnements modifier les dates de d�but et de fin   
 *                                            [variables] plugin ([variables])
 * @param [plugin_crea_objet] => plugin utilis� pour cr�er l'objet
 * @param [plugin_notice_2_db] => plugin utilis� pour enregistrer la notice dans la DB
 * @param [nom_ss_champ_lien] => nom ($a, $b...) du sous champ de lien � modifier
 * @param [bool_exige_ID_notice] => si vaut 1, retournera une erreur si la notice n'a pas d'ID_notice (i.e. n'a pas �t� enregistr�e)
 * @param [bool_pas_de_maj_champ_lien] => si vaut 1, on ne r�cup�rera pas le champ de lien et on ne le mettra pas � jour (parv exemple pour paiement du porte-monnaie)
 * 
 */

function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_creation_notice_ss_champs ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $ID_element=$parametres["ID_element"];
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $bool_exige_ID_notice=$parametres["bool_exige_ID_notice"];
    $bool_pas_de_maj_champ_lien=$parametres["bool_pas_de_maj_champ_lien"];
    
    $id_ss_champ_lien="";
    $variables=array();
    
    // 1) On v�rifie qu'on a bien un n� de notice (donc que la notice a �t� sauvegard�e)
    $ID_notice=$_SESSION["operations"][$ID_operation]["ID_notice"];
    if ($ID_notice == "" AND $bool_exige_ID_notice) {
         $retour["succes"]=0;
         $retour["erreur"]="@&Vous devez au prealable enregistrer la notice";
         return ($retour); 
    }
    $variables["ID_notice"]=$ID_notice; 
    
    // 2) On r�cup�re la liste des sous-champs  -> [id | valeur |type | nom]
    // On g�n�re une $variable de la forme ["ss_champ_a" => valeur, "ss_champ_b" => valeur]...
    // ATTENTION ne g�re pas la possibilit�s d'avoir plusieurs fois le m�me ss-champ
    $liste_ss_champs=$formulator->get_ss_champs_by_nom($ID_element, "");
    foreach ($liste_ss_champs as $ss_champ) {
        $nom=$ss_champ["nom"];
        $valeur=$ss_champ["valeur"];
        $id=$ss_champ["id"];
        $variables["ss_champ_".$nom]=$valeur;
        if ($nom == $nom_ss_champ_lien) {
            $id_ss_champ_lien=$id;
        }
    }
    
    // 3) traitements sp�cifiques
    if (is_array($parametres["plugin_traitements_specifiques"])) {
        $tmp=applique_plugin ($parametres["plugin_traitements_specifiques"], array("variables"=>$variables));
        if ($tmp ["succes"] != 1) {
            return ($tmp);
        }
        $variables=$tmp["resultat"]["variables"];
    }
    
    //4) on g�n�re l'objet
    $tmp=applique_plugin ($parametres["plugin_crea_objet"], array("variables"=>$variables));
    if ($tmp ["succes"] != 1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 5) On cr�e la notice
    $tmp=applique_plugin($parametres["plugin_notice_2_db"], array("notice"=>$notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $ID_objet=$tmp["resultat"]["ID_notice"];
    
    // 6) On r�cup�re l'ID du champ de lien
    if ($bool_pas_de_maj_champ_lien == "1") { // cas sp�cifique o� on ne veut pas mettre � jour le $3 (par exemple, paiement du porte monnaie)
        // on ne fait rien
    } else {
        if ($id_ss_champ_lien == "") {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
            return ($retour);
        }
        $update=array("valeur"=>$ID_notice);
        $formulator->update_element ($id_ss_champ_lien, $update);
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_objet.'");');
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
    }
    
    return ($retour);
    
}

?>