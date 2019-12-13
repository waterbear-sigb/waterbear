<?php

/**
 * plugin_catalogue_catalogage_grilles_get_infos_ss_champ()
 * 
 * Ce plugin retourne des informations sur un sous-champ, à savoir quel type (textbox, textarea...)...
 * Il utilise un autre plugin de définition du champ (et qui contient la liste des sous-champs)
 * 
 * @param array $parametres
 * @param [plugin_definition_champ] => le plugin qui va retourner les paramètres sur le champ
 * @param [nom_ss_champ] => le code du ss champ (a, b, c...)
 * @return array
 */
function plugin_catalogue_catalogage_grilles_get_infos_ss_champ ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $tmp=applique_plugin($parametres["plugin_definition_champ"], array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    foreach ($tmp["resultat"]["ss_champs"] as $ss_champ) {
        if ($ss_champ["nom"] == $parametres["nom_ss_champ"]) {
            $retour["resultat"]["ss_champ"]=$ss_champ;
            return ($retour);
        }
    }
    $retour["erreur"]=get_intitule("catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$parametres["nom_ss_champ"]));
    $restour["succes"]=0;
    return ($retour);
    
}



?>