<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_validation_lien_explicite()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [param_lien_explicite] : toutes les infos pour g�n�rer le lien explicite, � savoir :
 * @param                       [type] => le type d'objet li� (biblio, auteur...)
 * @param                       [plugin_formate] => le plugin utilis� pour mettre en forme le champ � partir de a notice li�e
 * @param                       [plugin_get_lien_explicite] => plugin utilis� pour r�cup�r�r la notice li�e, et la mettre en forme gr�ce au plugin pr�c�dent
 * @param                       [ss_champs_a_conserver] => Les ss champs qu'il ne faut pas �craser
 * @param                       [trier_ss_champs] => Si 1 (ou rien) les ss-champs seront ins�r�s dans l'ordre alpha. Sinon, ils seront ajout�s � la fin
 * @param [plugin_definition_champ] : Contient les infos (type, icones, �v�nements) n�cessaires pour g�n�rer les ss-champs d'un champ. Utilis� par le plugin ci-dessous'  
 * @param [plugin_get_infos_ss_champ] : Permet de r�cup�rer les infos (type, icones, �v�nements...) des nouveaux ss champs � ins�rer. Il utilise le plugin ci-dessus qui contient les infos de tous les ss-champs d'un champ  
 * @return array
 * 
 * Ce plugin met � jour un champ li� par un lien explicite.
 * Il reg�n�re totalement le champ � partir du num�ro de notice li�e et met � jour le formulator
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
        $trier_ss_champs = 1; // valeur par d�faut : on trie les ss-champs
    }
    
    $ID_notice_liee=$_REQUEST["valeur"]; // => ID notice li�e
    
    $type=$param_lien_explicite["type"];
    $plugin_formate=$param_lien_explicite["plugin_formate"];
    $plugin_get_lien_explicite=$param_lien_explicite["plugin_get_lien_explicite"];
    $ss_champs_a_conserver=$param_lien_explicite["ss_champs_a_conserver"];
    $plugin_definition_champ=$param_lien_explicite["plugin_definition_champ"];
    $plugin_get_infos_ss_champ=$param_lien_explicite["plugin_get_infos_ss_champ"];
    
       
    // 1) On r�cup�re les nouveaux sous-champs
    $tmp=applique_plugin($plugin_get_lien_explicite, array("type"=>$type, "ID"=>$ID_notice_liee, "plugin_formate"=>$plugin_formate));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $champ=$tmp["resultat"]["champ"];
    
    // 2) On vide le champ parent (sauf ss_champs � conserver)
    //$id_champ=$_SESSION["operations"][$ID_operation]["formulator"]->elements[$ID]["parent"];
    $id_champ=$infos["ID_parent"]; // => champ de lien
    $ss_champs_supprimes=$_SESSION["operations"][$ID_operation]["formulator"]->vide_champ($id_champ, $ss_champs_a_conserver);
    foreach ($ss_champs_supprimes as $ss_champ_supprime) {
        array_push($retour["resultat"], 'this_formulator.delete_element('.$ss_champ_supprime.', '.$id_champ.');');
    }
    
    
    // 3) On ins�re les nouveaux sous-champs
    foreach ($champ as $ss_champ) {
        $code=$ss_champ["code"];
        $valeur=$ss_champ["valeur"];
        $tmp=applique_plugin($plugin_get_infos_ss_champ, array("plugin_definition_champ"=>$plugin_definition_champ, "nom_ss_champ"=>$code));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $infos_ss_champ=$tmp["resultat"]["ss_champ"];
        $infos_ss_champ["valeur"]=$valeur;
        $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($id_champ, $infos_ss_champ, $trier_ss_champs); // le dernier param�tre indique s'il faut trier les ss-champs au moment de l'insertion
        array_push($retour["resultat"], $infos);
        array_push($retour["resultat"], "this_formulator.add_ss_champ(param);");
    }
 
    
    $update=array("valeur"=>$ID_notice_liee);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);

    return ($retour);    
    
}



?>