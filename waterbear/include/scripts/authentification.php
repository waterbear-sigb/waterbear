<?php

$bool_alerte_poste=0; // on met 1 si aucun poste n'est fourni lors de l'authentification pour affichage message d'alerte

if ($_REQUEST["reset_user"]=="1") {
    $metawb_site=$_SESSION["metawb"]["site"];
    $metawb_ID=$_SESSION["metawb"]["ID"];
    $_SESSION=array();
    $_SESSION["metawb"]["site"]=$metawb_site;
    $_SESSION["metawb"]["ID"]=$metawb_ID;
}



// User et groupe
if ($_SESSION["system"]["user"] != "") { // 1) regarder si on est déjà identifié
    // on ne fait rien
} elseif ($_REQUEST["login"] != "") { // 2) Est-ce qu'un login est fourni ?'
    $login=$_REQUEST["login"];
    $mdp=$_REQUEST["mdp"];
    $tmp=get_user($login, $mdp);
    if ($tmp["succes"] != 1) {
        if ($page_include == "bib") {
            affiche_template ("div/authentification.php", array("erreur"=>$tmp["erreur"]));
        } else { // si WS
            $chaine=$json->encode($tmp);
            print($chaine);
        }
        die ("");
    }
    $_SESSION["system"]["user"]=$login;
    $_SESSION["system"]["Guser"]=$tmp["resultat"]["groupe"];
    $_SESSION["system"]["nom"]=$tmp["resultat"]["nom"];
    $_SESSION["system"]["infos_user"]=$tmp["resultat"]["infos_user"];
    if ($_REQUEST["poste"] == "") {
        $bool_alerte_poste=1;
    }
} elseif ($GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["user"] != "") {
    $_SESSION["system"]["user"]=$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["user"];
    $_SESSION["system"]["Guser"]=$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["Guser"];
    $_SESSION["system"]["nom"]=$GLOBALS["tvs_global"]["conf"]["ini"]["user_defaut"]["nom"];
} else {
    if ($page_include == "bib") {
        affiche_template ("div/authentification.php", array());
    } else {
        $tmp=array("succes"=>"0", "erreur"=>get_intitule("erreurs/messages_erreur", "user_non_authentifie", array()));
        $chaine=$json->encode($tmp);
        print($chaine);
    }
    die("");
}

// Poste et bib
if ($_SESSION["system"]["poste"] != "") {
    if ($_SESSION["system"]["IP"] != $_SERVER["REMOTE_ADDR"]) {
        die ("Tentative de detournement de session !");
    }
} elseif ($_REQUEST["poste"] != "") {
    $poste=$_REQUEST["poste"];
    $tmp=get_poste($poste);
    if ($tmp["succes"] != 1) {
        if ($page_include == "bib") {
            affiche_template ("div/authentification.php", array("erreur"=>$tmp["erreur"]));
        } else { // si WS
            $chaine=$json->encode($tmp);
            print($chaine);
        }
        die ("");
    }
    $_SESSION["system"]["poste"]=$poste;
    $_SESSION["system"]["Gposte"]=$tmp["resultat"]["groupe"];
    $_SESSION["system"]["nom_poste"]=$tmp["resultat"]["nom"];
    $_SESSION["system"]["bib"]=$tmp["resultat"]["bib"];
    $_SESSION["system"]["IP"]=$_SERVER["REMOTE_ADDR"];
} elseif ($GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["poste"] != "") {
    $_SESSION["system"]["poste"]=$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["poste"];
    $_SESSION["system"]["Gposte"]=$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["Gposte"];
    $_SESSION["system"]["nom_poste"]=$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["nom"];
    $_SESSION["system"]["bib"]=$GLOBALS["tvs_global"]["conf"]["ini"]["poste_defaut"]["bib"];
    $_SESSION["system"]["IP"]=$_SERVER["REMOTE_ADDR"];
} else {
    if ($page_include == "bib") {
        affiche_template ("div/authentification.php", array());
    } else {
        $tmp=array("succes"=>"0", "erreur"=>get_intitule("erreurs/messages_erreur", "poste_non_authentifie", array()));
        $chaine=$json->encode($tmp);
        print($chaine);
    }
    die("");
}

// Langue
if ($_SESSION["system"]["langue"] != "") {
    // on ne fait rien
} elseif ($_REQUEST["langue"] != "") {
    $_SESSION["system"]["langue"]=$_REQUEST["langue"];
} else {
    $_SESSION["system"]["langue"]=$GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_defaut"];
}


?>