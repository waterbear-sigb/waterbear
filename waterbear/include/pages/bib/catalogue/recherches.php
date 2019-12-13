<?PHP
// Si on n'a pas encore défini d'opération, on le fait
if ($ID_operation=="") {
    $ID_operation=get_id_operation();
    $_SESSION["operations"][$ID_operation]=array();
}
$GLOBALS["affiche_page"]["parametres"]["ID_operation"]=$ID_operation;

$erreurs="";

// liste des critères
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_criteres"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$liste_criteres_ajout=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Formulare_defaut
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_formulaire_defaut"], array("GET"=>$_GET));
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$formulaire_defaut=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// liste des tris
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_tris"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$liste_tris=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// liste des formats de liste
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_formats_liste"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$liste_formats_liste=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// liste des formats de notice
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_formats_notice"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$liste_formats_notice=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Icones de l'onglet de recherche
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_icones_recherche"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$icones_recherche=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Icones de l'onglet liste
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_icones_liste"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$icones_liste=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Icones de l'onglet notice
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_icones_notice"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$icones_notice=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Icones de l'onglet paniers
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_icones_paniers"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$icones_paniers=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Association type (biblio, auteur...) => grille de catalogage par défaut (pour les rebonds)
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_type_2_grille"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$type_2_grille=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// Plugin de formatage utilisé pour récupérer l'id d'une notice à partir de sa position dans la liste
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_id"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}
$plugin_get_id=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

// STATS
if ($GLOBALS["affiche_page"]["parametres"]["bool_stat"] == 1) {
    // liste des critères stats
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_criteres_stats"], array());
    if ($tmp["succes"] != 1) {
        $erreurs.=$tmp["erreur"]."\\n";
    }
    $liste_criteres_ajout_stats=str_replace ('"', '\"', $json->encode($tmp["resultat"]));
    
    // Formulare_defaut stats
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_formulaire_defaut_stats"], array("GET"=>$_GET));
    if ($tmp["succes"] != 1) {
        $erreurs.=$tmp["erreur"]."\\n";
    }
    $formulaire_defaut_stats=str_replace ('"', '\"', $json->encode($tmp["resultat"]));
    
    // liste des formats de liste stats
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_formats_liste_stats"], array());
    if ($tmp["succes"] != 1) {
        $erreurs.=$tmp["erreur"]."\\n";
    }
    $liste_formats_liste_stats=str_replace ('"', '\"', $json->encode($tmp["resultat"]));
    
    // Icones de l'onglet stats
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_icones_stats"], array());
    if ($tmp["succes"] != 1) {
        $erreurs.=$tmp["erreur"]."\\n";
    }
    $icones_stats=str_replace ('"', '\"', $json->encode($tmp["resultat"]));
    
    // Icones de l'onglet resultats stats
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_icones_resultats_stats"], array());
    if ($tmp["succes"] != 1) {
        $erreurs.=$tmp["erreur"]."\\n";
    }
    $icones_resultats_stats=str_replace ('"', '\"', $json->encode($tmp["resultat"]));

}




$erreurs=str_replace ('"', '\"', $erreurs);
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_js"=>array("erreurs"=>$erreurs, "liste_criteres_ajout"=>$liste_criteres_ajout, "liste_criteres_ajout_stats"=>$liste_criteres_ajout_stats, "formulaire_defaut"=>$formulaire_defaut, "formulaire_defaut_stats"=>$formulaire_defaut_stats, "liste_tris"=>$liste_tris, "liste_formats_liste"=>$liste_formats_liste, "liste_formats_notice"=>$liste_formats_notice, "type_2_grille"=>$type_2_grille, "plugin_get_id"=>$plugin_get_id, "icones_recherche"=>$icones_recherche, "icones_liste"=>$icones_liste, "icones_notice"=>$icones_notice, "icones_paniers"=>$icones_paniers, "icones_stats"=>$icones_stats, "icones_resultats_stats"=>$icones_resultats_stats, "liste_formats_liste_stats"=>$liste_formats_liste_stats)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>

 