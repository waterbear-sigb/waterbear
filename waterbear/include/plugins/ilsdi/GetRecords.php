<?php

function plugin_ilsdi_GetRecords ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID_notices=$_REQUEST["ID_notices"];
    
    $plugin_get_titre=$parametres["plugin_get_titre"];
    $plugin_get_nom_bib=$parametres["plugin_get_nom_bib"];
    $plugin_get_pret=$parametres["plugin_get_pret"];
    $bool_reserver_disponibles=$parametres["bool_reserver_disponibles"];
    $separateur=$parametres["separateur"];
    
    if ($separateur == "") {
        $separateur=",";
    }
    
    // 1) on récupère les ID des notices
    $liste_ids=explode($separateur, $ID_notices);
    $notices=array();
    
    foreach ($liste_ids as $ID_notice) { // pour chaque notice
        $notice=array();
        // 1) on récupère la notice biblio
        $notice_biblio=get_objet_xml_by_id("biblio", $ID_notice);
        
        // 2) on formate le titre
        $tmp=applique_plugin($plugin_get_titre, array("notice"=>$notice_biblio));
        $titre=$tmp["resultat"]["texte"];
        $notice["title"]=$titre;
        $notice["bibId"]=$ID_notice;
        $notice["items"]=array();
        
        // 3) on récupère les exemplaires
        $lignes_exemplaires=get_objets_xml_lies("exemplaire", "explicite", "", $ID_notice, "biblio", 1);
        
        foreach ($lignes_exemplaires as $ligne_exemplaire) { // pour chaque exemplaire
            $ID_exemplaire=$ligne_exemplaire["ID"];
            $available=0;
            $holdable=0;
            $visible=0;
            $infos_exemplaire=get_objet_by_id("exemplaire", $ID_exemplaire);
            $cab=$infos_exemplaire["a_cab"];
            //$date_retour_prevu=$infos_exemplaire["a_date_retour_prevu"];
            $bib_code=$infos_exemplaire["a_bibliotheque"];
            $tmp=applique_plugin($plugin_get_nom_bib, array("texte"=>$bib_code));
            $bib_nom=$tmp["resultat"]["texte"];
            if ($infos_exemplaire["a_etat"] == "dispo" AND $infos_exemplaire["a_pretable"] == "oui" AND $infos_exemplaire["a_actif"] == "oui") {
                $available=1;
            }
            if ($infos_exemplaire["a_reservable"] == "oui" AND $infos_exemplaire["a_actif"] == "oui") {
                $holdable=1;
                if ($bool_reserver_disponibles == 0 AND $infos_exemplaire["a_etat"] == "dispo") {
                    $holdable=0;
                }
            }
            if ($infos_exemplaire["a_accessible"] == "oui" AND $infos_exemplaire["a_actif"] == "oui") {
                $visible=1;
            }
            
            // infos prêt en cours
            $tmp=applique_plugin($plugin_get_pret, array("ID_exemplaire"=>$ID_exemplaire));
            $date_retour_prevu=$tmp["resultat"]["notices"][0]["a_date_retour_prevu"];
            
            $exemplaire=array("barcode"=>$cab, "itemId"=>$ID_exemplaire, "available"=>$available, "dueDate"=>$date_retour_prevu, "holdable"=>$holdable, "visible"=>$visible, "locationLabel"=>$bib_nom, "locationId"=>$bib_code);
            array_push($notice["items"], $exemplaire);
        } // fin du pour chaque exemplaire
        
        array_push($notices, $notice);

        
    } // fin du pour chaque notice
  
    // on génère le xml
    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml.="<GetRecords>";
    foreach ($notices as $notice) {
        $xml.="<record>\n";
        $xml.="<bibId>".$notice["bibId"]."</bibId>\n";
        $xml.="<title>".$notice["title"]."</title>\n";
        $xml.="<items>\n";
        foreach ($notice["items"] as $item) {
            $xml.="<item>\n";
            $xml.="<barcode>".$item["barcode"]."</barcode>\n";
            $xml.="<itemId>".$item["itemId"]."</itemId>\n";
            $xml.="<dueDate>".$item["dueDate"]."</dueDate>\n";
            //$xml.="<dueDate>2013-11-01</dueDate>\n";
            $xml.="<available>".$item["available"]."</available>\n";
            $xml.="<holdable>".$item["holdable"]."</holdable>\n";
            $xml.="<visible>".$item["visible"]."</visible>\n";
            $xml.="<locationLabel>".$item["locationLabel"]."</locationLabel>\n";
            $xml.="<locationId>".$item["locationId"]."</locationId>\n";
            $xml.="</item>\n";
        }
        $xml.="</items>\n";
        $xml.="</record>\n";
    }
    $xml.="</GetRecords>";
    
    
    $retour["resultat"]["xml"]=$xml;
    return ($retour);
}


?>