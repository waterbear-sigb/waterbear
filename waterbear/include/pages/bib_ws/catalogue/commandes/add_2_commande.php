<?php

/**
 * Ce WS ajoute des champs exemplaires (997) aux notices d'un panier et crée les exemplaires correspondants
 * Ces exemplaires sont créés à partir d'un modèle déterminé via le champ 697$a. Si le 697$a vaut xxx, on appellera le plugin
 * $liste_plugins_crea_exemplaire["xxx"]
 * Par ailleurs, ce modèle poura être enrichi du contenu des ss-champs 697 qui seront passés en param au plugin via $param_modele
 * Il sera égalemetn enrichi d'infos extraites de la notice biblio (passées en param via $param_biblio)
 * Les informations sont extraites de la notice biblio par des plugins indiqués dans [liste_infos_biblio]. A chaque plugin est associé un mot clef
 * qui pourra être utilisé dans le plugin de formatage de l'exemplaire
 * 
**/
 

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

$nom_panier=$_REQUEST["nom_panier"];
$ID_commande=$_REQUEST["ID_commande"];

$liste_plugins_crea_exemplaire=$GLOBALS["affiche_page"]["parametres"]["liste_plugins_crea_exemplaire"];
$plugin_recherche_panier=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_panier"];
$liste_infos_biblio=$GLOBALS["affiche_page"]["parametres"]["liste_infos_biblio"];
$plugin_enregistre_exemplaire=$GLOBALS["affiche_page"]["parametres"]["plugin_enregistre_exemplaire"];
$plugin_maj_biblio=$GLOBALS["affiche_page"]["parametres"]["plugin_maj_biblio"];
$plugin_enregistre_biblio=$GLOBALS["affiche_page"]["parametres"]["plugin_enregistre_biblio"];
$plugin_maj_prix=$GLOBALS["affiche_page"]["parametres"]["plugin_maj_prix"];


$plugin_crea_exemplaire="";

// 1) On récupère la notice de commande
$cmde_xml=get_objet_xml_by_id("commande", $ID_commande);
$cmde_marcxml=new tvs_marcxml(array("type_obj"=>"commande", "ID"=>$ID_commande));
$cmde_marcxml->load_notice($cmde_xml);

// 1.bibs) on récupère 700$5 pour savoir si on a le droit de rajouter des notices
$champ_700=$cmde_marcxml->get_champ_unique("700", "");
$ss_champ_700_5=$cmde_marcxml->get_ss_champ_unique($champ_700, "5", "", "");
$etat_commande=$cmde_marcxml->get_valeur_ss_champ($ss_champ_700_5);
if ($etat_commande != "cours") {
    $retour["succes"]=0;
    $retour["erreur"]="Vous ne pouvez pas ajouter de notices a une commande deja validee";
    $output = $json->encode($retour);
    print($output);
    die("");
}

// 2) On récupère le champ 697
// $a => modèle exemplaire
// autres ss-champs => valeurs à insérer dans le modèle
$param_modele=array("ID_commande"=>$ID_commande, "notice_commande"=>$cmde_xml);
$modele="";
$tmp=$cmde_marcxml->get_champs("697", "");
$champ_697=$tmp[0];
$liste_ss_champs_997=$cmde_marcxml->get_ss_champs($champ_697, "", "", "");
foreach ($liste_ss_champs_997 as $ss_champ_997) {
    $code=$cmde_marcxml->get_nom_ss_champ($ss_champ_997);
    $valeur=$cmde_marcxml->get_valeur_ss_champ($ss_champ_997);
    if ($code=="a") {
        $plugin_crea_exemplaire=$liste_plugins_crea_exemplaire[$valeur];
    } else {
        $param_modele[$code]=$valeur;
    }
}


// 3) On récupère les notices du panier
$liste_notices=array();
$tmp=applique_plugin ($plugin_recherche_panier, array("nom_panier"=>$nom_panier));
if ($tmp["succes"] != 1) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}
$liste_notices=$tmp["resultat"]["notices"];


// 4) pour chaque notice du  panier...
foreach ($liste_notices as $notice_biblio) { // pour chaque notice biblio
    $ID_notice_biblio=$notice_biblio["ID"];
    $notice_xml_biblio=$notice_biblio["xml"];
    
    // 4.a) on récupère les infos intéressantes de la notice biblio (cote, prix...)
    $param_biblio=array();
    foreach ($liste_infos_biblio as $code_info_biblio => $plugin_info_biblio) {
        $tmp=applique_plugin ($plugin_info_biblio, array("notice"=>$notice_xml_biblio));
        if ($tmp["succes"] != 1) {
            $param_biblio[$code_info_biblio]="erreur";
        } else {
            $param_biblio[$code_info_biblio]=$tmp["resultat"]["texte"];
        }
    }
    
    // 4.b) on génère une notice exemplaire d'après le modèle, en y intégrant les infos fournies
    //      dans le champ 697 du bon de commandes, et celles extraites de la notice biblio (cote, prix...) 
    $tmp=applique_plugin ($plugin_crea_exemplaire, array("param_modele"=>$param_modele, "param_biblio"=>$param_biblio));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $notice_exemplaire=$tmp["resultat"]["notice"];
    
    // 4.?) on maj les prix
    $tmp=applique_plugin ($plugin_maj_prix, array("notice"=>$notice_exemplaire));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $notice_exemplaire=$tmp["resultat"]["notice"];
    
    // 4.c) On enregistre la notice exemplaire (et on récupère un ID)
    $tmp=applique_plugin ($plugin_enregistre_exemplaire, array("notice"=>$notice_exemplaire));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $ID_notice_exemplaire=$tmp["resultat"]["ID_notice"];
    
    // 4.d) On maj la notice biblio (en ajoutant l'exemplaire)
    $tmp=applique_plugin ($plugin_maj_biblio, array("notice"=>$notice_xml_biblio, "ID_exemplaire"=>$ID_notice_exemplaire, "notice_exemplaire"=>$notice_exemplaire));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $notice_xml_biblio2=$tmp["resultat"]["notice"];
    
    // 4.e) On enregistre la notice biblio
    $tmp=applique_plugin ($plugin_enregistre_biblio, array("notice"=>$notice_xml_biblio2, "ID_notice"=>$ID_notice_biblio));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }



} // fin du pour chaque notice biblio









$output = $json->encode($retour);
print($output);
?>