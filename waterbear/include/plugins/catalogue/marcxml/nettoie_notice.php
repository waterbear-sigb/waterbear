<?php

function plugin_catalogue_marcxml_nettoie_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $notice_xml=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $ID_notice=$parametres["ID_notice"];
    $type_doc=$parametres["type_doc"];
    
    $filtre=$parametres["filtre"];
    
    if ($tvs_marcxml == "") {
        if ($notice_xml=="") {
            if ($ID_notice == "" OR $type_doc == "") {
                $retour["succes"]=0;
                $retour["erreur"]="@& get_datafields : Vous n'avez fourni aucune notice";
                return($retour);
            }
            $notice_xml=get_objet_xml_by_id($type_doc, $ID_notice);
        }
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice_xml);
        
    }
    
    $tvs_marcxml->nettoie_notice($filtre);
    $retour["resultat"]["tvs_marcxml"]=$tvs_marcxml;
    
    
    return ($retour);
}


?>