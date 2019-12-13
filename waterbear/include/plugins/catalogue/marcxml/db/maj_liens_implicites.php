<?php
/**
 * 
 * 
 * plugin_catalogue_marcxml_db_maj_liens_implicites()
 * 
 * Ce plugin met à jour les liens implicites d'une notice donnée (fournie par ID ou directement en XML)
 * Il y a forcément une notice déjà présente dans la base et une nouvelle notice qui arrive
 * On commence par lister les types de liens implicites qui peuvent exister pour cette notice
 * Pour chacun, on regarde si la nouvelle notice va apporter une modification par rapport à l'ancienne
 * Si c'est le cas, on récupère toutes les notices liées, et pour chacune, on modife le champ de lien.
 * 
 * @param array $parametres
 * @param ID : ID de la notice
 * @param type : Type de l'objet
 * @param [plugin_get_champ_lie] : plugin utilisé pour récupérer un champ de lien précis (à partir du n° de notice liée)
 * @param [plugin_maj_lien_explicite] : plugin utilisé pour mettre à jour un champ donné d'une notice XML
 * @param [notice_old] *option* : ancienne notice avant modification (si non fourni, trouvé avec ID)
 * @param [notice_new] : nouvelle notice (ne peut être fourni sous forme d'ID)
 * @param [champs_liens_implicites] liste des types de liens implicites susceptibles d'être générés à partir de cette notice !! utilise les paramètres des liens EXPLICITES
 * @param                          [type_lien] => nom du champ qui sera généré dans la notice liée
 * @param                          [type_origine] => type d'objet lié (biblio, auteur...)
 * @param                          [plugin_formate] => le plugin qui va récupérer et mettre en forme les infos dans la notice pour générer le champ de lien
 * @param                          [plugin_notice_2_db] => Le plugin qui permet de regénérer une notice quand un champ de lien y a été modifié 
 * @param                          [ss_champ_jointure] => sous-champ contenant le n° de notice liée (souvent $3) 
 * @param                          [ss_champs_a_conserver] => ss-champs à ne pas écraser 
 * @return array
 */
function plugin_catalogue_marcxml_db_maj_liens_implicites($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    //$parametres=plugins_2_param($parametres, array()); // utilise les !! et les ?? 
    $infos=array(); // stats
    $infos["total"]=0; // stats

    if (! is_array($parametres["champs_liens_implicites"])) {
        return ($retour);
    }
   
    // 1) On récupère l'ancienne notice si pas déjà fournie
    if ($parametres["notice_old"] != "") {
        $notice_old=$parametres["notice_old"];
    } else {
        $notice_old=get_objet_xml_by_id($parametres["type"], $parametres["ID"]);
        if ($notice_old=="") {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/crea_notice", "notice_inexistante", array("type"=>$parametres["type"], "ID"=>$parametres["ID"]));
            return($retour);
        }
    }
    
    // 2) On récupère la nouvelle notice
    $notice_new=$parametres["notice_new"];
    
    // 3) Pour chaque type de lien
    foreach ($parametres["champs_liens_implicites"] as $champ_lien_implicite) {
        $type_origine=$champ_lien_implicite["type_origine"];
        $type_lien=$champ_lien_implicite["type_lien"];
        $plugin_formate=$champ_lien_implicite["plugin_formate"];
        $plugin_notice_2_db=$champ_lien_implicite["plugin_notice_2_db"];
        $ss_champ_jointure=$champ_lien_implicite["ss_champ_jointure"];
        $ss_champs_a_conserver=$champ_lien_implicite["ss_champs_a_conserver"];
        $infos[$type_origine."_".$type_lien]=array(); // stats
        $infos[$type_origine."_".$type_lien]["change"]=0; // stats
        $infos[$type_origine."_".$type_lien]["nb_change"]=0; // stats
        
        // a) on récupère le champ de lien AVANT modification
        $tmp=applique_plugin($plugin_formate, array("notice"=>$notice_old));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $champ_lien_old=$tmp["resultat"]["texte"];
        
        // b) on récupère le champ de lien APRES modification
        $tmp=applique_plugin($plugin_formate, array("notice"=>$notice_new));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $champ_lien_new=$tmp["resultat"]["texte"];
        
        // c) Si les 2 champs sont différents, il faut récupérer toutes les notices liées et les maj
        if ($champ_lien_old !== $champ_lien_new) {
            $infos[$type_origine."_".$type_lien]["change"]=1; // stats
            // c.1 on récupère les notices liées
            //$notices_liees=get_objets_lies($type_origine, $type_lien, $parametres["ID"], $parametres["type"]);
            $notices_liees=get_objets_xml_lies($type_origine, "implicite", $type_lien, $parametres["ID"], $parametres["type"], 1);
     
            
            // c.2 pour chaque notice liée...
            foreach ($notices_liees as $notice_liee_tmp) {
                
                $infos[$type_origine."_".$type_lien]["nb_change"]++; // stats
                $infos["total"]++; // stats
                // c.3 on récupère la notice XML
                //$notice_liee=new DOMDocument();
                //$notice_liee->preserveWhiteSpace = false;
                //$notice_liee->loadXML($notice_liee_tmp["contenu"]);
                //$ID_notice_liee=$notice_liee_tmp["ID"];
                $ID_notice_liee=$notice_liee_tmp["ID"];
                $notice_liee=$notice_liee_tmp["xml"];
                
                
                // c.4 on récupère le(s) champs liés (normalement un seul sauf champs en doublon (ex. 2 fois le même auteur))
                $param_champs=array(0 => array("tag"=>$type_lien, "sous-champs"=>array(0=>array("code"=>$ss_champ_jointure, "valeur"=>$parametres["ID"]))));
                $tmp=applique_plugin($parametres["plugin_get_champ_lie"], array("notice"=>$notice_liee, "champs"=>$param_champs));
                if ($tmp["succes"] != 1) {
                    return ($tmp);
                }
                
                // c.5 on maj les notices
                foreach ($tmp["resultat"]["champs"] as $champ) { // pour chaque champ trouvé (généralement 1)
                
                    // c.6 on maj la notice XML avec le nouveau champ modifié
                    $tmp=applique_plugin($parametres["plugin_maj_lien_explicite"], array("notice"=>$notice_liee, "champ"=>$champ, "champ_remplace"=>$champ_lien_new, "ss_champs_a_conserver"=>$ss_champs_a_conserver, "nom_champ"=>$type_lien));
                    if ($tmp["succes"] != 1) {
                        return ($tmp);
                    }
                    
                    // c.7) On la maj dans la DB
                    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice_liee, "ID_notice"=>$ID_notice_liee));
                    if ($tmp["succes"]==0) {
                        return ($tmp);
                    }
                } // fin du pour chaque champ trouvé
            } // fin du pour chaque notice liée
        } // fin du SI besoin de modifier les notices liées
    } // fin du pour chaque type de lien
    
    $retour["resultat"]["infos"]=$infos;
    return ($retour);
}


?>