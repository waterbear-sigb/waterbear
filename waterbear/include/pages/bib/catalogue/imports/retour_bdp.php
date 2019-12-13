<?php
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"];
$plugin_traitement=$GLOBALS["affiche_page"]["parametres"]["plugin_traitement"];
$plugin_notice_2_db =$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db"];

// Si pas de fichier, on affiche le formulaire
if (! isset($_FILES["fichier"])) {
    affiche_template($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());
    include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");
    die("");
}

// Si fichier
// On récupère le fichier
$param=upload_file("fichier");
if ($param["erreur"] != "") {
  	affiche_template("erreurs/erreur_div.php", array("message"=>get_intitule("erreurs/messages_erreur", "impossible_uploader_fichier", array("message"=>$param["erreur"]))));
  	die("");
}

$chemin=$param["chemin"];
$taille=$param["taille"];

// on récupère le fichier comme une chaine
$elements=file($chemin);

// Recherche, traitements, enregistrement
$log="";
foreach ($elements as $idx_element => $element) { // pour chaque élément...
    $element=trim($element);
    $log.="$idx_element : $element - ";
    // 1) recherche
    $tmp=applique_plugin($plugin_recherche, array("cab"=>$element));
    if ($tmp["succes"] != 1) {
        $log.="ERREUR lors de la recherche : ".$tmp["erreur"]."<br>\n";
        continue;
    }
    
    // 2) est-ce que l'élément existe
    if (!isset($tmp["resultat"]["notices"][0]["xml"])) {
        $log.="Objet inexistant<br>\n";
        continue;
    } else {
        $notice=$tmp["resultat"]["notices"][0]["xml"];
        $ID_notice=$tmp["resultat"]["notices"][0]["ID"];
        //$log.="ID = $ID_notice ";
    }
    
    // 3) traitement
    $tmp=applique_plugin($plugin_traitement, array("notice"=>$notice, "cab"=>$element));
    if ($tmp["succes"] != 1) {
        $log.="ERREUR lors du traitement : ".$tmp["erreur"]."<br>\n";
        continue;
    }
    $notice=$tmp["resultat"]["notice"];
    
    // 4) Enregistrement
    $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID_notice));
    if ($tmp["succes"] != 1) {
        $log.="ERREUR lors de l'enregistrement : ".$tmp["erreur"]."<br>\n";
        continue;
    }
    
    // 5) valide
    $log.="OK <br>\n";
    
} // fin du pour chaque élément

$GLOBALS["affiche_page"]["template"]["tmpl_main"]=$GLOBALS["affiche_page"]["template"]["tmpl_main_validation"];
affiche_template($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("log"=>$log)));
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");




?>