<?php
/**
 * 
 * Ce WS sert à réceptionner un bon de commande
 * Il commence par tester le acb envoyé pour déterminer si c'est un ISBN ou un cab document
 * dans le 1er cas, on se contente d'afficher la ligne de commande (et de l'enregistrer dans l'opéraion)
 * 
 * Si on fournit un cab document, on fait la réception en suivant les opérations suivantes (utilisant de nombreux plusgins, cf ci-dessous)
 * > vérifier s'il reste des lignes à recevoir
 * > vérifier que le cab n'est pas utilisé
 * > dupliquer la ligne de commande en exemplaire
 * > modifier cet exemplaire (ajout du cab, modif du statut...) et l'enregistrer
 * > Modifier la notice biblio (ajout de l'exemplaire) et l'enregistrer
 * > Modifier la ligne de commande (modif des quantités / engagement / facturés) et enregistrer
 * > idem pour la commande
 * > retourner les infos formatées
 * 
 * 
 * 
**/
$action=$_REQUEST["action"];
$cab=$_REQUEST["cab"];
$ID_commande=$_REQUEST["ID_commande"];
//$_SESSION["operations"]["'.$ID_operation.'"];

$plugin_cab_2_infos=$GLOBALS["affiche_page"]["parametres"]["plugin_cab_2_infos"];
$plugin_recherche_exe_isbn=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_exe_isbn"];
$plugin_recherche_exe_ID=$GLOBALS["affiche_page"]["parametres"]["plugin_recherche_exe_ID"];
$plugin_formate_ligne_commande=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_ligne_commande"];
$plugin_formate_exe=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_exe"];
$plugin_ddbl_cab=$GLOBALS["affiche_page"]["parametres"]["plugin_ddbl_cab"];
$plugin_maj_exe=$GLOBALS["affiche_page"]["parametres"]["plugin_maj_exe"];
$plugin_maj_biblio=$GLOBALS["affiche_page"]["parametres"]["plugin_maj_biblio"];
$plugin_notice_2_db_exe=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_exe"];
$plugin_notice_2_db_biblio=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_biblio"];
$plugin_notice_2_db_ligne_commande=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_ligne_commande"];
$plugin_notice_2_db_commande=$GLOBALS["affiche_page"]["parametres"]["plugin_notice_2_db_commande"];

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// 1) On récupère le type de cab
if (substr($cab, 0, 3) == "ID:") {
    $type="ID_notice";
    $cab=str_replace("ID:", "", $cab);
} else {
    $tmp=applique_plugin ($plugin_cab_2_infos, array("cab"=>$cab));
    if ($tmp["succes"] != 1) {
        $retour["resultat"]["message"]="Type d'identifiant inconnu ($cab)";
        $output = $json->encode($retour);
        print($output);
        die("");
    } else {
        $type=$tmp["resultat"]["infos"]["type"];
    }
    if ($type != "biblio" AND $type != "exemplaire" AND $type != "ID_notice") {
        $retour["resultat"]["message"]="$cab est de type $type. Vous devez rentrer un ISBN ou un code barre exemplaire";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
}


$retour["resultat"]["texte"]=$_SESSION["operations"]["'.$ID_operation.'"]["texte"]; // par défaut, on ne change pas le texte

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 2) SI BIBLIO

if ($type == "biblio" OR $type=="ID_notice") {
    
    // RAZ affichage des exemplaires
    $_SESSION["operations"]["'.$ID_operation.'"]["texte_historique_exemplaires"]="";
    
    // 2.a) on récupère la notice exe
    if ($type == "biblio") { // ISBN
        $tmp=applique_plugin($plugin_recherche_exe_isbn, array("ID_commande"=>$ID_commande, "ISBN"=>$cab));
    } else { // ID notice
        $tmp=applique_plugin($plugin_recherche_exe_ID, array("ID_commande"=>$ID_commande, "ID_notice"=>$cab));
    }
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $nb_notices=$tmp["resultat"]["nb_notices"];
    if ($nb_notices == 0) {
        $retour["resultat"]["message"]="Aucun document ayant l'identifiant $type = $cab n'est present dans la commande $ID_commande";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    if ($nb_notices > 1) {
        $retour["resultat"]["message"]="Plusieurs lignes de la commande $ID_commande portent l'identifiant $type = $cab";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    // 2.b) On enregistre ID_ligne_commande dans l'opération
    $_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"]=$tmp["resultat"]["notices"][0]["ID"];
    $notice_ligne_commande=$tmp["resultat"]["notices"][0];
    
    // 2.c) On formate
    
    $retour["resultat"]["texte"]=page_bib_ws_catalogue_commandes_reception_commande_formate_notice ($plugin_formate_ligne_commande, $notice_ligne_commande);
    $_SESSION["operations"]["'.$ID_operation.'"]["texte"]=$retour["resultat"]["texte"];
    
    // 2.d) on récupère ID_biblio
    $tmp=get_objets_xml_lies("biblio", "implicite", "", $_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"], "exemplaire", 0);
    if (count($tmp) != 1) {
        $retour["resultat"]["message"]="Aucune notice bibliographique ne semble associee a la ligne de commande ".$_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"];
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    $_SESSION["operations"]["'.$ID_operation.'"]["ID_biblio"]=$tmp[0]["ID"];


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 2) SI EXEMPLAIRES

} elseif ($type == "exemplaire") {
    
    // 1) Récupérer notice ligne de commande
    $notice_ligne_commande=get_objet_xml_by_id("exemplaire",  $_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"]);
    $notice_ligne_commande_marcxml=new tvs_marcxml(array("type_obj"=>"exemplaire", "ID"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"]));
    $notice_ligne_commande_marcxml->load_notice($notice_ligne_commande);
    
    // 2) Récupérer champ 500
    $champ_500=$notice_ligne_commande_marcxml->get_champ_unique("500", "");
    if ($champ_500=="") {
        $retour["resultat"]["message"]="Vous ne pouvez pas exemplariser de documents a partir de cette ligne de commande, car elle ne comporte pas de champ 500";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    $ss_champ_500_f=$notice_ligne_commande_marcxml->get_ss_champ_unique($champ_500, "f", "", "0");
    $ss_champ_500_l=$notice_ligne_commande_marcxml->get_ss_champ_unique($champ_500, "l", "", "0");
    $ss_champ_500_m=$notice_ligne_commande_marcxml->get_ss_champ_unique($champ_500, "m", "", "0");
    $ss_champ_500_n=$notice_ligne_commande_marcxml->get_ss_champ_unique($champ_500, "n", "", "0");
    $ss_champ_500_o=$notice_ligne_commande_marcxml->get_ss_champ_unique($champ_500, "o", "", "0");
    
    $prix=$notice_ligne_commande_marcxml->get_valeur_ss_champ($ss_champ_500_f);
    $engage=$notice_ligne_commande_marcxml->get_valeur_ss_champ($ss_champ_500_l);
    $facture=$notice_ligne_commande_marcxml->get_valeur_ss_champ($ss_champ_500_m);
    $reste_a_recevoir=$notice_ligne_commande_marcxml->get_valeur_ss_champ($ss_champ_500_n);
    $recu=$notice_ligne_commande_marcxml->get_valeur_ss_champ($ss_champ_500_o);
    
    // 3) Vérifier s'il reste des lignes à recevoir
    if ($reste_a_recevoir == 0) {
        $retour["resultat"]["message"]="Tout a ete recu";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    // 4) Vérifier si le cab n'a pas déjà été utilisé
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
    
    // 5) On clone la notice ligne de commande
    $notice_exemplaire=$notice_ligne_commande->cloneNode(true);
    
    // 6) On modifie la notice exemplaire (enlève champ 500, ajoute cab)
    $tmp=applique_plugin($plugin_maj_exe, array("notice"=>$notice_exemplaire, "cab"=>$cab));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    
    // 7) On enregistre la notice exemplaire et on récupère ID_notice_exemplaire
    $tmp=applique_plugin($plugin_notice_2_db_exe, array("notice"=>$notice_exemplaire));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $ID_notice_exemplaire=$tmp["resultat"]["ID_notice"];
    
    // 8) On récupère la notice biblio
    $notice_biblio=get_objet_xml_by_id("biblio", $_SESSION["operations"]["'.$ID_operation.'"]["ID_biblio"]);
    
    // 9) MAJ notice biblio (essentiellement, rajouter le champ 997 de l'exemplaire)
    $tmp=applique_plugin($plugin_maj_biblio, array("notice"=>$notice_biblio, "ID_exemplaire"=>$ID_notice_exemplaire, "notice_exemplaire"=>$notice_exemplaire));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    
    // 10) On enregistre la notice biblio dans la DB
    $tmp=applique_plugin($plugin_notice_2_db_biblio, array("notice"=>$notice_biblio, "ID_notice"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_biblio"]));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    
    // 11) On modifie la ligne de commande
    $notice_ligne_commande_marcxml->update_ss_champ($ss_champ_500_l, $engage-$prix);
    $notice_ligne_commande_marcxml->update_ss_champ($ss_champ_500_m, $facture+$prix);
    $notice_ligne_commande_marcxml->update_ss_champ($ss_champ_500_n, $reste_a_recevoir-1);
    $notice_ligne_commande_marcxml->update_ss_champ($ss_champ_500_o, $recu+1);
    
    // 12) On enregistre la ligne de commande
    $tmp=applique_plugin($plugin_notice_2_db_ligne_commande, array("notice"=>$notice_ligne_commande_marcxml->notice, "ID_notice"=>$_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"]));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    
    // 13) On formate l'exemplaire et la ligne de commande' pour affichage
    $notice_exemplaire_ligne=get_objet_by_id("exemplaire", $ID_notice_exemplaire);
    $notice_exemplaire=get_objet_xml_by_id("exemplaire", $ID_notice_exemplaire); // On RE récupère la notice exemplaire, car Sinon, le champ 000 n'est pas maj et on a l'ID de la ligne de cmde
    $notice_exemplaire_ligne["xml"]=$notice_exemplaire;
    $texte_exe=page_bib_ws_catalogue_commandes_reception_commande_formate_notice ($plugin_formate_exe, $notice_exemplaire_ligne);
    $_SESSION["operations"]["'.$ID_operation.'"]["texte_historique_exemplaires"].="<br/>".$texte_exe;
    
    $notice_ligne_commande_ligne=get_objet_by_id("exemplaire", $_SESSION["operations"]["'.$ID_operation.'"]["ID_ligne_commande"]);
    $notice_ligne_commande_ligne["xml"]=$notice_ligne_commande_marcxml->notice;
    $texte_ligne_commande=page_bib_ws_catalogue_commandes_reception_commande_formate_notice ($plugin_formate_ligne_commande, $notice_ligne_commande_ligne);
    
    // TMP !!!!
    $retour["resultat"]["texte"]=$texte_ligne_commande."<br><br> ".$_SESSION["operations"]["'.$ID_operation.'"]["texte_historique_exemplaires"];
    $_SESSION["operations"]["'.$ID_operation.'"]["texte"]=$retour["resultat"]["texte"];

    // 14) On récupère et maj la notice de commande
    $notice_commande=get_objet_xml_by_id("commande", $ID_commande);
    $notice_commande_marcxml=new tvs_marcxml(array("type_obj"=>"commande", "ID"=>$ID_commande));
    $notice_commande_marcxml->load_notice($notice_commande);
    
    $champ_700=$notice_commande_marcxml->get_champ_unique("700", "");
    if ($champ_700=="") {
        $retour["resultat"]["message"]="Pas de champ 700 dans la notice de commande : mise à jour du budget impossible";
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    
    $ss_champ_700_b=$notice_commande_marcxml->get_ss_champ_unique($champ_700, "b", "", "0");
    $ss_champ_700_c=$notice_commande_marcxml->get_ss_champ_unique($champ_700, "c", "", "0");
    $ss_champ_700_e=$notice_commande_marcxml->get_ss_champ_unique($champ_700, "e", "", "0");
    $ss_champ_700_f=$notice_commande_marcxml->get_ss_champ_unique($champ_700, "f", "", "0");
    
    $cmde_engage=$notice_commande_marcxml->get_valeur_ss_champ($ss_champ_700_b);
    $cmde_facture=$notice_commande_marcxml->get_valeur_ss_champ($ss_champ_700_c);
    $cmde_reste_a_recevoir=$notice_commande_marcxml->get_valeur_ss_champ($ss_champ_700_e);
    $cmde_recu=$notice_commande_marcxml->get_valeur_ss_champ($ss_champ_700_f);

    $notice_commande_marcxml->update_ss_champ($ss_champ_700_b, $cmde_engage-$prix);
    $notice_commande_marcxml->update_ss_champ($ss_champ_700_c, $cmde_facture+$prix);
    $notice_commande_marcxml->update_ss_champ($ss_champ_700_e, $cmde_reste_a_recevoir-1);
    $notice_commande_marcxml->update_ss_champ($ss_champ_700_f, $cmde_recu+1);
    
    // 15) on enregistre la notice de commande
    $tmp=applique_plugin($plugin_notice_2_db_commande, array("notice"=>$notice_commande_marcxml->notice, "ID_notice"=>$ID_commande));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$output = $json->encode($retour);
print($output);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function page_bib_ws_catalogue_commandes_reception_commande_formate_notice ($plugin_formatage, $ligne) {
    
    $tmp=applique_plugin($plugin_formatage, array());
    $plugin_formate_notice=$tmp["resultat"]["plugin_formate_notice"];
    $plugin_formate_liste=$tmp["resultat"]["plugin_formate_liste"];
    
    $tmp=applique_plugin($plugin_formate_notice, array("ligne"=>$ligne));
    if ($tmp["succes"]==0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $tmp_chaine=$tmp["resultat"];
    
    if (is_array($plugin_formate_liste)) {
        $tmp_tableau=array();
        $tmp_tableau[0]=$tmp_chaine;
        $tmp=applique_plugin($plugin_formate_liste, array("tableau"=>$tmp_tableau));
        if ($tmp["succes"]==0) {
            $output = $json->encode($tmp);
            print($output);
            die("");
        }
    }
    return ($tmp["resultat"]);
}

?>