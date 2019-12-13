<?php

function plugin_div_cab_2_id ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $type_obj=$parametres["type_obj"];
    $champ_recherche=$parametres["champ_recherche"];
    $champ_recupere=$parametres["champ_recupere"];
    $plugin_recherche=$parametres["plugin_recherche"];
    $query=$parametres["query"];
    
    if ($champ_recupere == "") {
        $champ_recupere="ID";
    }
    
    $tmp=applique_plugin ($plugin_recherche, array("query"=>$query, "champ_recherche"=>$champ_recherche, "type_obj"=>$type_obj));
    if ($tmp["succes"] != 1) {
        return($tmp);
    }
    
    $notices=$tmp["resultat"]["notices"];
    if (count($notices) == 0) {
        $tmp["succes"]=0;
        $tmp["erreur"]="@& Aucune notice de type $type_obj ne correspond a $query ($champ_recherche)";
        return ($tmp);
    } elseif (count($notices) > 1) {
        $tmp["succes"]=0;
        $tmp["erreur"]="@& Plus d'une notice de type $type_obj correspond a $query ($champ_recherche)";
        return ($tmp);
    }


    
    $retour["resultat"]["ID"]=$notices[0][$champ_recupere];    
    return ($retour);
}



?>