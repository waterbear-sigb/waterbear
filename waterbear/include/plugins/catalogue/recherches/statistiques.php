<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/recherche_simple.php"); // on utilise certaines méthodes de la recherche

function plugin_catalogue_recherches_statistiques ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    GLOBAL $json;
    
    $plugin_recherche=$parametres["plugin_recherche"];
    $param=$parametres["param"];
    $limite_nb_val=$parametres["limite_nb_val"];
    
    $recherchator=$param["recherchator"];
    $statator=$param["statator"];
    
    unset ($recherchator["page"]);
    unset ($recherchator["tris"]);
    unset ($recherchator["nb_notices_par_page"]);
    
    // on supprime les critères vides des stats
    $criteres_stat=array();
    $tmp=$statator["criteres"];
    foreach ($tmp as $elem) {
        if ($elem["valeur_critere"] != "") {
            array_push ($criteres_stat, $elem);
        }
    }
    
    // on tarnsforme les critères en array
    // si a|b|c => a, b, c
    // si # => recherche pour obtenir la liste des valeurs (TODO)
    $criteres_array=array();
    foreach ($criteres_stat as $idx_critere_stat => $critere_stat) {
        
        
        
        if ($critere_stat["valeur_critere"] == "#") {
            $criteres_array[$idx_critere_stat]=plugin_catalogue_recherches_statistiques_get_valeurs_possibles (array("critere"=>$critere_stat, "type_obj"=>$recherchator["type_objet"], "limite"=>$limite_nb_val));
        } elseif (strpos($critere_stat["valeur_critere"], "/#") !== false OR strpos($critere_stat["valeur_critere"], "|#") !== false) { // regroupement de paniers
            $critere_stat["valeur_critere"]=str_replace("|", "/", $critere_stat["valeur_critere"]);
            $critere_stat["valeur_critere"]=str_replace("/#", "", $critere_stat["valeur_critere"]);
            $type_obj_lien=$critere_stat["type_obj_lien"];
            if ($type_obj_lien == "") {
                $type_obj_lien=$recherchator["type_objet"]; // si $type_obj lien n'est pas fourni c'est que c'est un panier normal (pas de lien) donc on récupère le type doc global de la recherche
            }
            $tvs_panier=new tvs_paniers();
            $liste=$tvs_panier->get_contenu_repertoire($critere_stat["valeur_critere"], $type_obj_lien);
            $criteres_array[$idx_critere_stat]=array();
            foreach ($liste as $elem) {
                array_push($criteres_array[$idx_critere_stat], $critere_stat["valeur_critere"]."/".$elem["nom"]);
            }
            dbg_log("==== DBG LISTE ====");
            dbg_log($criteres_array[$idx_critere_stat]);
        } else {
            $criteres_array[$idx_critere_stat]=explode("|", $critere_stat["valeur_critere"]);
        }
    }
    
   
    // on récupère les critères de stats explosés
    $criteres_stat_exploses=plugin_catalogue_recherches_statistiques_get_exploses ($criteres_array);
    
    // tableau qui va contenir les chaines pour rebondir vers une recherche experte
    $liens_array=array();
    
    // on effectue la recherche pour chacun de ces jeux de critères
    // les résulats sont stockés à plat sous forme d'une liste. Il faudra ensuite structurer cette liste en colonnes et rangés ou en arbre
    $resultat_plat=array();
    foreach ($criteres_stat_exploses as $critere_stat_explose) { // pour chaque combinaison de valeurs
        $tmp_recherchator=$recherchator;
        foreach ($critere_stat_explose as $idx => $valeur_critere) { // pour chaque valeur
            $criteres_stat[$idx]["valeur_critere"]=$valeur_critere;
            array_push ($tmp_recherchator["criteres"], $criteres_stat[$idx]);
        }
        $tmp=applique_plugin($plugin_recherche, array("param_recherche"=>$tmp_recherchator));
        if ($tmp["succes"] != 1) {
            $tmp["resultat"]["nb_notices"]="0";
        }
        //$retour["resultat"].=" - ".$tmp["resultat"]["nb_notices"];
        array_push($resultat_plat, $tmp["resultat"]["nb_notices"]);
        $chaine_lien=$json->encode($tmp_recherchator["criteres"]);
        $chaine_lien=str_replace('"', "**", $chaine_lien); // pour des raisons de caractères spéciaux on remplace les " par ** puis on fait l'inverse
        array_push($liens_array, $chaine_lien); // le lien de recherche experte sous la forme d'une chaine json
    }
    
    // on structure le résultat plat
    $resultat_structure = plugin_catalogue_recherches_statistiques_structure_tableau ($resultat_plat, $criteres_array, $liens_array);
    $retour["resultat"]["resultat_structure"]=$resultat_structure;
    
    return ($retour);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// cette fonction va transformer un tableau du type
// [0] => a1, a2, a3
// [1] => b1, b2, b3
// [2] => c1, c2, c3
// en un tableau combiné (explosé) de la forme
// [0] => a1, b1, c1
// [1] => a1, b1, c2
// [...]
// [8] => a3, b3, c3
// Il utilise la récursivité : si un seul critère, on retourne ce critère sans transformation. Sinon, on garde le dernier critère
// on utilise la fonction récursivement sur les autres (pour obtenir un tableau explosé) et on combine ce tableau explosé au ernier critère

function plugin_catalogue_recherches_statistiques_get_exploses ($criteres_stat) {
   
    $retour=array();
    $nb_criteres = count($criteres_stat);
    if ($nb_criteres <= 1) {
        foreach ($criteres_stat[0] as $critere) {
            array_push ($retour, array(0=>$critere));
        }
        return ($retour);
        
        //return ($criteres_stat);
    }
    
    $dernier_critere=array_pop($criteres_stat);
    
    $criteres_stat_exploses=plugin_catalogue_recherches_statistiques_get_exploses($criteres_stat);
    foreach ($criteres_stat_exploses as $critere_stat_explose) {
        foreach ($dernier_critere as $elem_critere) {
            $tmp=array();
            foreach ($critere_stat_explose as $toto) {
                array_push ($tmp, $toto);
            }
            array_push ($tmp, $elem_critere);
            array_push ($retour, $tmp);
            //array_push($critere_stat_explose, $elem_critere);
            //array_push ($retour, $critere_stat_explose);
        }
    }
    return ($retour);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction va transformer un résultat plat en tableau à 1 ou 2 dimensions
// le tableau aura la forme :
// [intitules_col][0,1,2...] => les intitulés des colonnes
// [totaux_col][0,1,2...] => les totaux de chaque colonne
// [rows][0,1,2...] => les rangées avec pour chaque rangée :
//                 [intitule_row] => intitulé de la rangée
//                 [total_row] => total pour la rangée
//                 [cells][0,1,2,3...] => valeurs des différentes cellules 
//
function plugin_catalogue_recherches_statistiques_structure_tableau ($resultat_plat, $criteres_array, $liens_array) {
    $retour=array();
   
 
    // 1) Si un seul critère
    if (count($criteres_array) == 1) {
        $retour["intitules_col"]=$criteres_array[0];
        $retour["rows"][0]["cells"]=$resultat_plat;
        $retour["rows"][0]["liens"]=$liens_array;
        $retour["rows"][0]["intitule_row"]="*";
        $retour["rows"][0]["total_row"]=array_sum($retour["rows"][0]["cells"]);
        $retour["totaux_col"]=$resultat_plat;
        $retour["total"]=$retour["rows"][0]["total_row"];
        return ($retour);
    } 
    
    // 2) on génère les cellules et les intitulés
    $retour["intitules_col"]=$criteres_array[1];
    $retour["totaux_col"]=array();
    $idx_glob=0;
    foreach ($criteres_array[0] as $idx_row => $critere_row) {
        $retour["rows"][$idx_row]=array();
        $retour["rows"][$idx_row]["intitule_row"]=$criteres_array[0][$idx_row];
        
        $retour["rows"][$idx_row]["cells"]=array();
        foreach ($criteres_array[1] as $idx_cell => $critere_cell) {
            $retour["rows"][$idx_row]["cells"][$idx_cell]=$resultat_plat[$idx_glob];
            $retour["rows"][$idx_row]["liens"][$idx_cell]=$liens_array[$idx_glob];
            if (isset($retour["totaux_col"][$idx_cell])) {
                $retour["totaux_col"][$idx_cell]+=$resultat_plat[$idx_glob];
            } else {
                $retour["totaux_col"][$idx_cell]=$resultat_plat[$idx_glob];
            }
            $idx_glob++;
        }
        $retour["rows"][$idx_row]["total_row"]=array_sum($retour["rows"][$idx_row]["cells"]);
    }
    
    $retour["total"]=array_sum($retour["totaux_col"]);
    
    return($retour);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction va gérer les critères # c'est à dire lister les valeurs possibles pour un critère

function plugin_catalogue_recherches_statistiques_get_valeurs_possibles ($parametres) {
    $retour=array();
    $critere=$parametres["critere"];
    $type_obj=$parametres["type_obj"];
    $limite=$parametres["limite"];
    $intitule_critere=$critere["intitule_critere"];
    $autoplugin=$critere["autoplugin"];
    $type_recherche=$critere["type_recherche"];
    $sens_lien=$critere["sens_lien"];
    $type_obj_lien=$critere["type_obj_lien"];
    $type_lien=$critere["type_lien"];
    $nom_panier_comptage=$critere["nom_panier_comptage"];
    $tmp=applique_plugin($autoplugin, array());
    
    // Si critere soumis à liste
    // on désactive la gestion spécifique des listes, car ça ne permet pas d'afficher les 'anomalies' de catalogage (hors liste)
    /**
    if ($tmp["resultat"]["liste_choix"] != "") {
        foreach ($tmp["resultat"]["liste_choix"] as $choix) {
            array_push ($retour, $choix["valeur"]);
        }
        return ($retour);
    }
    **/
    
    // Si comptage
    if ($type_recherche == "comptage") {
        array_push($retour, 0);
        $recherche_simple=new recherche_simple ();
        $recherche_simple->init(array("type_objet"=>$type_obj));
        $sql_panier_comptage="";
        if ($nom_panier_comptage != "") {
            $sql_panier_comptage=$recherche_simple->panier_lien_2_critere($nom_panier_comptage, "", $type_obj_lien, $type_lien, $sens_lien, 0);
        }
        
        $sql=$recherche_simple->comptage_2_critere("XXX", "str_egal", $sql_panier_comptage, $type_obj_lien, $type_lien, $sens_lien, 1);
        
        
        /**
        if ($sens_lien) {
            $table_jointure="obj_".$type_obj."_liens";
            if ($type_lien != "") {
                $sql_type_lien=" AND $table_jointure.type_lien = '$type_lien' ";
            }
            $sql="SELECT distinct COUNT(*) AS A FROM $table_jointure WHERE $table_jointure.type_objet = '$type_obj_lien' $sql_type_lien $sql_panier_comptage GROUP BY $table_jointure.ID";
            $tmp=sql_as_array(array("sql"=>$sql, "contexte"=>"plugins/catalogue/recherches/statistiques::get_valeurs_possibles"));
            foreach ($tmp as $elem) {
                array_push($retour, $elem["A"]);
            }
        }
        **/
        
        $tmp=sql_as_array(array("sql"=>$sql, "contexte"=>"plugins/catalogue/recherches/statistiques::get_valeurs_possibles"));
        foreach ($tmp as $elem) {
            array_push($retour, $elem["A"]);
        }
        sort($retour);
        return($retour);
    }
    
    
    // Si critere libre
    $intitule_critere=secure_sql($intitule_critere);
    
    $sql="select distinct $intitule_critere from obj_".$type_obj."_acces LIMIT $limite ";
    $tmp=sql_as_array(array("sql"=>$sql, "contexte"=>"plugins/catalogue/recherches/statistiques::get_valeurs_possibles"));
    foreach ($tmp as $elem) {
        $tmp2=explode("|", $elem[$intitule_critere]);
        foreach ($tmp2 as $elem2) {
            $elem2=trim($elem2);
            $elem2=trim($elem2);
            $elem2=trim($elem2);
            if (! in_array($elem2, $retour) AND $elem2 != "") {
                array_push($retour, $elem2);
            }
        }
        sort($retour);
        //array_push($retour, $elem[$intitule_critere]);
    }
    return ($retour);
    
    
}

?>