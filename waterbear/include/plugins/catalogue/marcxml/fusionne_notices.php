<?php
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/fusion_marcxml.php");

/**
 * plugin_catalogue_marcxml_fusionne_notices()
 * 
 * Ce plugin permet de fusionner 2 notices selon un filtre
 * Les notices sont soit au format marcxml, soit en DOMXML (voir même XML string) soit simplement des ID de notices
 * en cas de modification de notices, c'est la notice A qui est conservée
 * 
 * @param mixed $parametres
 * @param SOIT [marcxml_a] et [marcxml_b] => notices en marcxml (le mieux !)
 * @param SOIT [xml_a] et [xml_b] => notices en DOMXml ou en XML string
 * @param SOIT [ID_notice_a] et [ID_notice_b] => ID de notices (la notice est récupérée dans la DB)
 * @param [type_obj]
 * @param [ID_notice] => si modification de notice. Peut correspondre à [ID_notice_a]
 * @param [format_retour] => "domxml" ou "marcxml" (défaut)
 * @param [filtre]
 * @param [------][nettoie_a | nettoie_b] => supprime certains champs / sous-champs des notices (cf détail + bas)
 * @param [------][fusion] => fusionne les 2 notices (cf détail + bas)
 * 
 * ** DETAIL NETTOYAGE **
 * @param [defaut_champ]=>garder | supprimer
 * @param [defaut_ss_champ] => garder | supprimer
 * @param SOIT [champs][110,200,210...][action]=>garder|supprimer 
 * @param SOIT [champs][110,200,210...][ss_champs][a,b,c...][action]=>garder|supprimer
 * 
 * ** DETAIL FUSION **
 * @param [defaut_champ] => action par défaut pour tous les champs (ajouter | remplacer | ajouter_si_existe_pas | inserer |supprimer)
 * @param [defaut_ddbl] => dédoublonnage par défaut pour les champs (nom_champ | criteres | rien)
 * @param [champs][210, 700, 676...][ddbl]
 * @param [champs][210, 700, 676...][action]
 * @param [champs][210, 700, 676...][ss_champ_ddbl] => Pour le cas où on aurait un ddbl de type "criteres", on met ici le sous-champ de dédoublonnage. Ex si on met 3, il dédoublonnera les champs dont les $3 sont identiques
 * @param [champs][210, 700, 676...][defaut_ss_champs] => action par défaut pour les ss-champs de ce champ en cas d'insertion
 * @param [champs][210, 700, 676...][ss_champs][a,b,c...][action] => (ajouter | remplacer | ajouter_si_existe_pas | rien)
 * 
 * 
 * @return [notice]
 */
function plugin_catalogue_marcxml_fusionne_notices ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    
    // 1) On s'assure d'avoir 2 objets de type marcxml
    $xml_a=$parametres["xml_a"];
    $xml_b=$parametres["xml_b"];
    if ($parametres["ID_notice_a"] != "") {
        $xml_a=get_objet_xml_by_id($parametres["type_obj"], $parametres["ID_notice_a"]);
    }
    
    if ($parametres["ID_notice_b"] != "") {
        $xml_b=get_objet_xml_by_id($parametres["type_obj"], $parametres["ID_notice_b"]);
    }
    
    if ($xml_a != "") {
        $marcxml_a=new tvs_marcxml(array("type_obj"=>$parametres["type_obj"]));
        $marcxml_a->load_notice($xml_a);
    } else {
        $marcxml_a=$parametres["marcxml_a"];
    }
    
    if ($xml_b != "") {
        $marcxml_b=new tvs_marcxml(array("type_obj"=>$parametres["type_obj"]));
        $marcxml_b->load_notice($xml_b);
    } else {
        $marcxml_b=$parametres["marcxml_b"];
    }
    
    // 2) On crée l'objet de fusion
    $fusion=new fusion_marcxml(array("notice_a"=>$marcxml_a, "notice_b"=>$marcxml_b, "type_objet"=>$parametres["type_obj"], "ID_notice"=>$parametres["ID_notice"]));
    
    // 3) On nettoie les notices
    if (is_array($parametres["filtre"]["nettoie_a"])) {
        $fusion->notice_a=$fusion->nettoie_notice($fusion->notice_a, $parametres["filtre"]["nettoie_a"]);
    }
    
    if (is_array($parametres["filtre"]["nettoie_b"])) {
        $fusion->notice_b=$fusion->nettoie_notice($fusion->notice_b, $parametres["filtre"]["nettoie_b"]);
    }
    
    // 4) On fusionne
    $tmp=$fusion->fusionne_notices($parametres["filtre"]["fusion"]);
    
    if ($parametres["format_retour"] == "domxml") {
        $retour["resultat"]["notice"]=$tmp->notice;
    } else {
        $retour["resultat"]["notice"]=$tmp;
    }
    
    
    return ($retour);
} // fin du plugin

?>