<?php
/**
 * plugin_catalogue_catalogage_grilles_grille_marc_2_marcxml()
 * 
 * @param mixed $parametres
 * @param [ID_operation]
 * @param [exporte_champs_vides] => si vaut 1 les champs et ss-champs vides seront quand même exportés. sinon, ils seront effacés (défaut)
 * @return array
 * 
 * Ce plugin Convertit les données saisies dans une grille de type unimarc en marcxml (DOMXml seulement !!)
 * Il n'y a pas de transformation, c'est une conversion simple'
 */
function plugin_catalogue_catalogage_grilles_grille_marc_2_marcxml ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];
    $exporte_champs_vides=$parametres["exporte_champs_vides"];

    $tmp=$_SESSION["operations"][$ID_operation]["formulator"]->onglets;
    $str_retour="";
    foreach ($tmp as $idx_onglet => $onglet) { // pour chaque onglet
        foreach ($onglet["champs"] as $idx_champ => $champ) { // pour chaque champ
            $str_champ="";
            $nom_champ=$champ["nom"];
            $bool_valide=0;
            foreach ($champ["ss_champs"] as $idx_ss_champ => $ss_champ) { // pour chaque ss_champ
                $nom_ss_champ=$ss_champ["nom"];
                $valeur_ss_champ=$ss_champ["valeur"];
                $bool_garde_ss_champ_vide=$ss_champ["bool_garde_ss_champ_vide"];
                // gestion des sous-champs qui ont une valeur par défaut mais ne suffisent pas à rendre un champ non vide
                $bool_dependant=$ss_champ["bool_dependant"]; // si vaut 1 alors le ss-champ ne suffit pas à rendre le champ valide quand il a une valeur != ""
                if ($bool_dependant != "1" AND $valeur_ss_champ != "") {
                    $bool_valide=1;
                } elseif ($exporte_champs_vides == 1 OR $bool_garde_ss_champ_vide == 1) {
                    $bool_valide=1;
                }
                if ($valeur_ss_champ != "" OR $exporte_champs_vides == 1 OR $bool_garde_ss_champ_vide == 1) {
                    $valeur_ss_champ=str_replace("<","&lt;",$valeur_ss_champ);
                    $valeur_ss_champ=str_replace("&","&amp;",$valeur_ss_champ);
                    $str_champ.="<subfield code='$nom_ss_champ'>$valeur_ss_champ</subfield>\n";
                }
            }
            if ($str_champ != "" AND $bool_valide == 1) {
                $str_retour .= "<datafield tag='$nom_champ'>\n $str_champ</datafield>\n";
            }
        } 
    }
    if ($str_retour != "") {
        $str_retour = "<record>\n $str_retour </record>";
    } else {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("bib_ws/catalogue/catalogage", "notice_vide", array());
        return ($retour);
    }
    
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $bool=$dom->loadXML($str_retour);
    if ($bool === false) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "xml_impossible_parser", array());
        return ($retour);
    }
    
    /**
    $tmp=applique_plugin(array("nom_plugin"=>"catalogue/marcxml/formatage/biblio/notice/isbd_standard"), array("notice"=>$dom));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $isbd=$tmp["resultat"]["texte"];
    **/
    
    //$retour["resultat"][0]='alert("'.$isbd.'");';
    $retour["resultat"]["notice"]=$dom;
    //$retour["resultat"]["notice"]=$str_retour; // TMP

    return ($retour);    
    
}



?>