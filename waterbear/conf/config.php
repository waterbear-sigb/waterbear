<?PHP
/**
 * NE PAS MODIFIER CE FICHIER
 * 
 * Faites figurer vos modifications dans perso.php qui figure dans le même répertoire
 * 
 * */

// A AJOUTER AU DEBUT
// perso.php doit figurer dans le même répertoire que conf.php
include ("perso.php");

// Configurations ne pouvant figurer dans le registre
// souvent parce qu'elles sont nécessaires au registre lui-même
// toujours de la forme : $GLOBALS["tvs_global"]["conf"]["ini"]["XXXXX"]="YYYY"

// Configurations PHP si on ne peut pas les mettre dans php.ini
ini_set ("display_errors", "Off");
ini_set ("log_errors", "On");
//ini_set ("error_log", "/home/moccam/waterbear/LOG/php_log.log");

// registre_2_conf
// si vaut 0, le registre ne peut pas surcharger le fichier de conf. Si vaut 1 il le peut. Si vaut 2 il ne le peut que pour les users décalrés ci-dessous
$GLOBALS["tvs_global"]["conf"]["ini"]["bool_registre_2_conf"]=1; // en PROD, on n'autorise le registre a surcharger le fichier de conf que pour certains utilisateurs
$GLOBALS["tvs_global"]["conf"]["ini"]["registre_2_conf_users"]=array(); // liste des utilisateurs autorises a surcharger le registre : exemple array("toto", "tutu")


// Metawb
// NE PAS UTILISER : spécifique au mode SaaS de Waterbear.info
/**
$GLOBALS["tvs_global"]["conf"]["ini"]["bool_metawb"]=0; // si vaut 1 on récupérera le nom de la DB et les les Paths via metawb
$GLOBALS["tvs_global"]["conf"]["ini"]["metawb_nom_db"]=""; // Nom de la base de données
$GLOBALS["tvs_global"]["conf"]["ini"]["metawb_adresse_db"]=""; // URL du serveur
$GLOBALS["tvs_global"]["conf"]["ini"]["metawb_login_db"]=""; // Nom utilisateur mysql
$GLOBALS["tvs_global"]["conf"]["ini"]["metawb_mdp_db"]=""; // mdp utilisateur mysql
**/

$GLOBALS["tvs_global"]["conf"]["ini"]["metawb_bool_master"]="0"; // Si vaut 1, le site est master metawb. i.e toutes les modifs du registre et des objets sont loggées pour pouvoir générer un script de maj


// SQL
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]="XXX"; // Nom de la base de données 
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_adresse_db"]="localhost"; // URL du serveur
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_login_db"]="XXX"; // Nom utilisateur mysql
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_mdp_db"]="XXX"; // mdp utilisateur mysql

// CHEMINS
//$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]="/home/moccam/waterbear/"; // install_path : ne pas définir ici mais dans perso.php
$GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]=$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]."include/"; // include_path
$GLOBALS["tvs_global"]["conf"]["ini"]["plugins_path"]=$GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."plugins"; // chemin des plugins (sans '/' à la fin)
$GLOBALS["tvs_global"]["conf"]["ini"]["template_path"]=$GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."templates"; // chemin des templates
$GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"]=$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]."upload"; // chemin de stockage des fichiers uploadés
$GLOBALS["tvs_global"]["conf"]["ini"]["download_path"]=$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]."download"; // chemin de stockage des fichiers downloadés
$GLOBALS["tvs_global"]["conf"]["ini"]["download_path_short"]="download";
$GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]=$GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."registre_compilation"; // emplacement des fichiers générés par compilation

// WS
//$GLOBALS["tvs_global"]["conf"]["ini"]["wb_url"]="http://waterbear.info"; // ne pas definir ici mais dans perso.php
$GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]=$GLOBALS["tvs_global"]["conf"]["ini"]["wb_url"]."/bib_ws.php";
$GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["opac"]=$GLOBALS["tvs_global"]["conf"]["ini"]["wb_url"]."/opac_ws.php";

// LOG
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"]=$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]."LOG"; // log_path
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path_short"]="LOG"; // pour accès dans une url

// User defaut
$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]=array();
$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["user"]=""; // mettre à "" si on veut désactiver l'utilisateur par défaut
$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["Guser"]="visiteur";
$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["nom"]="Visiteur anonyme";

// Users System
// utilisateurs qui ne sont pas définis dans le registre (super admin, maintenance, metawb...)
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]=array();
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]["superadmin"]=array(); // tâches accomplies directement en WS par metawb
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]["superadmin"]["groupe"]="admin";
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]["superadmin"]["mdp"]="achanger";
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]["superadmin"]["nom"]="Super administrateur";


// Poste defaut
$GLOBALS["tvs_global"]["conf"]["ini"]["bool_poste_ip_uniquement"]=0; // si 1, les postes sont identifiés uniquement par leur adresse IP (pas de choix possible)
$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]=array();
$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["poste"]="Pdefaut"; // mettre à "" si on veut désactiver le poste par défaut
$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["Gposte"]="divers";
$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["nom"]=utf8_encode("Poste indéterminé");
$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["bib"]="BIB";

////////////////////////////////// ** SQL ** ////////////////////////////////////////////////////// SQL
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["sql_querys"]["fichier"]="SQL.log"; 
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["sql_querys"]["bool"]=0; // 1 pour logger 
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["sql_querys"]["tab"]="            "; // décalage 

////////////////////////////////// ** PLUGINS ** //////////////////////////////////////////////////////
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_querys"]["fichier"]="GLOBAL.log"; // Requêtes et résultats des plugins GLOBAL.log
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_querys"]["bool"]=0; // 1 pour logger 
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_querys"]["tab"]="        "; // décalage

////////////////////////////////// ** BIB et BIB_WS ** //////////////////////////////////////////////////////
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_ws_querys"]["fichier"]="GLOBAL.log"; // Requêtes et résultats de BIB_WS.php
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_ws_querys"]["bool"]=0; // 1 pour logger 
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_ws_querys"]["tab"]="    "; // décalage

$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_querys"]["fichier"]="GLOBAL.log"; // Requêtes et résultats de BIB.php
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_querys"]["bool"]=0; // 1 pour logger
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["bib_querys"]["tab"]=""; // décalage

////////////////////////////////// ** REGISTRE ** //////////////////////////////////////////////////////
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_querys"]["fichier"]="GLOBAL.log"; // appels dans le registre
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_querys"]["bool"]=0; // 1 pour logger
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_querys"]["tab"]=""; // décalage

$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_sub_querys"]["fichier"]="GLOBAL.log"; // sous-appels dans le registre
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_sub_querys"]["bool"]=0; // 1 pour logger
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_sub_querys"]["tab"]="------"; // décalage

////////////////////////////////// ** REGISTRE SQL ** //////////////////////////////////////////////////////
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_querys_sql"]["fichier"]="GLOBAL.log"; // appels dans le registre
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_querys_sql"]["bool"]=0; // 1 pour logger
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_querys_sql"]["tab"]=""; // décalage

$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_sub_querys_sql"]["fichier"]="GLOBAL.log"; // sous-appels dans le registre
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_sub_querys_sql"]["bool"]=0; // 1 pour logger
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["registre_sub_querys_sql"]["tab"]="------"; // décalage

////////////////////////////////// ** DBG ** //////////////////////////////////////////////////////
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["dbg"]["fichier"]="GLOBAL.log"; // Pour débugger : tvs_log("dbg", "DBG", array(XXXXXX));
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["dbg"]["bool"]=1; // 1 pour logger
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["dbg"]["tab"]=""; // décalage

////////////////////////////////// ** ERREURS ** //////////////////////////////////////////////////////
// Commenter si on ne veut pas que les erreurs PHP apparaissent dans GLOBAL.log
//ini_set ("error_log", $GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"]."/GLOBAL.log");
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["sql_errors"]["fichier"]="sql_errors.log"; // erreurs SQL
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["sql_errors"]["bool"]=1; // 1 pour logger 

$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_errors"]["fichier"]="plugins_errors.log"; // erreurs des plugins
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_errors"]["bool"]=1; // 1 pour logger 

////////////////////////////////// ** TRACE PLUGINS ** //////////////////////////////////////////////////////
// Active / désactive le traceur de plugins *********************************** DEBUGGER *****************************************************
$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["DBGPLUGINS"]["bool"]=0;
// *******************************************************************************************************************************************

// Tracer les plugins dans les messages d'erreur
$GLOBALS["tvs_global"]["conf"]["ini"]["trace_erreurs_plugins"]=0;


// DROITS
$GLOBALS["tvs_global"]["conf"]["ini"]["droit_par_defaut"]=1; // Droits par défaut (pour les pages ou les WS). Si 0 accès interdit sauf si spécifiquement autorisé. Si 1 accès autorisé sauf si interdit

// INTERNATIONAL
$GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_defaut"]="_fr";
$GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_devel"]="_fr"; // a priori toujours _fr. S'il ne trouve pas un intitulé dans sa langue, il cherchera en _fr

// Scripts à inclure AVANT le dezerialize()
$GLOBALS["tvs_global"]["conf"]["ini"]["a_inclure"]["bib_ws"]["catalogue/catalogage/grilles"][0]="classes/tvs_formulator_server.php";
$GLOBALS["tvs_global"]["conf"]["ini"]["a_inclure"]["bib_ws"]["autocomplete/biblio/standard/morceaux"][0]="classes/tvs_formulator_server.php";

// module_defaut
$GLOBALS["tvs_global"]["conf"]["ini"]["module_defaut"]="accueil/accueil1"; //module chargé par défaut



// A AJOUTER A LA FIN
// perso.php doit figurer dans le même répertoire que conf.php
include ("perso.php");

?>
