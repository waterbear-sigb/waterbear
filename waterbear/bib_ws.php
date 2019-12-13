<?PHP
//$t1=microtime(true);
//print ($t1." <br>\n");
$conf_path="conf/";
include_once($conf_path."config.php");
include_once($conf_path."version.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/sql.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/log.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/util.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/registre.php");
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/templates.php");
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
$page_include="bib_ws";
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/a_inclure.php"); 

// Gestion de la session
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/session.php");

// Metawb
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/metawb.php");

//$t2=microtime(true);
//$x1=$t2-$t1;
//print ("includes : $x1 <br>\n");
// optimisation : on inclut le registre pages/bib_ws
//include_once ($GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]."/pages_bib_ws.php");
$GLOBALS["dico_registre_include"]=array();

// TMP !! optimisation désactivée (temporairement ?) car compliqué à gérer dans le contexte metawaterbear et efficacité à démontrer 
//include_once ($GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]."/dictionnaire.php");

//$t3=microtime(true);
//$x2=$t3-$t2;
//print ("includes optimisation : $x2 <br>\n");




$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
$retour=array();


// variables passées en paramètre
$module=$_REQUEST["module"];
$operation=$_REQUEST["operation"];
$reset=$_REQUEST["reset"];
$ID_operation=$_REQUEST["ID_operation"];



//$t4=microtime(true);
//$x3=$t4-$t3;
//print ("log bib_ws : $x3 <br>\n");
// Connexion MySQL
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/connexion_mysql.php");

// On ajoute la configuration définie dans le registre
registre_2_conf();



// Gestion de la session
//include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/session.php");

//$t5=microtime(true);
//$x4=$t5-$t4;
//print ("Session : $x4 <br>\n");
// Gestion de l'authentification
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/authentification.php");
// Vérification des droits pour la page appelée

// On refait un registre_2_conf car tant que l'authetification n'a pas eu lieu on ne peut pas savoir si le user est habilité à surcharger le fichier de conf
//registre_2_conf();

// LOG
if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_ws_querys"]["bool"] == 1) {
    tvs_log("bib_ws_querys", "APPEL", array($module, $json->encode($_REQUEST)));
}

try {$droit=verifie_droits_page ("bib_ws/".$module);}
catch (tvs_exception $e) {print ($json->encode(array("succes"=>0, "erreur"=>get_intitule("erreurs/erreur_div", "message", array("message"=>get_exception($e->get_infos()))))));die("");}
if ($droit == 0) {
  	print ($json->encode(array("succes"=>0, "erreur"=>get_intitule("erreurs/erreur_droits_page", "message", array()))));
	die();
}
//$t6=microtime(true);
//$x5=$t6-$t5;
//print ("droits : $x5 <br>\n");

// Gestion du traçage des plugins
dbg_plugins_init ("bib_ws");

// Gestion des include du registre
//include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/registre_include.php");

//$t7=microtime(true);
//$x6=$t7-$t6;
//print ("includes registre : $x6 <br>\n");

// Appel des scripts en fonction de $module
$GLOBALS["affiche_page"]=array("page"=>"bib_ws/".$module, "include"=>"", "template"=>array(), "parametres"=>array(), "idx_page"=>0);
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

//$t8=microtime(true);
//$x7=$t8-$t7;
//print ("fin : $x7 <br>\n");



//print_r ($GLOBALS["affiche_page"]);
// LOG
tvs_log("bib_ws_querys", "RETOUR", array($module, $output));


//$t9=microtime(true);
//$x8=$t9-$t8;
//print ("apres log : $x8 <br>\n");

//$total=$x1+$x2+$x3+$x4+$x5+$x6+$x7+$x8;
//print ("TOTAL : $total <br>\n");



?>