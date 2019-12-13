<?php



/**
 * plugin_catalogue_import_export_meta_format_marc2xml()
 * 
 * Ce plugin transforme une notice marc en marcxml
 * 
 * @param mixed $parametres
 * @param ["notice"] => notice marc
 * @param ["plugin_encodage"] => plugin utilisé pour décoder les caractères
 * @return array
 * @return $retour["resultat"]["notice"] => notice XML
 */
function plugin_catalogue_import_export_meta_format_marc2xml ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    $notice=$parametres["notice"];
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $node_record=$dom->createElement('record');
    //$dom->appendChild($node_record);
    
    

	$adresse_1e_car=substr($notice,12,5);
	$zone_adresse=substr($notice, 24, $adresse_1e_car-25);
	$zone_champs=substr($notice, $adresse_1e_car-1, strlen($notice)-$adresse_1e_car+1);
    //$node_leader=$dom->createElement('leader', substr($notice,0,24));
    $node_leader=$dom->createElement('label', substr($notice,0,24));
    $node_record->appendChild($node_leader);

	for ($i=0;$i<strlen($zone_adresse)/12;$i++) {//////////////////////////////   POUR CHAQUE CHAMP...
    	$nom_champ=substr($zone_adresse,$i*12,3);
    	$sous_champs=plugin_catalogue_import_export_meta_format_marc2xml__retourne_ss_champs ($i, $zone_adresse, $zone_champs);
    	$nbre_ss_champs=count($sous_champs);
    	
    	// Gestion des indicateurs
    	if ($nbre_ss_champs>1) { // S'il y a des indicateurs
    		$indice1=substr($sous_champs[0],1,1);
    		$indice2=substr($sous_champs[0],2,1);
    	}
    	
    	// Gestion des champs sans sous champs
    	if ($nbre_ss_champs==1) { // Pas de sous-champs (directement valeur du champ)
            if (isset($parametres["plugin_encodage"])) { // Encodage
                $tmp=applique_plugin($parametres["plugin_encodage"],array("chaine"=>substr($sous_champs[0], 1, strlen($sous_champs[0])-1)));
                if ($tmp["succes"] != 1) {
                    $tmp=substr($sous_champs[0], 1, strlen($sous_champs[0])-1);
                } else {
                    $tmp=$tmp["resultat"]["chaine"];
                }
            } else {
                $tmp=substr($sous_champs[0], 1, strlen($sous_champs[0])-1);
            }
    		//$node_controlfield=$dom->createElement('controlfield', $tmp);
            //$node_controlfield2=$node_record->appendChild($node_controlfield);
            //$node_controlfield2->setAttributeNode(new DOMAttr('tag', $nom_champ));
            $node_datafield=$dom->createElement('datafield');
            $node_subfield=$dom->createElement('subfield', $tmp);
            $node_subfield2=$node_datafield->appendChild($node_subfield);
            $node_subfield2->setAttributeNode(new DOMAttr('code', "a")); // on crée un ss-champ "a" par défaut
            $node_datafield2=$node_record->appendChild($node_datafield);
            $node_datafield2->setAttributeNode(new DOMAttr('tag', $nom_champ));
            
   		} else { // Si plusieurs sous-champs
            $node_datafield=$dom->createElement('datafield');
    		for ($j=1;$j<=$nbre_ss_champs-1;$j++) {    ////////////////   POUR CHAQUE SOUS CHAMP...
                if (isset($parametres["plugin_encodage"])) { // Encodage
                    $tmp=applique_plugin($parametres["plugin_encodage"],array("chaine"=>substr($sous_champs[$j], 1, strlen($sous_champs[$j])-1)));
                    if ($tmp["succes"] != 1) {
                        $tmp=substr($sous_champs[$j], 1, strlen($sous_champs[$j])-1);
                    } else {
                        $tmp=$tmp["resultat"]["chaine"];
                    }
                } else {
                    $tmp=substr($sous_champs[$j], 1, strlen($sous_champs[$j])-1);
                }
    			$node_subfield=$dom->createElement('subfield', $tmp);
                $node_subfield2=$node_datafield->appendChild($node_subfield);
                $node_subfield2->setAttributeNode(new DOMAttr('code', substr($sous_champs[$j], 0, 1)));
    		}
            
            if ($indice1 != "") {
                $node_id1=$dom->createElement('subfield', $indice1);
                $node_id1_2=$node_datafield->appendChild($node_id1);
                $node_id1_2->setAttributeNode(new DOMAttr('code', "id1"));
            }
            
            if ($indice2 != "") {
                $node_id2=$dom->createElement('subfield', $indice2);
                $node_id2_2=$node_datafield->appendChild($node_id2);
                $node_id2_2->setAttributeNode(new DOMAttr('code', "id2"));
            }
            
            $node_datafield2=$node_record->appendChild($node_datafield);
            $node_datafield2->setAttributeNode(new DOMAttr('tag', $nom_champ));
            
            //$node_datafield2->setAttributeNode(new DOMAttr('ind1', $indice1));
            //$node_datafield2->setAttributeNode(new DOMAttr('ind2', $indice2));
    	}
	}
    $dom->appendChild($node_record);
    $retour["resultat"]["notice"]=$dom;
	return($retour);
    
    
} // fin de la fonction


/**
 * plugin_catalogue_import_export_meta_format_marc2xml__retourne_ss_champs()
 * 
 * retourne les sous-champs contenus dans un champ marc (sous forme d'Array)
 * 
 * @param mixed $idx_champ
 * @param mixed $zone_adresse
 * @param mixed $zone_champs
 * @return array $sous_champs
 */
function plugin_catalogue_import_export_meta_format_marc2xml__retourne_ss_champs ($idx_champ, $zone_adresse, $zone_champs) {
    $separateur_ss_champs="";
    $pos=$idx_champ*12+3;
    $longueur_champ=substr($zone_adresse,$pos,4);
    $pos=$pos+4;
    $adresse_champ=substr($zone_adresse,$pos,5);
    $valeur_champ=substr($zone_champs, $adresse_champ, $longueur_champ);
    $sous_champs=explode ("$separateur_ss_champs", $valeur_champ);
    
    return ($sous_champs);
}

?>