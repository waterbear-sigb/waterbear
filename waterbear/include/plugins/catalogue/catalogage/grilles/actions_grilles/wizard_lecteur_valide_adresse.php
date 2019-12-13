<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_valide_adresse ()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * 
 * @param [nom_ss_champ_lien] => nom ($a, $b...) du sous champ de lien à modifier
 * 
 * @return array
 * 
 * Ce plugin valide la sélection d'une rue dans une fiche lecteur. 2 possibilités : soit la rue existait dans la db ($ID_notice_liee est préfixée par "id:")
 * dans ce cas, on fait comme d'habitude.
 * soit c'est une adresse tirée de google place ($ID_notice_liee préfixée par "ref:") auquel cas :
 * 1) on effectue une requête pour récupérer les infos détaillées depuis google
 * 2) on dédoublonne pour voir si une rue similaire n'existe pas déjà
 * 3) on génère la notice de rue et on l'enregistre (en récupérant un ID_notice)
 * 4) on continue le processus de création
 * 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_lecteur_valide_adresse ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
        
    
    $infos=$parametres["infos"];
    $ID_operation=$parametres["ID_operation"];
    $ID_element=$parametres["ID_element"];
    $ID_parent=$infos["ID_parent"];
    
    $nom_ss_champ_lien=$parametres["nom_ss_champ_lien"];
    $plugin_google_details=$parametres["plugin_google_details"];
    $plugin_ddbl_adresse=$parametres["plugin_ddbl_adresse"];
    $plugin_crea_notice_rue=$parametres["plugin_crea_notice_rue"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    
    $plugin_analyse_chaine=$parametres["plugin_analyse_chaine"]; // si la rue est fournie sous forme de texte
 
    $ID_notice_liee=$_REQUEST["valeur"]; // => ID notice liée
    $intitule=$_REQUEST["intitule"]; // => intitulé du ss-champ synthétique

    // on maj le ss-champ synthétique coté serveur 
    //$update=array("valeur"=>$intitule);
    //$_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    // on récupère l'id du ss-champ de lien
    $liste_ss_champs=$_SESSION["operations"][$ID_operation]["formulator"]->get_ss_champs_by_nom($ID_parent, $nom_ss_champ_lien);
    if (count ($liste_ss_champs) == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/catalogage/grilles", "ss_champ_inconnu", array("nom_ss_champ"=>$nom_ss_champ_lien));
        return ($retour);
    }
    $id_ss_champ_lien=$liste_ss_champs[0]["id"];
    
    // on analyse la chaine passée pour voir si ça commence par "id" ou par "ref"
    $analyse=explode(":", $ID_notice_liee);
    $type=trim($analyse[0]);
    if ($type != "id" AND $type != "ref" ) {
        $type="chaine";
    } else {
        $ID_notice_liee=trim($analyse[1]);
    }

    
    if ($type == "id") {
        // on ne fait rien
    } elseif ($type == "ref" OR $type == "chaine") {
        if ($type == "chaine") { // si chaine du type "nom_rue : CP ville"
            $tmp=applique_plugin($plugin_analyse_chaine, array("chaine"=>$ID_notice_liee));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
            $rue=$tmp["resultat"]["variables"]["rue"];
            $CP=$tmp["resultat"]["variables"]["CP"];
            $ville=$tmp["resultat"]["variables"]["ville"];
        }elseif ($type == "ref") { // on récupère les détails de l'adresse
            $tmp=applique_plugin($plugin_google_details, array("reference"=>$ID_notice_liee));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }

            $rue=$tmp["resultat"]["rue"];
            $CP=$tmp["resultat"]["CP"];
            $ville=$tmp["resultat"]["ville"];
        } /**elseif ($type=="ign") {
            $tmp=explode("|", $ID_notice_liee);
            $rue=$tmp[0];
            $CP=$tmp[1];
            $ville=$tmp[2];
            $lng=$tmp[3]; // finalement inutile
            $lat=$tmp[4]; // finalement inutile
        }
            **/

        $vedette="$rue : $CP $ville";
        //array_push($retour["resultat"], 'alert("'.$vedette.'");');
        
        // On vérifie que cette adresse n'existe pas déjà (si c'est le cas, on récupère l'id)
        $tmp=applique_plugin($plugin_ddbl_adresse, array("vedette"=>$vedette, "rue"=>$rue, "ville"=>$ville, "CP"=>$CP));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        if ($tmp["resultat"]["notices"][0]["ID"] != "") {
            $ID_notice_liee=$tmp["resultat"]["notices"][0]["ID"];
        } else {
            // On génère la notice
            $tmp=applique_plugin($plugin_crea_notice_rue, array("rue"=>$rue, "CP"=>$CP, "ville"=>$ville));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
            $notice=$tmp["resultat"]["notice"];
            
            // et on la crée
            $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
            $ID_notice_liee=$tmp["resultat"]["ID_notice"];
        }
            
    } else { // au cas où...
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, array("valeur"=>""));
        return ($retour);
    }
    
    // on maj le ss-champ synthétique coté serveur 
    $update=array("valeur"=>$intitule);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    // on maj le ss-champ de lien
    $update=array("valeur"=>$ID_notice_liee);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($id_ss_champ_lien, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].set_valeur("'.$ID_notice_liee.'");');
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$id_ss_champ_lien.'].validation();');

    return ($retour);
    
    
    
    
}



?>