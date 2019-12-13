<?php
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/fichiers.php");


// variables passées en paramètre
$interactif=$_REQUEST["interactif"]; // filtre utilisé
$bool_verif=$_REQUEST["bool_verif"];


$type_objet=$GLOBALS["affiche_page"]["parametres"]["type_objet"]; // Type d'objet (biblio...)
$filtre=$GLOBALS["affiche_page"]["parametres"]["filtre"]; // Type d'objet (biblio...)

// On récupère d'autres paramètres avancés pouvant éventuellement surcharger ceux définis dans le filtre
// !! TODO !! quand on fera la artie "AVANCE"

$meta_format=$GLOBALS["affiche_page"]["parametres"]["meta_format"];
$plugin_split=$GLOBALS["affiche_page"]["parametres"]["plugin_split"];
$plugin_get_notice=$GLOBALS["affiche_page"]["parametres"]["plugin_get_notice"];
$plugin_get_options=$GLOBALS["affiche_page"]["parametres"]["plugin_get_options"];

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupérer le nombre de notices
if ($operation == "split_fichier") {
    if (! $handle=fopen($_SESSION["operations"][$ID_operation]["fichier"],"r")) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "impossible_ouvrir_fichier", array("fichier"=>$_SESSION["operations"][$ID_operation]["fichier"]));
    } else {
        $retour=applique_plugin($plugin_split, array("handle"=>$handle, "meta_format"=>$meta_format, "taille_fichier"=>$_SESSION["operations"][$ID_operation]["taille_fichier"]));
        $_SESSION["operations"][$ID_operation]["nb_notices"]=$retour["resultat"];
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupérer la notice suivante
// la convertit en XML, récupère les infos de ddbl, dédoublonne et affiche
} elseif ($operation == "get_notice") {
    // On ouvre le fichier
    if (! $handle=fopen($_SESSION["operations"][$ID_operation]["fichier"],"r")) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "impossible_ouvrir_fichier", array("fichier"=>$_SESSION["operations"][$ID_operation]["fichier"]));
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    // On vérifie que $last_car est bien défini
    if (! isset ($_SESSION["operations"][$ID_operation]["last_car"])) {
        $_SESSION["operations"][$ID_operation]["last_car"]=0;
    }
    
    // On vérifie que $no_notice est bien défini
    if (! isset ($_SESSION["operations"][$ID_operation]["no_notice"])) {
        $_SESSION["operations"][$ID_operation]["no_notice"]=0;
    }
    // On applique le plugin en fonction du format
    if ($_SESSION["operations"][$ID_operation]["last_car"] >= $_SESSION["operations"][$ID_operation]["taille_fichier"]) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "fin_fichier_atteinte", array());
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    $tmp=applique_plugin($plugin_get_notice, array("handle"=>$handle, "meta_format"=>$meta_format, "taille_fichier"=>$_SESSION["operations"][$ID_operation]["taille_fichier"], "last_car"=>$_SESSION["operations"][$ID_operation]["last_car"], "panier"=>$_SESSION["operations"][$ID_operation]["panier"], "import_options"=>$_SESSION["operations"][$ID_operation]["import_options"], "bool_verif"=>$bool_verif));
    // Si erreur ou fin du fichier...
    if ($tmp["succes"]==0) {
        //$retour=$tmp;
        //$output = $json->encode($retour);
        //print($output);
        //die("");
        $retour["resultat"]["commentaire"]="ERREUR : ".$resultat["erreur"];
        
    } elseif ($tmp["resultat"]["last_car"]==0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "fin_fichier_atteinte", array());
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    $nb_notices_traitees=$tmp["resultat"]["nb_notices_traitees"];
    // on maj $last_car et no_notice
    $_SESSION["operations"][$ID_operation]["no_notice"]+=$nb_notices_traitees;
    $_SESSION["operations"][$ID_operation]["last_car"]=$tmp["resultat"]["last_car"];
    $retour["resultat"]["notice_fichier"]=$tmp["resultat"]["commentaire"];
    $retour["resultat"]["url"]=$tmp["resultat"]["url"];
    $retour["resultat"]["no_notice"]=$_SESSION["operations"][$ID_operation]["no_notice"];
} elseif ($operation == "get_options") {
    $filtre=$_REQUEST["filtre"];
    $plugin_get_options["nom_plugin"].="/".$filtre;
    $retour=applique_plugin($plugin_get_options, array());
    
}


$output = $json->encode($retour);
print($output);


?>