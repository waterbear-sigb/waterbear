<?php

function plugin_catalogue_recherches_autocomplete_rue_ign ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["notices"]=array();
    
    $plugin_recherche_rue=$parametres["plugin_recherche_rue"];
    $plugin_recherche_ign=$parametres["plugin_recherche_ign"];
    
    $chaine=$parametres["query"];
    
    // a) recherche IGN
    $tmp=applique_plugin($plugin_recherche_ign, array("chaine"=>$chaine));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    foreach ($tmp["resultat"]["propositions"] as $proposition) {
        $proposition["street"]=plugin_catalogue_recherches_autocomplete_rue_ign_traite_abreviations($proposition["street"]); // on remplace les abréviations (r => rue ...)
        $nom=$proposition["street"]." : ".$proposition["zipcode"]." ".$proposition["city"];
        //$id="ign:".$proposition["street"]." : ".$proposition["zipcode"]." ".$proposition["city"];
        if (plugin_catalogue_recherches_autocomplete_rue_ign_teste_in_array($retour["resultat"]["notices"], $nom) == 0) {
            array_push($retour["resultat"]["notices"], array("nom"=>$nom, "id"=>$nom));
        }
    }
    
    // b) recherche dans la DB
    $tmp=applique_plugin($plugin_recherche_rue, array("query"=>$chaine));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    foreach ($tmp["resultat"]["notices"] as $resultat) {
        if (plugin_catalogue_recherches_autocomplete_rue_ign_teste_in_array($retour["resultat"]["notices"], $resultat["nom"]) == 0) {
            $resultat["id"]="id:".$resultat["id"];
            array_unshift($retour["resultat"]["notices"], $resultat);
        }
    }
    
    return ($retour);
}

function plugin_catalogue_recherches_autocomplete_rue_ign_teste_in_array($liste, $nom) {
    foreach ($liste as $elem) {
        $nom2=$elem["nom"];
        if ($nom2==$nom) {
            return(1);
        }
    }
    return(0);
}

function plugin_catalogue_recherches_autocomplete_rue_ign_traite_abreviations($nom) {
    $liste=array();
    $liste["r"]="rue";
    $liste["av"]="avenue";
    $liste["carref"]="carrefour";
    $liste["che"]="chemin";
    $liste["chem"]="chemin";
    $liste["dom"]="domaine";
    $liste["fbg"]="faubourg";
    $liste["ham"]="hameau";
    $liste["imp"]="impasse";
    $liste["bd"]="boulevard";
    $liste["imp"]="impasse";
    $liste["all"]="allée";
    $liste["rte"]="route";
    $liste["pl"]="place";
    $liste["pass"]="passage";
    $liste["rle"]="ruelle";
    $liste["ven"]="venelle";
    //$liste["r"]="rue";
    //$liste["r"]="rue";
    
    foreach ($liste as $abreviation => $remplace) {
        $remplace=utf8_encode($remplace);
        $abreviation.=" ";
        if (stripos($nom, $abreviation) === 0) { // si on trouve l'abréviation en 1ere position
            $longueur=strlen($nom)-strlen($abreviation);
            $chaine=substr($nom,strlen($abreviation));
            $nom=$remplace." ".$chaine;
            break;   
        }
    }
    return($nom);
}



?>