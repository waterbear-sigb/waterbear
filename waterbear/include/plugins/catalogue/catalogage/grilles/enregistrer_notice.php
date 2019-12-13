<?php

/**
 * plugin_catalogue_catalogage_grilles_enregistrer_notice()
 * 
 * [ID_operation] => ID de l'opération
 * 
 * [plugin_marcxml] => le plugin à utiliser pour convertir les données de la grille en marcxml
 * [plugin_notice_2_db] => le plugin qui crée ou maj la notice dans la DB. Récupère accès..., gère liens implicites
 * 
 * Ce plugin valide une notice saisie dans une grille. Il appelle une série de plugins pour convertir la grille en marcxml
 * puis en extraire les données (accès, tris, liens explicites), mettre à jour les liens implicites et enregistrer le résultat dans la DB
 * 
 */
function plugin_catalogue_catalogage_grilles_enregistrer_notice($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $plugin_verifie_notice=$parametres["plugin_verifie_notice"];
    
    // 1) On regarde s'il y a un $ID_notice au niveau de l'opération (pour savoir si maj ou création)
    $ID_notice=$_SESSION["operations"][$parametres["ID_operation"]]["ID_notice"];

    // 2) Convertir la grille en MarcXML
    $tmp=applique_plugin($parametres["plugin_marcxml"], array("ID_operation"=>$parametres["ID_operation"]));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $notice_xml=$tmp["resultat"]["notice"];
    
    // 2bis) on rajoute un champ 000 avec ID_notice (si existant)
    if ($ID_notice != "") {
        $tvs_marcxml=new tvs_marcxml(array("ID"=>$ID_notice, "type_obj"=>"xxx"));
        $tvs_marcxml->load_notice($notice_xml);
        $tvs_marcxml->add_champ_000();
        $notice_xml=$tvs_marcxml->notice;
    }
    
    // 3) *option* on vérifie que la notice est conforme
    if (is_array($plugin_verifie_notice)) {
        $tmp=applique_plugin($plugin_verifie_notice, array("notice"=>$notice_xml));
        if ($tmp["succes"]!=1) {
            return ($tmp);
        }
        if ($tmp["resultat"]["message"] != "") {
            $retour["succes"]=0;
            $retour["erreur"]=$tmp["resultat"]["message"];
            return($retour);
        }
    }
    
    // 4) On la crée ou la maj dans la DB
    $tmp=applique_plugin($parametres["plugin_notice_2_db"], array("notice"=>$notice_xml, "ID_notice"=>$ID_notice));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $ID_notice=$tmp["resultat"]["ID_notice"];
    $_SESSION["operations"][$parametres["ID_operation"]]["ID_notice"]=$ID_notice;
    
    $retour["resultat"][0]='this_formulator.set_id_notice('.$ID_notice.');';
    $retour["resultat"][1]='this_formulator.post_enregistrer_notice('.$ID_notice.');';
    
    return ($retour);
   
    
    
}


?>