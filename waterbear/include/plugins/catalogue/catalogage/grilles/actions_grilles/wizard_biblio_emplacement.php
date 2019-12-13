<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_biblio_emplacement()
 * 
 * Ce wizard var sugg�rer un emplacement � partir d'infos pr�sentes dans le champ 997 (biblio) ou 110 (exe) (cote, type doc, section...)
 * Peut �tre utilis� dans la notice biblio ou dans la notice exe, mais avec des param�tres diff�rents
 * car les ss-champs ne sont aps les m�mes
 * 
 * [ss_champs_pertinents][0,1,2...] indique les codes des ss-champs du champ 997 qui sont pertinents (dans l'ordre) pour d�duire l'emplacement
 * [plugin_emplacement] plugin qui va retourner l'emplacement dans [code]
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_biblio_emplacement ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$infos["ID_parent"];
    
    $ss_champs_pertinents=$parametres["ss_champs_pertinents"];
    $plugin_emplacement=$parametres["plugin_emplacement"];
    
    $infos=array();
    
    // 1) on r�cup�re les infos pertinentes dans le champ (cote, section...)
    foreach ($ss_champs_pertinents as $ss_champ_pertinent) {
        $ss_champs=$formulator->get_ss_champs_by_nom($ID_parent, $ss_champ_pertinent);
        array_push($infos, $ss_champs[0]["valeur"]);
    }
    
    // 2) On r�cup�re l'emplacement � partir de ces infos
    $tmp=applique_plugin($plugin_emplacement, array("infos"=>$infos));
    if ($tmp["succes"]!=1) {
        return ($tmp);
    }
    $code=$tmp["resultat"]["code"];
    
    // 3) on modifie le ss-champ cot� serveur et cot� client
    $update=array("valeur"=>$code);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$code.'");');
    
    return ($retour);
}



?>