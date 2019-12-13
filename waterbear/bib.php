<?PHP
$conf_path="conf/";
include_once($conf_path."config.php");
include_once($conf_path."version.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/sql.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/log.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/util.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/registre.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/templates.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/fichiers.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/plugins.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/objets.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_registre.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_exception.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_marcxml.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/gestion_objets_db.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes_ext/JSON.php");

// On inclut les scripts qui DOIVENT être inclus avant le début de la session
// (sérialisation des objets dans les sessions)
// Ces scripts sont définis dans config.php
$page_include="bib";
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/a_inclure.php"); 

// Gestion de la session
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/session.php");

// Metawb
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/metawb.php");

// optimisation : on inclut le registre pages/bib
//include_once ($GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]."/pages_bib.php");
$GLOBALS["dico_registre_include"]=array();

// TMP !! optimisation désactivée (temporairement ?) car compliqué à gérer dans le contexte metawaterbear et efficacité à démontrer 
//include_once ($GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]."/dictionnaire.php");



header('Content-type: text/html; charset=UTF-8');

// variables passées en paramètre
$module=$_REQUEST["module"];
$operation=$_REQUEST["operation"];
$reset=$_REQUEST["reset"];
$ID_operation=$_REQUEST["ID_operation"];

// skins
if ($_REQUEST["skin"] != "") {
    $_SESSION["system"]["skin"]=$_REQUEST["skin"];
}



// Connexion MySQL
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/connexion_mysql.php");

// On ajoute la configuration définie dans le registre
registre_2_conf();

// Module par défaut
if ($module == "") {
    $module=$GLOBALS["tvs_global"]["conf"]["ini"]["module_defaut"];
}

// Gestion de la session
//include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/session.php");


// Gestion de l'authentification
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/authentification.php");


// On refait un registre_2_conf car tant que l'authetification n'a pas eu lieu on ne peut pas savoir si le user est habilité à surcharger le fichier de conf
//registre_2_conf();

// LOG
$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_querys"]["bool"] == 1) {
    //tvs_log("bib_querys", "APPEL", array($module, $json->encode($_REQUEST)));
}


// Vérification des droits pour la page appelée
try {$droit=verifie_droits_page ("bib/".$module); }
catch (tvs_exception $e) {affiche_template ("erreurs/erreur_div.php", array("message"=>get_exception($e->get_infos())));die("");}
if ($droit == 0) {
  	affiche_template ("erreurs/erreur_droits_page.php", array("page"=>$GLOBALS["affiche_page"]["page"], "message"=>$GLOBALS["message"]));
	die();
}


// Gestion du traçage des plugins
dbg_plugins_init ("bib");

// Gestion des include du registre
//include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/registre_include.php");

//tvs_log ("registre_querys", "****BIB****", var_export($GLOBALS["registre_include"], true));

// Appel des scripts en fonction de $module
$GLOBALS["affiche_page"]=array("page"=>"bib/".$module, "include"=>"", "template"=>array(), "parametres"=>array("bool_alerte_poste"=>$bool_alerte_poste), "idx_page"=>0);

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

//print_r($_SESSION);
//print_r ($GLOBALS["affiche_page"]);


// LOG ... pas intéressant pour l'instant, mais voir éventuellement pour logger les affiche_page ????
//tvs_log("bib_querys", "RETOUR", array($module, "------------------------------------------------------------------"));






?>