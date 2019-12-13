<?php

$retour=array();
$retour["succes"]=1;
$retour["resultat"]=array("url"=>"");

$cab=$_REQUEST["cab"];

$plugin_cab_2_infos=$GLOBALS["affiche_page"]["parametres"]["plugin_cab_2_infos"];
$plugin_recherche_biblio=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_biblio"];
$plugin_recherche_exe=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_exe"];
$plugin_recherche_lecteur=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_lecteur"];
$url_biblio=$GLOBALS["affiche_page"]["parametres"]["url_biblio"];
$url_exe=$GLOBALS["affiche_page"]["parametres"]["url_exe"];
$url_lecteur=$GLOBALS["affiche_page"]["parametres"]["url_lecteur"];

// on détermine le type de code barre
$tmp=applique_plugin ($plugin_cab_2_infos, array("cab"=>$cab));
if ($tmp["succes"] != 1) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}

$type=$tmp["resultat"]["infos"]["type"];

// On fait la recherche
if ($type == "biblio") {
    $retour["resultat"]["url"]=$url_biblio;
    $tmp=applique_plugin ($plugin_recherche_biblio, array("cab"=>$cab));
} elseif ($type == "exemplaire") {
    $retour["resultat"]["url"]=$url_exe;
    $tmp=applique_plugin ($plugin_recherche_exe, array("cab"=>$cab));
} elseif ($type == "lecteur") {
    $retour["resultat"]["url"]=$url_lecteur;
    $tmp=applique_plugin ($plugin_recherche_lecteur, array("cab"=>$cab));
} else {
    $retour["succes"]=0;
    $retour["erreur"]="type de code barre inconnu : $type";
    $output = $json->encode($retour);
    print($output);
    die("");
}

if ($tmp["succes"] != 1) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}

// on regarde s'il y a des résultats 
if ($tmp["resultat"]["nb_notices"]==0) {
    $retour["succes"]=0;
    $retour["erreur"]="$type $cab inconnu";
    $output = $json->encode($retour);
    print($output);
    die("");
}

// On récupère ID
$ID=$tmp["resultat"]["notices"][0]["ID"];
if ($ID=="") {
    $retour["succes"]=0;
    $retour["erreur"]="$type $cab - impossible de recuperer un ID";
    $output = $json->encode($retour);
    print($output);
    die("");
}

// On formate l'url
$retour["resultat"]["url"]=str_replace("#cab#", $cab, $retour["resultat"]["url"]);
$retour["resultat"]["url"]=str_replace("#ID#", $ID, $retour["resultat"]["url"]);

$output = $json->encode($retour);
print($output);



?>