<?php


function plugin_catalogue_commandes_maj_prix ($parametres) {
    $notice_xml=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $ID_notice=$parametres["ID_notice"];
    $type_doc=$parametres["type_doc"];
    $plugin_modif=$parametres["plugin_modif"];
    
    
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $tx_tva="";
    $tx_remise="";
    $prix_public="";
    $prix_remise_deduite=0;
    $tva=0;
    $prix_ht=0;
    $remise=0;
    $prix_remise_deduite_total=0;
    $tva_totale=0;
    $prix_ht_total=0;
    $quantite=1;
    
    if ($tvs_marcxml == "") {
        if ($notice_xml=="") {
            if ($ID_notice == "" OR $type_doc == "") {
                $retour["succes"]=0;
                $retour["erreur"]="@& maj_prix : Vous n'avez fourni aucune notice";
                return($retour);
            }
            $notice_xml=get_objet_xml_by_id($type_doc, $ID_notice);
        }
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice_xml);
    }
    
    // 1) On récupère le tx de TVA (500$d) et le tx de remise (500$e) et le prix public (500$c)
    $tmp=$tvs_marcxml->get_champs("500", "");
    $champ_500=$tmp[0];
    if ($champ_500 == "") {
        $retour["resultat"]["notice"]=$notice_xml;
        return ($retour); // on ne retourne pas d'erreur, car le plugin peut être appelé sur 
    }
    
    $tmp=$tvs_marcxml->get_ss_champs($champ_500, "c", "", "");
    $ss_champ_prix_public=$tmp[0];
    if ($ss_champ_prix_public != "") {
        $prix_public=$tvs_marcxml->get_valeur_ss_champ($ss_champ_prix_public);
        if (! is_numeric($prix_public)) {
            $prix_public="";
        }
    }
    
    if ($prix_public=="") {
        $prix_public=0;
    }
    $tmp=$tvs_marcxml->get_ss_champs($champ_500, "b", "", "");
    $ss_champ_quantite=$tmp[0];
    if ($ss_champ_quantite != "") {
        $quantite=$tvs_marcxml->get_valeur_ss_champ($ss_champ_quantite);
        if (! is_numeric($quantite)) {
            $quantite=1;
        }
    }
    
    $tmp=$tvs_marcxml->get_ss_champs($champ_500, "d", "", "");
    $ss_champ_tx_tva=$tmp[0];
    if ($ss_champ_tx_tva != "") {
        $tx_tva=$tvs_marcxml->get_valeur_ss_champ($ss_champ_tx_tva);
        if (! is_numeric($tx_tva)) {
            $tx_tva="";
        }
    }
    
    $tmp=$tvs_marcxml->get_ss_champs($champ_500, "e", "", "");
    $ss_champ_tx_remise=$tmp[0];
    if ($ss_champ_tx_remise != "") {
        $tx_remise=$tvs_marcxml->get_valeur_ss_champ($ss_champ_tx_remise);
        if (! is_numeric($tx_remise)) {
            $tx_remise="";
        }
    }
    
    
    
    // 2) On calcule les autres valeurs
    if ($tx_remise != "") {
        $remise=($prix_public*$tx_remise)/100;
        $prix_remise_deduite=$prix_public-$remise;
    } else {
        $remise=0;
        $prix_remise_deduite=$prix_public;
    }
    
    if ($tx_tva != "") {
        $tva=($prix_public*$tx_tva)/100;
        $prix_ht=$prix_public-$tva;
    } else {
        $tva=0;
        $prix_ht=$prix_public;
    }
    
    $prix_remise_deduite_total=$prix_remise_deduite*$quantite;
    $tva_totale=$tva*$quantite;
    $prix_ht_total=$prix_ht*$quantite;
    
    // 3) On modifie la notice
    $tmp=applique_plugin($plugin_modif, array("tvs_marcxml"=>$tvs_marcxml, "prix_public"=>round($prix_public,2), "prix_remise_deduite"=>round($prix_remise_deduite,2), "tva"=>round($tva,2), "prix_ht"=>round($prix_ht,2), "prix_remise_deduite_total"=>round($prix_remise_deduite_total,2), "tva_totale"=>round($tva_totale,2), "prix_ht_total"=>round($prix_ht_total,2)));
    return ($tmp);
    
    
    
    
}


?>