<?php

/**
 * 
 * Dans opération
 * [ID_periodique]
 * [ddbl_fascicule]
 * [ID_fascicule]
 * [liste_abos_utilises]
 * [elements_crees]
 * 
 * 
 * 
 * 
*/


$action=$_REQUEST["action"];
$cab=$_REQUEST["cab"];
$ID_abo=$_REQUEST["id_abo"];
$bool_hs=$_REQUEST["bool_hs"];
$bool_afficher_tous_abos=$_REQUEST["bool_afficher_tous_abos"];
$no_hs=$_REQUEST["no_hs"];
$date_hs=$_REQUEST["date_hs"];

$plugin_cab_2_infos=$GLOBALS["affiche_page"]["parametres"]["plugin_cab_2_infos"];
$plugin_recherche_periodique_issn=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_periodique_issn"];
$plugin_recherche_periodique_ID=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_periodique_ID"];
$plugin_formate_periodique=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_periodique"];
$plugin_recherche_abos=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_abos"];
$plugin_ddbl_fascicule=$GLOBALS["affiche_page"]["parametres"]["plugin_ddbl_fascicule"];
$plugin_crea_fascicule=$GLOBALS["affiche_page"]["parametres"]["plugin_crea_fascicule"];
$plugin_notice_2_db_biblio=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_biblio"];
$plugin_maj_fascicule=$GLOBALS["affiche_page"]["parametres"]["plugin_maj_fascicule"];
$plugin_notice_2_db_exemplaire=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_exemplaire"];
$liste_plugins_crea_exemplaire=$GLOBALS["affiche_page"]["parametres"]["liste_plugins_crea_exemplaire"];
$plugin_ddbl_cab=$GLOBALS["affiche_page"]["parametres"]["plugin_ddbl_cab"];
$plugin_calcule_prochaine_date=$GLOBALS["affiche_page"]["parametres"]["plugin_calcule_prochaine_date"];
$plugin_maj_abo=$GLOBALS["affiche_page"]["parametres"]["plugin_maj_abo"];
$plugin_notice_2_db_abo=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_abo"];
$plugin_formate_fascicule=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_fascicule"];
$plugin_formate_exemplaire=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_exemplaire"];
$plugin_select_abonnements=$GLOBALS["affiche_page"]["parametres"]["plugin_select_abonnements"];

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// 1) on récupère le type de cab


// 1) On récupère le type de cab
if (substr($cab, 0, 3) == "ID:") {
    $type="ID_notice";
    $cab=str_replace("ID:", "", $cab);
} else {
    $tmp=applique_plugin ($plugin_cab_2_infos, array("cab"=>$cab));
    if ($tmp["succes"] != 1) {
        $type="biblio";
    } else {
        $type=$tmp["resultat"]["infos"]["type"];
    }
    if ($type != "exemplaire" AND $type != "ID_notice") {
        $type="biblio";
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// Si biblio...


if ($type == "biblio" OR $type=="ID_notice") {
    
    // 1) on RAZ la liste des abos utilisés
    $_SESSION["operations"]["'.$ID_operation.'"]["liste_abos_utilises"]="";
    $_SESSION["operations"]["'.$ID_operation.'"]["elements_crees"]=array();
    
    // 1.a) on RAZ fascicules et exemplaires dans le client
    $retour["resultat"]["bool_raz_fascicules"]=1;
    
    // 2.a) on récupère la notice de périodique
    if ($type == "biblio") { // ISBN
        $tmp=applique_plugin($plugin_recherche_periodique_issn, array("ISSN"=>$cab));
    } else { // ID notice
        $tmp=applique_plugin($plugin_recherche_periodique_ID, array("ID_notice"=>$cab));
    }
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $nb_notices=$tmp["resultat"]["nb_notices"];
    if ($nb_notices == 0) {
        $retour["resultat"]["message"]="Aucun periodique ayant l'identifiant $type = $cab n'existe";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    if ($nb_notices > 1) {
        $retour["resultat"]["message"]="Plusieurs periodiques portent l'identifiant $type = $cab";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    // 2.b) On enregistre ID_ligne_commande dans l'opération
    $_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"]=$tmp["resultat"]["notices"][0]["ID"];
    //$notice_periodique=$tmp["resultat"]["notices"][0]["xml"];
    
    
    
} 


////////////////////////////////////////////////////////////////////////////////////////////////////
// Si cab exemplaire

if ($type == "exemplaire") { // SI EXEMPLAIRE

    // 0) Vérifier si le cab n'a pas déjà été utilisé
    $tmp=applique_plugin($plugin_ddbl_cab, array("cab"=>$cab));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $tmp2=$tmp["resultat"]["nb_notices"];
    if ($tmp2 != 0) {
        $retour["resultat"]["message"]="Ce code barre ($cab) est deja utilise";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    // 1) on récupère l'abonnement
    if ($ID_abo == "") {
        $retour["resultat"]["message"]="Vous devez selectionner un abonnement";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    // 2) On récupère le n° et date du numéro à recevoir (et à partir de ça une clef de ddbl)
    if ($bool_hs == "true") {
        $prochain_numero=$no_hs;
        $prochaine_date=$date_hs;
    } else {
        $abo=get_objet_by_id("abo", $ID_abo);
        $prochain_numero=$abo["a_prochain_numero"];
        $prochaine_date=$abo["a_prochaine_date"];
        $mode_parution=$abo["a_mode_parution"];
        $parait_jours=$abo["a_parait_jours"];
        $parait_mois=$abo["a_parait_mois"];
    }
    $ddbl_fascicule=$prochain_numero."_".$prochaine_date;
    
    // 3) Est-ce qu'il faut créer une notice de fascicule ou pas ?
    $tmp=applique_plugin($plugin_ddbl_fascicule, array("ID_periodique"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"], "ddbl_fascicule"=>$ddbl_fascicule));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $nb_fascicules=$tmp["resultat"]["nb_notices"];
    if ($nb_fascicules == 1) { // Si fascicule existe déjà
        $_SESSION["operations"]["'.$ID_operation.'"]["ID_fascicule"]=$tmp["resultat"]["notices"][0]["ID"];
        $notice_fascicule=get_objet_xml_by_id("biblio", $_SESSION["operations"]["'.$ID_operation.'"]["ID_fascicule"]);
    } elseif ($nb_fascicules == 0) { // si fascicule n'existe pas, on le crée
        $titre_fascicule="num. $prochain_numero du $prochaine_date"; // TMP !!
        $tmp=applique_plugin($plugin_crea_fascicule, array("ID_periodique"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"], "numero"=>$prochain_numero, "date"=>$prochaine_date, "titre_fascicule"=>$titre_fascicule));
        if ($tmp["succes"]==0) {
            $output = $json->encode($tmp);
            print($output);
            die("");
        }
        $notice_fascicule=$tmp["resultat"]["notice"];
        $tmp=applique_plugin($plugin_notice_2_db_biblio, array("notice"=>$notice_fascicule));
        if ($tmp["succes"]==0) {
            $output = $json->encode($tmp);
            print($output);
            die("");
        }
        $_SESSION["operations"]["'.$ID_operation.'"]["ID_fascicule"]=$tmp["resultat"]["ID_notice"];
        $notice_fascicule=add_champ_000($notice_fascicule, $tmp["resultat"]["ID_notice"], "biblio"); // on rajoute le champ 000 pour formatage
    } else { // si plusieurs fascicules => erreur
        $retour["resultat"]["message"]="$nb_fascicules fascicules sont deja associes a la clef $ddbl_fascicule pour le periodique ".$_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"];
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    // 3.a) On formate la notice fascicule pour affichage
    $tmp=applique_plugin($plugin_formate_fascicule, array("notice"=>$notice_fascicule));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $retour["resultat"]["fascicules"]=$tmp["resultat"]["texte"];
    
    // 4) création de l'exemplaire
    // 4.a) on récupère les infos sur l'exemplaire dans le champ 697 de l'abonnement
    $param_modele=array("ID_abo"=>$ID_abo, "notice_abo"=>$notice_abo);
    $notice_abo=get_objet_xml_by_id("abo", $ID_abo);
    $notice_abo_marcxml=new tvs_marcxml(array("type_obj"=>"abo", "ID"=>$ID_abo));
    $notice_abo_marcxml->load_notice($notice_abo);
    $tmp=$notice_abo_marcxml->get_champs("697", "");
    $champ_697=$tmp[0];
    $liste_ss_champs_997=$notice_abo_marcxml->get_ss_champs($champ_697, "", "", "");
    foreach ($liste_ss_champs_997 as $ss_champ_997) {
        $code=$notice_abo_marcxml->get_nom_ss_champ($ss_champ_997);
        $valeur=$notice_abo_marcxml->get_valeur_ss_champ($ss_champ_997);
        if ($code=="a") {
            $plugin_crea_exemplaire=$liste_plugins_crea_exemplaire[$valeur];
        } else {
            $param_modele[$code]=$valeur;
        }
    }
    
    // 4.b) on génère une notice exemplaire d'après le modèle, en y intégrant les infos fournies
    //      dans le champ 697 du bon de commandes
    $tmp=applique_plugin ($plugin_crea_exemplaire, array("param_modele"=>$param_modele, "cab"=>$cab));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $notice_exemplaire=$tmp["resultat"]["notice"];
    
    // 4.c) On enregistre la notice exemplaire (et on récupère un ID)
    $tmp=applique_plugin ($plugin_notice_2_db_exemplaire, array("notice"=>$notice_exemplaire));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $ID_notice_exemplaire=$tmp["resultat"]["ID_notice"];
    $notice_exemplaire=add_champ_000($notice_exemplaire, $ID_notice_exemplaire, "exemplaire"); // on rajoute le champ 000 pour formatage
    
    // 4.d) On formate la notice exemplaire pour affichage
    $tmp=applique_plugin($plugin_formate_exemplaire, array("notice"=>$notice_exemplaire));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $retour["resultat"]["exemplaires"]=$tmp["resultat"]["texte"];
    
    // 4.e) On maj la notice fascicule (en ajoutant l'exemplaire)
    $tmp=applique_plugin ($plugin_maj_fascicule, array("notice"=>$notice_fascicule, "ID_exemplaire"=>$ID_notice_exemplaire, "notice_exemplaire"=>$notice_exemplaire));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $notice_fascicule2=$tmp["resultat"]["notice"];
    
    // 4.f) On enregistre la notice biblio
    $tmp=applique_plugin ($plugin_notice_2_db_biblio, array("notice"=>$notice_fascicule2, "ID_notice"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_fascicule"]));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    
    // 5) On calcule la date du prochain n°
    if ($bool_hs != "true") {
        // 5.a) on calcule prochaine date et n°
        $tmp=applique_plugin($plugin_calcule_prochaine_date, array("date_dernier_no"=>$prochaine_date, "mode_parution"=>$mode_parution, "parait_jours"=>$parait_jours, "parait_mois"=>$parait_mois));
        if ($tmp["succes"] != 1) {
            $output = $json->encode($tmp);
            print($output);
            die("");
        }
        $date_prochain_no=$tmp["resultat"]["date"];
        $num_prochain_no=$prochain_numero+1;
        
        
        // 5.b) on modifie la notice
        $tmp=applique_plugin($plugin_maj_abo, array("notice"=>$notice_abo, "prochaine_date"=>$date_prochain_no, "prochain_no"=>$num_prochain_no));
        if ($tmp["succes"] != 1) {
            $output = $json->encode($tmp);
            print($output);
            die("");
        }
        $notice_abo2=$tmp["resultat"]["notice"];
        
        // 5.c) on enregistre la notice dans la DB
        $tmp=applique_plugin($plugin_notice_2_db_abo, array("notice"=>$notice_abo2, "ID_notice"=>$ID_abo));
        if ($tmp["succes"] != 1) {
            $output = $json->encode($tmp);
            print($output);
            die("");
        }
            
    }
    
    // 6) on maj $liste_abos_utilises
    if ($bool_hs != "true") {
        if ($_SESSION["operations"]["'.$ID_operation.'"]["liste_abos_utilises"] != "") {
            $_SESSION["operations"]["'.$ID_operation.'"]["liste_abos_utilises"] .= ", ".$ID_abo;
        } else {
            $_SESSION["operations"]["'.$ID_operation.'"]["liste_abos_utilises"] = $ID_abo;
        }
    }
    
    
    

    
} // fin du 'si exemplaire...'

////////////////////////////////////////////////////////////////////////////////////////////////////
// Recherche et affichage périodique et liste abos

// on récupère la notice
$notice_periodique=get_objet_xml_by_id("biblio", $_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"]);

//  on formate et affiche la notice de périodique
$tmp=applique_plugin($plugin_formate_periodique, array("notice"=>$notice_periodique));
if ($tmp["succes"]==0) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}
$retour["resultat"]["texte"]=$tmp["resultat"]["texte"];

//  On récupère la liste des abonnements liés à ce périodique
if ($bool_afficher_tous_abos == 'true') {
    $liste_abos_utilises="";
} else {
    $liste_abos_utilises=$_SESSION["operations"]["'.$ID_operation.'"]["liste_abos_utilises"];
}
// pour les séries
$tmp=applique_plugin($plugin_recherche_abos, array("ID_periodique"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"], "liste_abos_utilises"=>$liste_abos_utilises));
if ($tmp["succes"]==0) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}
$retour["resultat"]["abos"]=$tmp["resultat"]["notices"];

// pour les HS et anciens n°
if ($type == "biblio" OR $type=="ID_notice") {
    $tmp=applique_plugin($plugin_select_abonnements, array("notice"=>$notice_periodique));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $retour["resultat"]["select_abos"]=$tmp["resultat"]["texte"];
}

// date du jour (pcque c'est galère en JS :/
$retour["resultat"]["date_jour"]=date("Y-m-d");
$retour["resultat"]["ID_revue"]=$_SESSION["operations"]["'.$ID_operation.'"]["ID_periodique"];


$output = $json->encode($retour);
print($output);
?>