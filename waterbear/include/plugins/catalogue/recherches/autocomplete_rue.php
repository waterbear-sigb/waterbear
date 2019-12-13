<?PHP

function plugin_catalogue_recherches_autocomplete_rue ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["notices"]=array();
    
    $plugin_recherche_rue=$parametres["plugin_recherche_rue"];
    $plugin_recherche_google=$parametres["plugin_recherche_google"];
    $plugin_recherche_ville=$parametres["plugin_recherche_ville"];
    
    $chaine=$parametres["query"];
    
    // 1) on regarde si on est en mode rue ou ville (présence d'un : )
    $analyse=explode(":", $chaine);
    
    if (count($analyse)>1) {  // ville
        $part1=$analyse[0];
        $part2=$analyse[1];
        $tmp=applique_plugin($plugin_recherche_ville, array("query"=>$part2));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        foreach ($tmp["resultat"]["notices"] as $resultat) {
            $ville=$resultat["nom"];
            $element=$part1." : ".$ville;
            //$resultat["id"]="id:".$resultat["id"];
            array_push($retour["resultat"]["notices"], array("id"=>$element, "nom"=>$element));
        }
    } else {
        // a) recherche dans la DB
        $tmp=applique_plugin($plugin_recherche_rue, array("query"=>$chaine));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        foreach ($tmp["resultat"]["notices"] as $resultat) {
            $resultat["id"]="id:".$resultat["id"];
            array_push($retour["resultat"]["notices"], $resultat);
        }
        
        // b) recherche google
        if ($plugin_recherche_google != "") {
            $tmp=applique_plugin($plugin_recherche_google, array("chaine"=>$chaine));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
            if (count($tmp["resultat"]) > 0 AND count($retour["resultat"]["notices"]) > 0) {
                array_push($retour["resultat"]["notices"], array("nom"=>"--------------------", "id"=>""));
            }
            foreach ($tmp["resultat"] as $resultat) {
                array_push($retour["resultat"]["notices"], $resultat);
            }
        }
    }
    
    return ($retour);
    
    
}


?>