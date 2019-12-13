<?php
$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// variables
$type_obj=$_REQUEST["type_obj"];
$traitement=$_REQUEST["traitement"];
$params=$_REQUEST["params"];
$panier=$_REQUEST["panier"];
$page=$_REQUEST["page"];
$plugin_get_liste_traitements=$GLOBALS["affiche_page"]["parametres"]["plugin_get_liste_traitements"];
$plugin_get_formulaire=$GLOBALS["affiche_page"]["parametres"]["plugin_get_formulaire"];
$plugin_recherche=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche"];

$erreurs="";



if ($operation == "get_liste_traitements") {
    $plugin_get_liste_traitements["nom_plugin"].="/".$type_obj;
    $liste_traitements=applique_plugin($plugin_get_liste_traitements, array());
    $retour=$liste_traitements;
} elseif ($operation == "get_formulaire") {
    $plugin_get_formulaire["nom_plugin"].="/".$traitement;
    $formulaire=applique_plugin($plugin_get_formulaire, array());
    $retour=$formulaire;
} elseif ($operation == "lance_traitement") {
    $feedback=array(); // permet de passer le retour du plugin n en paramètre au plugin n+1
    
    // on convertit $params de json en array
    $params_array=$json->decode($params);
    
    // on récupère le plugin d'exéution et le plugin d'enregistrement
    $plugin_get_formulaire["nom_plugin"].="/".$traitement;
    $formulaire=applique_plugin($plugin_get_formulaire, array());
    if ($formulaire["succes"] != 1) {
        $output = $json->encode($formulaire);
        print($output);
        die("");
    }
    $plugin_execution=$formulaire["resultat"]["plugin_execution"];
    $plugin_enregistrement=$formulaire["resultat"]["plugin_enregistrement"];
    
    
    // On récupère les notices du panier
    $tmp=applique_plugin($plugin_recherche, array("type_obj"=>$type_obj, "panier"=>$panier, "page"=>$page));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $nb_notices=$tmp["resultat"]["nb_notices"];
    $nb_pages=$tmp["resultat"]["nb_pages"];
    $lignes=$tmp["resultat"]["notices"];
    $retour["resultat"]["resume"]="";
    $retour["resultat"]["nb_notices"]=$nb_notices;
    $retour["resultat"]["nb_pages"]=$nb_pages;
    $retour["resultat"]["erreurs"]="";
    foreach ($lignes as $ligne) { // pour chaque notice
        $ID_notice=$ligne["ID"];
        $notice=$ligne["xml"];
        if ($notice == "") { // notice vide ou false
            $erreurs.=" ERREUR XML : notice $ID_notice XML mal forme <br> \n";
            continue;
        }
        $bool_erreur_traitement=0;
        
        // On exécute le plugin
        if (is_array($plugin_execution)) {
            $tmp=applique_plugin($plugin_execution, array("type_obj"=>$type_obj, "params"=>$params_array, "ID_notice"=>$ID_notice, "notice"=>$notice, "feedback"=>$feedback));
            if ($tmp["succes"] != 1) {
                $bool_erreur_traitement=1;
                $erreurs.="ERREUR plugin execution notice $ID_notice : ".$tmp["erreur"]."<br>\n";
                //$output = $json->encode($tmp);
                //print($output);
                //die("");
            }
            $feedback=$tmp["resultat"];
            $notice=$tmp["resultat"]["notice"];
        }
        
        // On enregistre la notice
        if (is_array($plugin_enregistrement) AND $bool_erreur_traitement != 1) {
            $tmp=applique_plugin($plugin_enregistrement, array("ID_notice"=>$ID_notice, "notice"=>$notice));
            if ($tmp["succes"] != 1) {
                $erreurs.="ERREUR plugin enregistrement notice $ID_notice : ".$tmp["erreur"]."<br>\n";
                //$output = $json->encode($tmp);
                //print($output);
                //die("");
            }
        }
        
        
        $retour["resultat"]["resume"].=$ID_notice."<br/>";
    } // fin du pour chaque notice
    $retour["resultat"]["erreurs"]=$erreurs;
    
    
}


$output = $json->encode($retour);
print($output);
?>