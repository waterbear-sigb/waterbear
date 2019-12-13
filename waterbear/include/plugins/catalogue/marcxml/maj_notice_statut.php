<?php

/**
 * plugin_catalogue_marcxml_maj_notice_statut()
 * 
 * Ce plugin de mettre à jour une notice en fonction de paramètre conditionnels fournis sous la forme d'un tableau
 * Typiquement, elle permet de maj le statut d'un exemplaire (avec les infos prêtable, réservable... qui dépendent du statut)
 * 
 * [notice] : notice à modifier
 * [statut] : valeur fournie qui va conditionner les modifications à apporter à la notice
 * [plugin_infos_statut] : plugin qui va retourner les infos de modification à apporter à la notice. Fourni sous la forme d'un tableau :
 *                         [en_rayon][a=>oui, b=>non, c=>oui]... Les codes utilisés ne sont pas forcément des codes de ss-champs, mais n'importe quel
 *                         code qui sera utilisé en variable incluse dans le plugin de maj
 * [plugin_maj] : plugin de maj de la notice : les paramètes sont [statut] et [infos] qui est un tableau avec des clefs pouvant être intégrées à la notice via des variables incluses
 *                          ex. [infos][997_b => toto, 550_a => tutu]
 *  
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_marcxml_maj_notice_statut ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $notice=$parametres["notice"];
    $statut=$parametres["statut"];
    $plugin_infos_statut=$parametres["plugin_infos_statut"];
    $plugin_maj=$parametres["plugin_maj"];
    
    // 1) on récupère les infos de statut
    $tmp=applique_plugin($plugin_infos_statut, array());
    if ($tmp["succes"] != 1) {
        return($retour);
    }
    $infos=$tmp["resultat"][$statut];
    
    // 3) on maj
    $tmp=applique_plugin($plugin_maj, array("notice"=>$notice, "statut"=>$statut, "infos"=>$infos));
    return ($tmp);
    
 
}

?>