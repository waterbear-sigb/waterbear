<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_maj_champ_valeur()
 * 
 * Ce  plugin permet de mettre à jour les ss-champs d'un champ en fonction de la valeur fournie dans $_REQUEST["valeur"] (ou parametres[valeur])
 * Il se base sur les infos fournies par la plugin [plugin_get_infos] qui indique les ss-champs à modifier en fonction de telle ou telle valeur
 * On aura par exemple [A] => [a=>toto, b=>tutu], [B] => [a=>caca, c=>pipi]
 * ==> Si $_REQUEST["valeur"] vaut A, on fait $a = toto et $b = tutu, si $_REQUEST["valeur"] vaut B...
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [plugin_get_infos] => le plugin qui indique comment modifier les ss-champs en fonction de la valeur fournie
 * @param [valeur] => si fourni, on se base dessus. Sinon, on appelle $_REQUEST["valeur"]
 * 
 * 
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_maj_champ_valeur ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_operation=$parametres["ID_operation"];
    $formulator=$_SESSION["operations"][$ID_operation]["formulator"];
    $ID_element=$parametres["ID_element"];
    
    $plugin_get_infos=$parametres["plugin_get_infos"];
    if ($parametres["valeur"] == "") {
        $valeur=$_REQUEST["valeur"];
    } else {
        $valeur=$parametres["valeur"];
    }
    
    // 1) on récupère les infos
    $tmp=applique_plugin($plugin_get_infos, array());
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $infos=$tmp["resultat"];
    
    // 2) on maj les sous-champs en fonction des infos récupérées
    $liste_ss_champs=$formulator->get_ss_champs_by_nom($ID_element, "");
    foreach ($liste_ss_champs as $ss_champ) {
        $nom=$ss_champ["nom"];
        $id=$ss_champ["id"];
        foreach ($infos as $valeur_info => $modifs) {
            if ($valeur_info == $valeur) { // si c'est la bonne valeur, on modifie les ss-champs
                foreach ($modifs as $code_modif => $valeur_modif) {
                    $liste_ss_champs_modif=$formulator->get_ss_champs_by_nom($ID_element, "$code_modif");
                    foreach ($liste_ss_champs_modif as $ss_champ_modif) {
                        $id_modif=$ss_champ_modif["id"];
                        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_modif.'].set_valeur("'.$valeur_modif.'");');
                        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_modif.'].validation();');
                    }
                }
                
            }
        }
    }
    
    
    return($retour);
}

?>