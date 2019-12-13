<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_champ_200()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @param [plugin_marcxml] => plugin (statique) pour convertir la grille en marcxml
 * @param [plugin_7xx_2_200] => plugin qui va extraire les infos des champs 7XX et retourner qqchse du genre "cf:kghkk|cf:gliglig|cf:ygoygoyg..."
 * @param [auto_plugin_200_f] => plugin qui va générer le champ 200$f
 * @param [auto_plugin_200_g] => plugin qui va générer le champ 200$g
 * @param [chemin_intitules_fonctions] => Chemin vers la liste qui contient les intitulés des fonctions (trad. par, ill. par...)
 * @param 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_champ_200 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    
    // 1) Supprimer les ss-champs $f et $g
    $liste_f=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "f");
    $liste_g=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_element, "g");
    $liste=array_merge($liste_f, $liste_g);
    foreach ($liste as $ss_champ_a_supprimer) {
        $_SESSION["operations"][$ID_operation]["formulator"]->delete_element($ss_champ_a_supprimer["id"]);
        array_push($retour["resultat"], 'this_formulator.delete_element('.$ss_champ_a_supprimer["id"].', '.$ID_element.');');
    }
    
    // 2) conversion de la notice en marcxml
    $tmp=applique_plugin($parametres["plugin_marcxml"], array("ID_operation"=>$ID_operation));
    if ($tmp["succes"]!=1) {
        return ($tmp);
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 3) on récupère les infos des champs 7** sous la forme "cf:toto tutu|cf:robert dupont|..."
    //    avec cf = code fonction. Les champs les plus importants 700, 710, 720
    $tmp=applique_plugin($parametres["plugin_7xx_2_200"], array("notice"=>$notice));
    if ($tmp["succes"]!=1) {
        return ($tmp);
    }
    $str_tmp=$tmp["resultat"]["texte"];
    
    // 4) On répartit les infos obtenues par code fonction
    $liste_retour=array();
    $t1=explode("|", $str_tmp);
    foreach ($t1 as $t1_elem) {
        if ($t1_elem == "") {
            continue;
        }
        $t2=explode(":", $t1_elem, 2);
        if (count($t2)==1) {
            $cf="070";
            $vedette=$t2[0];
        } else {
            $cf=$t2[0];
            $vedette=$t2[1];
        }
        if (!is_array($liste_retour[$cf])) {
            $liste_retour[$cf]=array();
        }
        array_push($liste_retour[$cf], $vedette);
    }
    
    // 5) On génère les ss-champs $f et $g
    $idx=0;
    foreach ($liste_retour as $cf => $liste_vedettes) {
        $intitule=get_intitule($parametres["chemin_intitules_fonctions"], $cf, array());
        if (strpos($intitule, "?") !== false) { // si jamais on n'avait pas défini cette fonction
            $intitule="";
        }
        if ($intitule != "") {
            $intitule.=" ";
        }
        $str_vedette=$intitule.implode(", ", $liste_vedettes);
        if ($idx==0) { // si 1er on crée un $f
            $tmp=applique_plugin($parametres["auto_plugin_200_f"], array()); 
        } else { // sinon un $g
            $tmp=applique_plugin($parametres["auto_plugin_200_g"], array());
        }
        if ($tmp["succes"] != 1) {
            return ($tmp);
        } 
        $tmp["resultat"]["valeur"]=$str_vedette;
        $infos=$_SESSION["operations"][$ID_operation]["formulator"]->insere_ss_champ ($ID_element, $tmp["resultat"]);
        array_push($retour["resultat"],$infos);
        array_push($retour["resultat"],"this_formulator.add_ss_champ(param);");
        $idx++;
    }
    
    
    return ($retour); 
}


?>