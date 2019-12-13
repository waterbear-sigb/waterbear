<?php

// Cet web service permet d'appliquer un plugin à une notice
// La requête doit fournir au minimum l'ID_notice. Le type_obj peut être fourni soit par la requête, soit par le registre
// Le plugin de traitement est fourni dans le registre
// On peut fournir d'autres paramètres via la requête
// Dans ce cas, dans le registre, on indiquera la liste des variables dans [liste_variables][0,1,2...]
// Les variables corerspondantes seront extraites de $_REQUEST et transmises au plugin via [param_traitement]
// ces variables pourront être exploitées via des alias ou des variables incluses.

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// variables
$plugin_traitement=$GLOBALS["affiche_page"]["parametres"]["plugin_traitement"];
$plugin_notice_2_db=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db"];
$ID_notice=$_REQUEST["ID_notice"];
$liste_variables=$GLOBALS["affiche_page"]["parametres"]["liste_variables"];
$type_obj=$GLOBALS["affiche_page"]["parametres"]["type_obj"]; // par défaut, on prend type_obj dans _parametres...
// ... sauf si fourni via $_REQUEST
if (isset($_REQUEST["type_obj"])) {
    $type_obj=$_REQUEST["type_obj"];
}
$param_traitement=array();

// on récupère la notice
$notice=get_objet_xml_by_id($type_obj, $ID_notice);
if ($notice=="") {
    $retour["resultat"]=0;
    $retour["erreur"]="Notice $ID_notice de type $type_obj inexistante";
    $output = $json->encode($retour);
    print($output);
    die ("");
}

// on récupère les éventuelles variables optionnelles
if (is_array($liste_variables)) {
    foreach ($liste_variables as $variable) {
        if (isset ($_REQUEST[$variable])) {
            $param_traitement[$variable]=$_REQUEST[$variable];
        }
    }
}

// on applique le plugin
$tmp=applique_plugin($plugin_traitement, array("notice"=>$notice, "param_traitement"=>$param_traitement));
if ($tmp["succes"] != 1) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}
$notice_modif=$tmp["resultat"]["notice"];
// On maj la notice
$tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice_modif, "ID_notice"=>$ID_notice));


$output = $json->encode($tmp);
print($output);
?>