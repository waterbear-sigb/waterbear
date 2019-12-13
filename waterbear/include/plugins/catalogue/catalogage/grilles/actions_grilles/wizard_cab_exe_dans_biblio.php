<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_cab_exe_dans_biblio()
 * 
 * Ce plugin gère la saisie directe d'un n° de cab exemplaire dans la notice biblio
 * Si l'exemplaire existe déjà, le plugin se contente de maj (à la manière du catalogage direct)
 * Si l'exe n'existe pas en revanche, le plugin va le créer et maj le ss-champ de lien
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_cab_exe_dans_biblio ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$infos["ID_parent"];
    $valeur_cab=$infos["valeur"]; // valeur du ss-champ cab si déjà saisi
    
    $cab=$_REQUEST["valeur"];
    
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $plugin_enregistrer_notice=$parametres["plugin_enregistrer_notice"];
    $plugin_cab_2_infos=$parametres["plugin_cab_2_infos"];
    $plugin_ddbl=$parametres["plugin_ddbl"];
    $plugin_maj=$parametres["plugin_maj"]; // modif exemplaire quand maj du cab
    $plugin_crea=$parametres["plugin_crea"]; // creation exemplaire si nouveau cab
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $liste_infos_biblio=$parametres["liste_infos_biblio"];
    
        
    // 1) On vérifie si le cab est du bon type
    $tmp=applique_plugin ($plugin_cab_2_infos, array("cab"=>$cab));
    if ($tmp["succes"] != 1) {
        //return ($tmp);
        $message=$tmp["erreur"];
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$valeur_cab.'");');
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    $type_cab=$tmp["resultat"]["infos"]["type"];
    if ($type_cab != "exemplaire") {
        $message=get_intitule("plugins/catalogue/catalogage/grilles", "cab_mauvais_type", array("cab"=>$cab, "type"=>$type_cab, "type_attendu"=>"exemplaire"));
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$valeur_cab.'");');
        array_push($retour["resultat"], 'alert("'.$message.'")');
        return ($retour);
    }
    
    // 2) on vérifie que ce cab n'est pas déjà utilisé
        $tmp=applique_plugin ($plugin_ddbl, array("cab"=>$cab));
        if ($tmp["succes"] != 1) {
            $message=$tmp["erreur"];
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$valeur_cab.'");');
            array_push($retour["resultat"], 'alert("'.$message.'")');
            return ($retour);
        }
        $nb_notices = $tmp["resultat"]["nb_notices"];
        if ($nb_notices > 0) {
            $message=$tmp["erreur"];
            array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$valeur_cab.'");');
            array_push($retour["resultat"], 'alert("Ce code barre est deja utilise")');
            return ($retour);
        }
    
    // 3) on regarde s'il y a un ID_notice de lien (dans le $3)
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_parent, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    $ID_notice_exe=$liste_ss_champs[0]["valeur"];
    
    
      
    // 4) S'il y a déjà un ID dans le ss-champ de lien
    if ($ID_notice_exe != "") {
        
        
        // a) on maj la notice
        //$ID_notice_exe=$tmp["resultat"]["notices"][0]["ID"];
        $tmp=applique_plugin($plugin_maj, array("cab"=>$cab));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $notice_exe=$tmp["resultat"]["notice"];
         
        // b) on enregistre la notice dans la db
        $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice_exe, "ID_notice"=>$ID_notice_exe));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
    
    } else {
        // a) On enregistre la notice
        $tmp=applique_plugin($plugin_enregistrer_notice, array("ID_operation"=>$ID_operation));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
    
        $ID_notice=$_SESSION["operations"][$ID_operation]["ID_notice"];
        
        
        // b) on récupère les infos pertinentes de la notice biblio (cote, prix...)
        $param_biblio=array();
        $notice=get_objet_xml_by_id("biblio", $ID_notice);
        foreach ($liste_infos_biblio as $code_info_biblio => $plugin_info_biblio) {
            $tmp=applique_plugin ($plugin_info_biblio, array("notice"=>$notice));
            if ($tmp["succes"] != 1) {
                $param_biblio[$code_info_biblio]="erreur";
            } else {
                $param_biblio[$code_info_biblio]=$tmp["resultat"]["texte"];
            }
        }
        
        // c) On crée la notice exe
        $tmp=applique_plugin($plugin_crea, array("cab"=>$cab, "param_biblio"=>$param_biblio));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $notice_exe=$tmp["resultat"]["notice"];
        
        // d) on enregistre la notice exe
        $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice_exe, "ID_notice"=>$ID_notice_exe));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $ID_notice_exe=$tmp["resultat"]["ID_notice"];
        
        // e) On maj ss-champ de lien
        $update=array("valeur"=>$ID_notice_exe);
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($id_ss_champ_lien, $update);
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_notice_exe.'");');
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');
        
        
        
    } // fin du si ID ss-champ de lien inexistant
    
    // 5) on maj le cab coté serveur
        $update=array("valeur"=>$cab);
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    // 6) On enregistre à nouveau la notice
    $tmp=applique_plugin($plugin_enregistrer_notice, array("ID_operation"=>$ID_operation));
    if ($tmp["succes"] != 1) {
        return($tmp);
    }
   
    
    return($retour);
}



?>