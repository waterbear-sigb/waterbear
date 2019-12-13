<?php

/**
 * plugin_catalogue_marcxml_crea_rue()
 * 
 * Ce plugin va générer une notice de rue à partir d'un nom de rue, d'un CP, d'un nom de ville et d'infos de géolocalisation
 * Mais il va d'abord regarder si la ville existe déjà. Si c'est le cas il récupère son ID, sinon, il la crée
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_marcxml_crea_rue ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $plugin_ddbl_ville=$parametres["plugin_ddbl_ville"];
    $plugin_crea_ville=$parametres["plugin_crea_ville"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $plugin_crea_rue=$parametres["plugin_crea_rue"];
    
    $rue=$parametres["rue"];
    $ville=$parametres["ville"];
    $CP=$parametres["CP"];
    $latitude=$parametres["latitude"];
    $longitude=$parametres["longitude"];
    
    // On regarde si la ville existe déjà. Sinon on la crée. On récupère l'ID_notice_ville
    $tmp=applique_plugin($plugin_ddbl_ville, array("ville"=>$ville, "CP"=>$CP));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    if ($tmp["resultat"]["notices"][0]["ID"] != "") { // si notice existante
        $ID_notice_ville=$tmp["resultat"]["notices"][0]["ID"];
    } else {
        $tmp=applique_plugin($plugin_crea_ville, array("ville"=>$ville, "CP"=>$CP));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $notice_ville=$tmp["resultat"]["notice"];
        
        $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice_ville));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $ID_notice_ville=$tmp["resultat"]["ID_notice"];
    }
    
    // On crée la notice de rue
    $tmp=applique_plugin($plugin_crea_rue, array("rue"=>$rue, "ID_notice_ville"=>$ID_notice_ville, "latitude"=>$latitude, "longitude"=>$longitude));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    $retour["resultat"]["notice"]=$notice;
    
    
    return ($retour);
}



?>