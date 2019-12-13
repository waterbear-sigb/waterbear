<?php

/**
 * plugin_catalogue_marcxml_db_notice_2_infos()
 * 
 * Ce plugin extrait les infos d'une notice afin de l'enregistrer dans la db (plugin crea_db)
 * Il va utiliser plusieurs plugins pour récupérer chaque type d'information (acces, tri, liens)
 * 
 * On peut fournir directement la notice OU un ID, dans ce cas la notice est récupérée dans la base
 * 
 * @param array $parametres
 * @param [plugin_acces] => plugin utilisé pour récupérer les accès
 * @param [plugin_tri] => pour récupérer les tris
 * @param [plugin_liens_explicites] => pour récupérer les liens
 * @param [type] => type d'objet (biblio, auteur...)
 * @param SOIT [notice] => la notice en XML
 * @param SOIT [ID_notice] => ID_notice (pour récupérer la notice)
 * @return array
 */
function plugin_catalogue_marcxml_db_notice_2_infos ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // 1) Si la notice n'est pas fournie on la récupère via ID_notice
    if ($parametres["notice"] == "") {
        $tmp=get_objet_by_id($parametres["type"], $parametres["ID_notice"]);
        if ($tmp == "") {
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/crea_notice", "notice_inexistante", array("type"=>$parametres["type"], "ID"=>$parametres["ID"]));
            return($retour);
        }
        $tmp2=$tmp["contenu"];
        $notice=new DOMDocument();
        $notice->preserveWhiteSpace = false;
        $test=$notice->loadXML($tmp2);
        if ($test === false) {
            $notice="";
        }
    } else {
        $notice=$parametres["notice"];
    }
    
    if ($notice=="") { // notice vide ou false
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/crea_notice", "xml_mal_forme", array());
        return($retour);
    }
    
        
    // 2) On récupère les accès
    $tmp=applique_plugin($parametres["plugin_acces"], array("notice"=>$notice));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $retour["resultat"]["acces"]=$tmp["resultat"];
    
    // 3) On récupère les tris
    $tmp=applique_plugin($parametres["plugin_tri"], array("notice"=>$notice));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $retour["resultat"]["tri"]=$tmp["resultat"];
    
    // 4) On récupère le contenu
    $retour["resultat"]["contenu"]=$notice->saveXML();
    
    //5) On récupère les liens
    if ($parametres["plugin_liens_explicites"] != "") {
        $tmp=applique_plugin($parametres["plugin_liens_explicites"], array("notice"=>$notice));
        if ($tmp["succes"]==0) {
            return ($tmp);
        }
        $retour["resultat"]["liens"]=$tmp["resultat"]["liens"];
    }
    
    return ($retour);
}


?>