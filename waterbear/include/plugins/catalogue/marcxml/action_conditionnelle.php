<?php
/**
 * plugin_catalogue_marcxml_action_conditionnelle()
 * 
 * Ce plugin est utilisé lorsqu'on veut tester la valeur de certains champs d'une notice avant d'effectuer une action
 * 
 * Pour faciliter le traitement, la grille de saisie est d'abord convertie en xml, ce qui permet d'utiliser les outils de formatage puissants comme get_datafields...
 * 
 *  * 2) extraction des infos pertinentes grâce à des plugins de type get_datafields...
 * 3) évaluation de la chaine trouvée par un plugin de type div/util_str_choix (retourne 0 ou 1)
 * 4) envoi d'un message d'erreur OU exécution du plugin voulu
 *  
 * @param mixed $parametres
 * @param [notice] OU [ID_notice] et [type_doc] OU [tvs_marcxml] => la notice à tester 
 * @param [plugin_formate] => extrait les infos pertinentes de la notice (généralement get_datafields) [notice] => plugin => [texte]
 * @param [plugin_evaluation] => évalue la chaine trouvée. retourne 0 ou 1 [texte] => plugin => [texte]
 * @param [plugin_action] => action à effectuer si c'est 1 [parametres] => plugin
 * @param [message_erreur] => message à retourner si c'est 0 * 
 * 
 * @return [...] => variable suivant le [plugin_action]
 */
function plugin_catalogue_marcxml_action_conditionnelle ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    

    $plugin_formate=$parametres["plugin_formate"];
    $plugin_evaluation=$parametres["plugin_evaluation"];
    $plugin_action=$parametres["plugin_action"];
    $message_erreur=$parametres["message_erreur"];
    
    
    
    // 2) on formate la notice pour récupérer l'info pertinente
    $tmp=applique_plugin ($plugin_formate, $parametres);
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $chaine=$tmp["resultat"]["texte"];
    
    // 3) On évalue cette chaine et on récupère 0 ou 1
    $tmp=applique_plugin ($plugin_evaluation, array("texte"=>$chaine));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $bool=$tmp["resultat"]["texte"];
    
    // 4) Si Impossible :
    if ($bool != 1) {
        $retour["succes"]=0;
        $retour["erreur"]=$message_erreur;
        return ($retour);
    }
    
    // 5) Si possible 
    unset ($parametres["plugin_formate"]); // on efface pour que ça ne rentre pas enconflit avec des paramètres fournis via le registre
    unset ($parametres["plugin_evaluation"]); // on efface pour que ça ne rentre pas enconflit avec des paramètres fournis via le registre
    unset ($parametres["plugin_action"]); // on efface pour que ça ne rentre pas enconflit avec des paramètres fournis via le registre
    unset ($parametres["message_erreur"]); // on efface pour que ça ne rentre pas enconflit avec des paramètres fournis via le registre
    $tmp=applique_plugin ($plugin_action, $parametres);
    return ($tmp);
    
    
    
    
    
}



?>