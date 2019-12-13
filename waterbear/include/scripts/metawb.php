<?php

if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_metawb"]==1) {
    $metawb_ID=0;
    if ($_REQUEST["metawb_site"] != "") {
        $_SESSION=array(); // on RAZ la session
        $_SESSION["metawb"]=array();
        if ($_REQUEST["metawb_site"] == "tvs") {
            $_SESSION["metawb"]["site"]="tvs";
            $_SESSION["metawb"]["ID"]="tvs";
        } else {
            include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."scripts/connexion_metawb.php");
            $metawb_site=secure_sql($_REQUEST["metawb_site"]);
            
            $sql="select * from inscription where domaine = '$metawb_site'";
            $resultat=sql_as_array(array("sql"=>$sql, "contexte"=>"metawb.php"));
            if (count($resultat)==0) {
                die ("le site Waterbear ".$_REQUEST["metawb_site"]." n'est pas defini");
            }
            $_SESSION["metawb"]["site"]=$resultat[0]["domaine"];
            $metawb_ID=$resultat[0]["ID"];
            if ($resultat[0]["nom_db"] != "") {
                $_SESSION["metawb"]["ID"]=$resultat[0]["nom_db"];
            } else {
                $_SESSION["metawb"]["ID"]=$resultat[0]["ID"];
            }
        }
    } 
    
    // Vérification de RAZ Metawb
    if ($_REQUEST["metawb_site"] != "") { // on ne teste qu'au 1er affichage du site
        $sql2="select * from enquete where ID=".$metawb_ID;
        try {
            $resultat2=sql_as_array(array("sql"=>$sql2, "contexte"=>"metawb.php"));
        } catch (tvs_exception $e) {
            die ("ERREUR SQL :: metawb.php::RAZ metawb : $sql2");
        }
        $delai_relance=time() - (60*60*24*7);
        if (count($resultat2)>0) {
            $date_mail=$resultat2[0]["date_mail"];
            $date_reponse=$resultat2[0]["date_reponse"];
            $reponse_enquete=$resultat2[0]["reponse"];
            $ID_enquete=$resultat2[0]["ID"];
            $clef_enquete=$resultat2[0]["clef"];
            if (($date_mail > $date_reponse) OR $reponse_enquete==3) { // si on n'a jamais répondu ou si une nouvelle enquête a été envoyée depuis la dernière réponse
                if (($date_mail < $delai_relance) OR $reponse_enquete==3) { // si le mail a été envoyé il y a plus de 15 jours
                    header('Content-type: text/html; charset=UTF-8');
                    print ("date mail : $date_mail // delai_relance $delai_relance");
                    print ("ATTENTION : Vous n'avez pas répondu au questionnaire concernant votre utilisation de Waterbear. Faute de réponse dans un très bref délai, votre compte sera désactivé et vos données seront perdues. <br><br>");
                    print ("<a href='http://moccam-en-ligne.fr/metawb/enquete.php?ID=$ID_enquete&clef=$clef_enquete'>Répondre au questionnaire</a>");
                    die ("");
                }
            }
        } // fin de verif RAZ metawb
    }
    
    // MAJ du nom de la base de données
    if ($_SESSION["metawb"]["ID"] != "") {
        if ($_SESSION["metawb"]["ID"] == "tvs") {
            // on ne fait rien : on reste sur les paramètres par défaut
        } else {
            $GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]="wb_". $_SESSION["metawb"]["ID"]; // Nom de la base de données

        }
    } else {
        //affiche_template ("metawb/index.php", array());
        print("<html><body>Vous devez selectionner un site</body></html>");
        die();
        //die ("Aucun site Waterbear n'est selectionne. <a href='bib.php?module=accueil/accueil1&metawb_site=tvs'>Se connecter au site de demonstration</a>");
    }
    
    // on paramètre (éventuellement on crée) les répertoires spécifiques à chaque site
    $GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"].="/". $_SESSION["metawb"]["ID"]; // chemin de stockage des fichiers uploadés
    $GLOBALS["tvs_global"]["conf"]["ini"]["download_path"].="/". $_SESSION["metawb"]["ID"]; // chemin de stockage des fichiers downloadés
    $GLOBALS["tvs_global"]["conf"]["ini"]["download_path_short"].="/". $_SESSION["metawb"]["ID"]; // chemin de stockage des fichiers downloadés
    $GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"].="/". $_SESSION["metawb"]["ID"]; // emplacement des fichiers générés par compilation
    $GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"].="/". $_SESSION["metawb"]["ID"]; // emplacement des fichiers de LOG
    $GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path_short"].="/". $_SESSION["metawb"]["ID"]; // emplacement des fichiers de LOG
    // on essaie de créer les répertoires (s'ils existent déjà ça fait un warning)
    mkdir($GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"]);
    mkdir($GLOBALS["tvs_global"]["conf"]["ini"]["download_path"]);
    mkdir($GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]);
    mkdir($GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"]);
    
    //print_r ($GLOBALS["tvs_global"]["conf"]["ini"]);
   
} // fin du si metawb activé

?>