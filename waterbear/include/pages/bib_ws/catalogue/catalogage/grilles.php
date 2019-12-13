<?php
// variables pass�es en param�tre
$action=$_REQUEST["action"]; // action effectu�e sur un �l�ment du formulaire
$valeur=$_REQUEST["valeur"]; // valeur du champ de cet �l�ment
$nom_grille=$_REQUEST["nom_grille"]; // nom de la grille (i.e du module ex : unimarc_defaut...)
$ID_element=$_REQUEST["ID_element"]; // ID de cet �l�ment
$idx_onglet=$_REQUEST["idx_onglet"]; 
$ID_notice=$_REQUEST["ID_notice"]; // ID de la notice (si modification)

// Variables pass�es via le registre
$type_objet=$GLOBALS["affiche_page"]["parametres"]["type_objet"]; // type d'objet (biblio...)
$type_formulator_php=$GLOBALS["affiche_page"]["parametres"]["type_formulator_php"]; // type de formulaire (cot� serveur)
$type_formulator_js=$GLOBALS["affiche_page"]["parametres"]["type_formulator_js"]; // type de formulaire (cot� client)

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// on inclut la classe formulator en fonction de $type_formulator_php
//include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/$type_formulator_php.php");

if ($operation == "init_formulator") { // Cr�e un nouveau formulaire vierge
    // 1) on cr�e un objet formulator en fonction de $type_formulator
    $tmp='$_SESSION["operations"]["'.$ID_operation.'"]["formulator"]=new '.$type_formulator_php.'();';
    eval ($tmp);
    // 2 on peuple les onglets
    try {
        if ($ID_notice != "") {
            $retour=applique_plugin($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire_modification"], array("ID_notice"=>$ID_notice, "type_objet"=>$type_objet));
        } else {
            $retour=applique_plugin($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire"], array());
        }
        $_SESSION["operations"][$ID_operation]["formulator"]->init_formulator($retour["resultat"]);
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
    }
    

} elseif ($operation=="action") {
    try {
        //$retour=applique_plugin($type_objet=$GLOBALS["affiche_page"]["parametres"]["plugin_switcher"], array("ID_operation"=>$ID_operation, "ID_element"=>$ID_element, "idx_onglet"=>$idx_onglet, "action"=>$action));
        $retour=applique_plugin($type_objet=$GLOBALS["affiche_page"]["parametres"]["plugin_switcher"], $_REQUEST);
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
    }

} elseif ($operation=="debug") {
    print_r ($_SESSION["operations"][$ID_operation]["formulator"]);
}



$output = $json->encode($retour);
print($output);


?>