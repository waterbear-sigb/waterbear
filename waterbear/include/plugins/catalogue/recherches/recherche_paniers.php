<?php

function plugin_catalogue_recherches_recherche_paniers ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $query=secure_sql($parametres["query"]);
    $type_obj=secure_sql($parametres["type_obj"]);
    $type_recherche=$parametres["type_recherche"];
    $type_panier=$parametres["type_panier"];
    if ($type_panier == "") {
        $type_panier="'statique', 'dynamique'";
    }
    
    
    
    if ($type_recherche=="str_commence") {
        $sql="select * from tvs_paniers where type_obj = '$type_obj' AND nom like '$query%' AND type IN ($type_panier) order by chemin_parent, nom";
    } elseif ($type_recherche=="str_contient") {
        $chaine=plugin_catalogue_recherches_recherche_paniers_formate_fulltext($query, array("ou"=>false, "commence"=>false, "last_commence"=>false));
        $sql="select * from tvs_paniers where type_obj = '$type_obj' AND MATCH (nom, chemin_parent) AGAINST ('$chaine' IN BOOLEAN MODE) AND type IN ($type_panier) order by chemin_parent, nom";
    } elseif ($type_recherche=="str_contient_commence") {
        $chaine=plugin_catalogue_recherches_recherche_paniers_formate_fulltext($query, array("ou"=>false, "commence"=>true, "last_commence"=>false));
        $sql="select * from tvs_paniers where type_obj = '$type_obj' AND MATCH (nom, chemin_parent) AGAINST ('$chaine' IN BOOLEAN MODE) AND type IN ($type_panier) order by chemin_parent, nom";
    } else  { // DEFAUT !!!
        $chaine=plugin_catalogue_recherches_recherche_paniers_formate_fulltext($query, array("ou"=>false, "commence"=>false, "last_commence"=>true));
        $sql="select * from tvs_paniers where type_obj = '$type_obj' AND MATCH (nom, chemin_parent) AGAINST ('$chaine' IN BOOLEAN MODE) AND type IN ($type_panier) order by chemin_parent, nom";
    }
    $liste=sql_as_array(array("sql"=>$sql, "contexte"=>"plugin_catalogue_recherches_recherche_paniers()"));
    foreach ($liste as $elem) {
        $chemin_parent=$elem["chemin_parent"];
        $nom=$elem["nom"];
        if ($chemin_parent=="") {
            $chemin=$nom;
        } else {
            $chemin=$chemin_parent."/".$nom;
        }
        $tab=array("nom"=>$chemin, "id"=>$chemin);
        array_push($retour["resultat"], $tab);
    }
    
    return ($retour);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ajoute des '*' à la fin des mots pour une recherche de type "contient mots commençant par..."
function plugin_catalogue_recherches_recherche_paniers_formate_fulltext ($chaine, $parametres) {
    $ou=$parametres["ou"];
    $commence=$parametres["commence"];
    $last_commence=$parametres["last_commence"];
    $phrase="";
    $liste=explode(" ", $chaine);
    $nb_mots=count($liste);
    foreach ($liste as $idx=>$mot) {
        if ($mot == "") {
            continue;
        }
        if ($commence === true) {
            $mot.="*";
        }
        if ($ou === false) {
            $mot="+".$mot;
        }
        if ($last_commence === true AND $idx == $nb_mots - 1) {
            $mot.="*";
        }
        $phrase.=$mot." ";
    }
    return ($phrase);
}

?>