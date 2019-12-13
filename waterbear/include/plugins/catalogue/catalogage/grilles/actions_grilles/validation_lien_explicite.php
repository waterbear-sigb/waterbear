<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_validation_lien_explicite()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [param_lien_explicite] : toutes les infos pour générer le lien explicite, à savoir :
 * @param                       [type] => le type d'objet lié (biblio, auteur...)
 * @param                       [plugin_formate] => le plugin utilisé pour mettre en forme le champ à partir de a notice liée
 * @param                       [plugin_get_lien_explicite] => plugin utilisé pour récupérér la notice liée, et la mettre en forme grâce au plugin précédent
 * @param                       [ss_champs_a_conserver] => Les ss champs qu'il ne faut pas écraser
 * @param                       [trier_ss_champs] => Si 1 (ou rien) les ss-champs seront insérés dans l'ordre alpha. Sinon, ils seront ajoutés à la fin
 * @param [plugin_definition_champ] : Contient les infos (type, icones, événements) nécessaires pour générer les ss-champs d'un champ. Utilisé par le plugin ci-dessous'  
 * @param [plugin_get_infos_ss_champ] : Permet de récupérer les infos (type, icones, événements...) des nouveaux ss champs à insérer. Il utilise le plugin ci-dessus qui contient les infos de tous les ss-champs d'un champ  
 * @return array
 * 
 * Ce plugin met à jour un champ lié par un lien explicite.
 * Il regénère totalement le champ à partir du numéro de notice liée et met à jour le formulator
 * au niveau du client comme du serveur
 * 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_validation_lien_explicite ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    //$parametres=plugins_2_param($parametres, array()); // utilise les !! et les ?? 
    
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $param_lien_explicite=$parametres["param_lien_explicite"];
    $trier_ss_champs=$parametres["trier_ss_champs"];
    if ($trier_ss_champs == "") {
        $trier_ss_champs = 1; // valeur par défaut : on trie les ss-champs
    }
    
    $ID_notice_liee=$_REQUEST["valeur"]; // => ID notice liée
    
    $type=$param_lien_explicite["type"];
    $plugin_formate=$param_lien_explicite["plugin_formate"];
    $plugin_get_lien_explicite=$param_lien_explicite["plugin_get_lien_explicite"];
    $ss_champs_a_conserver=$param_lien_explicite["ss_champs_a_conserver"];
    $plugin_definition_champ=$param_lien_explicite["plugin_definition_champ"];
    $plugin_get_infos_ss_champ=$param_lien_explicite["plugin_get_infos_ss_champ"];
    
       
    // 1) On récupère les nouveaux sous-champs
    $tmp=applique_plugin($plugin_get_lien_explicite, array("type"=>$type, "ID"=>$ID_notice_liee, "plugin_formate"=>$plugin_formate));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $champ=$tmp["resultat"]["champ"];
    
    // 2) On vide le champ parent (sauf ss_champs à conserver)
    //$id_champ=$_SESSION["operations"][$ID_operation]["formulator"]->elements[$ID]["parent"];
    $id_champ=$infos["ID_parent"]; // => champ de lien
    $ss_champs_supprimes=$_SESSION["operations"][$ID_operation]["formulator"]->vide_champ($id_champ, $ss_champs_a_conserver);
    foreach ($ss_champs_supprimes as $ss_champ_supprime) {
        array_push($retour["resultat"], 'this_formulator.delete_element('.$ss_champ_supprime.', '.$id_champ.');');
    }
    
    
    // 3) On insère les nouveaux sous-champs
    foreach ($champ as $ss_champ) {
        $code=$ss_champ["code"];
        $valeur=$ss_champ["valeur"];
        $tmp=applique_plugin($plugin_get_infos_ss_champ, array("plugin_definition_champ"=>$plugin_definition_champ, "nom_ss_champ"=>$code));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $infos_ss_champ=$tmp["resultat"]["ss_champ"];
        $infos_ss_champ["valeur"]=$valeur;
        $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($id_champ, $infos_ss_champ, $trier_ss_champs); // le dernier paramètre indique s'il faut trier les ss-champs au moment de l'insertion
        array_push($retour["resultat"], $infos);
        array_push($retour["resultat"], "this_formulator.add_ss_champ(param);");
    }
 
    
    $update=array("valeur"=>$ID_notice_liee);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);

    return ($retour);    
    
}



?>