<?php

function plugin_catalogue_catalogage_grilles_switcher ($parametres) {
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $idx_onglet=$parametres["idx_onglet"];
    $action=$parametres["action"];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]="";

    if ($ID_element != "") { // Si champ ou sous-champ
        $infos=$_SESSION["operations"][$ID_operation]["formulator"]->get_infos_element($ID_element);
    } elseif ($idx_onglet != "") { // Si onglet
        $infos=array("type_element"=>"onglet", "idx_onglet"=>$idx_onglet);
    } 
    //dbg_log(var_export($infos, true));
    $plugin="";
    if ($infos["type_element"]=="onglet") { // onglet
        if (isset($parametres["liste_onglets"][$idx_onglet][$action])) {
            $plugin=$parametres["liste_onglets"][$idx_onglet][$action];
        } else {
            $plugin=$parametres["defaut_onglet"][$action];
        }
    } elseif ($infos["type_element"]=="champ") {  // champ
        if (isset($parametres["liste_champs"][$infos["nom_champ"]][$action])) {
            $plugin=$parametres["liste_champs"][$infos["nom_champ"]][$action];
        } else {
            $plugin=$parametres["defaut_champ"][$action];
        }
    } elseif ($infos["type_element"]=="ss_champ") { // ss-champ
        if (isset($parametres["liste_champs"][$infos["nom_champ"]][$infos["nom_ss_champ"]][$action])) {
            $plugin=$parametres["liste_champs"][$infos["nom_champ"]][$infos["nom_ss_champ"]][$action];
        } else {
            $plugin=$parametres["defaut_ss_champ"][$action];
        }
    } else { // formulaire
        $plugin=$parametres["liste_formulaire"][$action];
    }
    /**
   try {
        $parametres["infos"]=$infos;
        $retour=applique_plugin($plugin, $parametres);
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
        return ($retour);
    }
    **/
    
    // Si cette branche du switcher n'existe pas mais qu'on hérite d'un autre switcher...
    if (!isset($plugin["nom_plugin"]) AND $parametres["switcher_herite"] != "") {
        $retour=applique_plugin($parametres["switcher_herite"], $_REQUEST);
        return ($retour);
    }
    
    $parametres["infos"]=$infos; 
    $retour=applique_plugin($plugin, $parametres);
    return ($retour);
}


?>