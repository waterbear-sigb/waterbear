<?php
/**
 * Fichier de configuration
 * Ce fichier est appel� apr�s config.php Il en surcharge donc les valeurs. Toutes les modifications au fichier de configuration
 * doivent �tre faites dans perso.php et jamais dans config.php (qui sera r�initialis� � chaque mise � jour)
 * 
 * Lors de la premi�re installation, renommez ce fichier en "perso.php" et renseignez obligatoiremetn les valeurs
 * de la rubrique "A MODIFIER ABSOLUMENT"
 * 
 * Lors des mises � jour, sauvegardez bien ce fichier (perso.php) et replacez le dans le r�petoire
 * "conf"
 * 
 * */

/** ************* A MODIFIER ABSOLUMENT ***************/
// A D�commenter et modifier si vous souhaitez int�grer le log PHP aux autres logs de Waterbear
//ini_set ("error_log", "/home/moccam/waterbear/LOG/php_log.log"); 
ini_set ("display_errors", "Off");

// SQL
// Indiquez les param�tres de votre connexion SQL
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]="XXX"; // Nom de la base de donn�es 
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_adresse_db"]="localhost"; // URL du serveur (g�n�ralement localhost)
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_login_db"]="XXX"; // Nom utilisateur mysql
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_mdp_db"]="XXX"; // mdp utilisateur mysql

// CHEMINS : emplacement de l'installation de votre site sur le serveur
$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]="/home/moccam/waterbear/"; // install_path

// URL de votre site (racine)
$GLOBALS["tvs_global"]["conf"]["ini"]["wb_url"]="http://waterbear.info";

// utilisateurs qui ne sont pas d�finis dans le registre (super admin)
// ATTENTION : changer le mot de passe du super adminsitrateur (login = superadmin)
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]["superadmin"]["mdp"]="achanger";

/** ************* FIN DU A MODIFIER ABSOLUMENT ***************/


?>