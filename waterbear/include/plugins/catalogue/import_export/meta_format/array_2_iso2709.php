<?php

/**
 * plugin_catalogue_import_export_meta_format_array_2_iso2709()
 * 
 * Génère une chaine de caractère iso2709 à partir d'une correctement formatée
 * Il nettoie également les champs / ss-champs vides.
 * On peut utiliser le paramètre $ss_champs_non_significatifs pour indiquer les ss-champs qui ne doivent pas être pris en compte même si non vides (par ex. 700$4)
 * Sous la forme [ss_champs_non_significatifs][700_4|701_4|...]=>1
 * 
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_import_export_meta_format_array_2_iso2709 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    
    $notice=$parametres["notice"]; // définition de la notice sous forme d'array
    $ss_champs_non_significatifs=$parametres["ss_champs_non_significatifs"];

    $separateur_champs="";
	$separateur_ss_champs="";
	$notice_export=array();
	$pointeur=0;
	$idx_champs=0; // nb de champs. Si toujours 0 à la fin, notice vide, on ne renvoie rien
	
	// Pour chaque champ...
	foreach ($notice["champs"] as $champ) {
        
	  	$idx_champs++;
		$string_champ="";
		
		// gestion des indicateurs
		if ($champ["id1"]=="") {
			$champ["id1"]=" ";
		}
		if ($champ["id2"]=="") {
			$champ["id2"]=" ";
		}
		
		// SI sous-champs...
		if (count($champ["ss_champs"]) > 0) {
		   // Début de chaque champ ISO (séparateur champs + idc1 + idc2
		   $string_champ=$separateur_champs.$champ["id1"].$champ["id2"];
		   
		   // Pour chaque sous-champ...	
           $bool_exporte=0;		
		   foreach($champ["ss_champs"] as $sous_champ) {
                if (strlen($sous_champ["nom"]) != 1) { // gestion des ss-champs non conformes (ex. $9a...)
                    continue;
                }
                if ($sous_champ["valeur"] != "" AND $sous_champ["valeur"] != "_void_") {
                    $string_champ.=$separateur_ss_champs.$sous_champ["nom"].$sous_champ["valeur"];
                    if ($ss_champs_non_significatifs[$champ["nom"]."_".$sous_champ["nom"]] != 1) {
                        $bool_exporte=1;
                    }
                }
		   }
		   
  		// SI Champ sans sous-champs...
		} else {
			//$string_champ=$separateur_champs.$champ["texte_champ"];
            $bool_exporte=1; // ??? todo à modifier ?
			$string_champ=$separateur_champs.$champ["valeur"];
		}
		
		// Pour chaque champ, on va calculer les pointeurs (nom du champ, nbre de caractères du champ et adresse de début du champ)
        if ($bool_exporte == 1 AND strlen($champ["nom"])==3 AND is_numeric($champ["nom"])) {
    		$champ_export=array();
    		$champ_export["string"]=$string_champ;
    		$champ_export["longueur"]=str_pad (strlen($string_champ), 4 ,"0", STR_PAD_LEFT);
    		$champ_export["champ"]=str_pad ($champ["nom"], 3 ,"0", STR_PAD_LEFT);
    		$champ_export["pointeur"]=str_pad ($pointeur, 5 ,"0", STR_PAD_LEFT);
    		array_push($notice_export, $champ_export);
    		$pointeur=$pointeur+strlen($string_champ);
        }
	} // fin du 'pour chaque champ...'
	
	// On teste si notice vide
	if ($idx_champs==0) {
	  	//return(array("notice"=>"")); // on ne renvoie rien
        $retour["resultat"]["notice"]="";
	}
	
	$string_champs="";
	$string_pointeurs="";
	
	// On génère les pointeurs et les champs
	foreach ($notice_export as $champ) {
		$string_champs.=$champ["string"];
		$string_pointeurs.=$champ["champ"].$champ["longueur"].$champ["pointeur"];
	}
	
	// On calcule l'adresse du 1er caractère du 1er champ (juste après le label et les pointeurs)
	$adresse_1e_car=strlen($string_pointeurs)+25; // longueur des pointeurs + longueur du label + 1 (on commence au caractère suivant). ATTENTION en MARC le 1er caractère est le car 1 (pas 0)
	
	// On concatène les pointeurs, les champs + caractères de fin de notice
	$string_pointeurs.=$string_champs;
	$string_pointeurs.="";
	
	// On calcule la longueur de la notice
	$longueur_notice=strlen($string_pointeurs)+24;
	
	// On modifie le label en fonction de la longueur de la notice et de l'adresse du 1er caractère
	$label2="";
	$adresse_1e_car=str_pad ($adresse_1e_car, 5 ,"0", STR_PAD_LEFT);
	$longueur_notice=str_pad ($longueur_notice, 5 ,"0", STR_PAD_LEFT);
	$label2=$longueur_notice.substr($notice["label"],5,7).$adresse_1e_car.substr($notice["label"],17,7);
	
	// On concatène le label au reste
	$retour["resultat"]["notice"]=$label2.$string_pointeurs;


    return($retour);
}

?>