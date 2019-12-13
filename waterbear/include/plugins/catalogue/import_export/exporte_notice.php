<?php

function plugin_catalogue_import_export_exporte_notice($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $notice2=$parametres["notice"];
    $tvs_marcxml2=$parametres["tvs_marcxml"];
    $ID_notice=$parametres["ID_notice"];
    $type_obj=$parametres["type_obj"];
    $plugin_xml2marc=$parametres["plugin_xml2marc"];
    $plugin_array_2_iso2709=$parametres["plugin_array_2_iso2709"];
    $plugin_maj=$parametres["plugin_maj"];
    
    // On récupère la notice
    // ATTENTION : on doit dupliquer l'objet XML (si fourni via $notice ou $tvs_marcxml), car cette méthode peut le modifier en étant appelé par d'autres plugins qui eux ne veulent pas le modifier
    // par exemple l'export de la colonne a_unimarc (notice en unimarc iso) modifie l'objet xml (par ex. convertit le 997 et 995...) ce qui ensuite pourrait altérer la notice enregistrée dans la base
  
    if ($tvs_marcxml2 == "" AND $notice2 == "" AND $ID_notice == "") {
         $retour["succes"]=0;
         $retour["erreur"]="Aucune notice fournie";
         return ($retour);
    }
    
    if ($tvs_marcxml2 == "" AND $notice2 == "") { // notice fournie via $ID_notice et $type_obj
        $notice=get_objet_xml_by_id($type_obj, $ID_notice);
    } elseif ($tvs_marcxml2 != "") { // notice fournie via $tvs_marcxml2
         $notice2=$tvs_marcxml2->notice;
    } else { // notice fournie via $notice2
        // on ne fait rien 
    }
    
    if ($notice2 != "") {
        $txt=$notice2->saveXML();
        $notice=new DOMDocument();
        $notice->preserveWhiteSpace = false;
        $notice->loadXML($txt);
    }
    
    $tvs_marcxml=new tvs_marcxml(array());
    $tvs_marcxml->load_notice($notice);

    
    // transformation de la notice
    if (is_array($plugin_maj)) {
        $tmp=applique_plugin($plugin_maj, array("notice"=>$tvs_marcxml->notice));
        if ($tmp["succes"] != 1) {
            return($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
        $tvs_marcxml->load_notice($notice);
    }
    
    
    
    // On convertit la notice en array conforme à marcxml (gestion des datafields, des indicateurs...)
    $tmp=applique_plugin($plugin_xml2marc, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $array_marc=$tmp["resultat"]["notice"];
    
    // On convertit cette array en chaine de caratres iso2709
    $tmp=applique_plugin($plugin_array_2_iso2709, array("notice"=>$array_marc));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
   
   $retour["resultat"]["notice"]=$tmp["resultat"]["notice"];    
    
    
    
    
    
    
    
    return ($retour);
}

?>