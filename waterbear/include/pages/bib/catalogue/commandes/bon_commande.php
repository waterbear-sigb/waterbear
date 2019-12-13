<?php

$ID_commande=$_REQUEST["ID_commande"];

$bloc_lignes_commande="";
$bloc_fournisseur="";
$bloc_livraison="";
$bloc_somme="";

// lignes de commande
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_lignes_commande"], array("ID_commande"=>$ID_commande));
if ($tmp["succes"] != 1) {
    die ("plugin_get_lignes_commande : ".$tmp["erreur"]);
}
$bloc_lignes_commande=$tmp["resultat"]["notices"];

$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_totaux"], array("ID_commande"=>$ID_commande));
if ($tmp["succes"] != 1) {
    die ("plugin_totaux : ".$tmp["erreur"]);
}
$bloc_somme=$tmp["resultat"]["texte"];

// on récupère la notice de commande
$notice=get_objet_xml_by_id("commande", $ID_commande);

// bloc fournisseur
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_formate_fournisseur"], array("notice"=>$notice));
if ($tmp["succes"] != 1) {
    die ("plugin_formate_fournisseur : ".$tmp["erreur"]);
}
$bloc_fournisseur=$tmp["resultat"]["texte"];

// bloc livraison
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_formate_livraison"], array("notice"=>$notice));
if ($tmp["succes"] != 1) {
    die ("plugin_formate_livraison : ".$tmp["erreur"]);
}
$bloc_livraison=$tmp["resultat"]["texte"];

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("bloc_lignes_commande"=>$bloc_lignes_commande, "bloc_fournisseur"=>$bloc_fournisseur, "bloc_livraison"=>$bloc_livraison, "bloc_somme"=>$bloc_somme));
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");


?>