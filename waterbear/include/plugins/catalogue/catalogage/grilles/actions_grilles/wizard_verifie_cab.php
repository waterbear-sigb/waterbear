<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_verifie_cab()
 * 
 * ATTENTION le cab est récupéré par $_REQUEST["valeur"];
 * 
 * Ce plugin vérifie un cab (exemplaire ou lecteur ou ISBN)
 * > * option * on formate le cab (ex. ISBN => EAN) avec le plugin plugin_formate_cab : ATTENTION à la signature [chaine] => plugin => [EAN] Si on utilise un plugin avec une autre signature, il faudra des alias
 * > Est-ce que le cab est bien formé. Pour cela [plugin_cab_2_infos] retourne le type_doc correspondant au cab, et on le compare avec [type_doc]
 * > Est-ce que le cab n'est pas déjà utilisé ? Pour cela, on utilise [plugin_ddb] qui fait une recherche qui doit retourner normalement 0 notices
 * 
 * Si tout est OKon valide
 * 
 *  
 * @param mixed $parametres
 * @param [plugin_formate_cab] => *option* plugin qui formate le cab (ex. ISBN => EAN) [chaine] => plugin => [EAN]
 * @param [plugin_cab_2_infos] => retourne lecteur ou exemplaire (ou autre)
 * @param [plugin_ddbl] => plugin de recherche de doublons de cab
 * @param [bool_laisse_si_doublon] => si vaut 0 (defaut) on interdit de créer un doublon. Si vaut 1, on autorise les doublons, mais on envoie un message (ex. on peut avoir 2 livres avec le même isbn)
 * @param [bool_affiche_doublon] => si vaut 1, on lance une requete pour afficher le doublon (à partir de l'id notice)
 * @param [url_affiche_doublon] => url pour affichage du doublon avec ID_notice à la fin
 * @param [type_obj] => type d'objet attendu (lecteur, exemplaire...)
 * 
 * @param [ID_element] => l'ID de l'élément du formaulaire
 * 
 * @return liste de commandes
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_verifie_cab ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $plugin_cab_2_infos=$parametres["plugin_cab_2_infos"];
    $plugin_ddbl=$parametres["plugin_ddbl"];
    $plugin_formate_cab=$parametres["plugin_formate_cab"];
    $bool_laisse_si_doublon=$parametres["bool_laisse_si_doublon"];
    $bool_affiche_doublon=$parametres["bool_affiche_doublon"];
    $url_affiche_doublon=$parametres["url_affiche_doublon"];
    $cab=$_REQUEST["valeur"];
    $type_obj=$parametres["type_obj"];
    $ID_element=$parametres["ID_element"];
    $ID_operation=$parametres["ID_operation"];
    
    // 0) *option* on formate le cab
    if ($plugin_formate_cab != "") {
        $tmp=applique_plugin ($plugin_formate_cab, array("chaine"=>$cab));
        if ($tmp["succes"] != 1) {
            $message=$tmp["erreur"];
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");');
            array_push($retour["resultat"], 'alert("'.$message.'")');
            return ($retour);
        }
        $cab=$tmp["resultat"]["EAN"];
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$cab.'");');
    }
    
    // 1) On teste le type de cab
    $tmp=applique_plugin ($plugin_cab_2_infos, array("cab"=>$cab));
    if ($tmp["succes"] != 1) {
        //return ($tmp);
        $message=$tmp["erreur"];
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");');
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    $type_cab=$tmp["resultat"]["infos"]["type"];
    if ($type_cab != $type_obj) {
        $message=get_intitule("plugins/catalogue/catalogage/grilles", "cab_mauvais_type", array("cab"=>$cab, "type"=>$type_cab, "type_attendu"=>$type_obj));
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");');
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    
    // 2) On dédoublonne
    $tmp=applique_plugin ($plugin_ddbl, array("cab"=>$cab));
    if ($tmp["succes"] != 1) {
        $message=$tmp["erreur"];
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");');
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    $nb_notices = $tmp["resultat"]["nb_notices"];
    $ID_notice = $tmp["resultat"]["notices"][0]["ID"];
    if ($nb_notices >= 1) {
        if ($bool_laisse_si_doublon != 1) {
             
            $message=get_intitule("plugins/catalogue/catalogage/grilles", "cab_deja_utilise", array("cab"=>$cab, "type"=>$type_cab, "type_attendu"=>$type_obj));
            array_push($retour["resultat"], 'alert("'.$message.'")');
            if ($bool_affiche_doublon==1) {
                array_push($retour["resultat"], 'this_formulator.maj_bool_modif(0);');
                array_push($retour["resultat"], "window.location.href=\"".$url_affiche_doublon.$ID_notice."\";");
            } else {
                array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");'); // raz le champ
            }
            return ($retour);
        }
    }
    
    
    // Si tout est OK, on valide
    $update=array("valeur"=>$cab);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    return ($retour);
}

?>