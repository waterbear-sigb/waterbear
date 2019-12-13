<?php
set_time_limit(600);
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/api_mel.php");
$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

$plugin_get_notice=$GLOBALS["affiche_page"]["parametres"]["plugin_get_notice"];
$plugin_marc2xml=$GLOBALS["affiche_page"]["parametres"]["plugin_marc2xml"];
$plugin_importe_notice=$GLOBALS["affiche_page"]["parametres"]["plugin_importe_notice"];
$chemin_panier=$GLOBALS["affiche_page"]["parametres"]["chemin_panier"];

$mel=new api_mel ();

$param=$mel->get_param_registre();
$mel->load_param_mel($param);

if ($operation == "get_liste_paniers") {
    $tmp=$mel->get_liste_paniers();
    $retour["resultat"]=$tmp;
} elseif ($operation == "importe_panier") {
    // 1) on exporte le panier
    $ID_panier=$_REQUEST["ID_panier"];
    $nom_panier=$_REQUEST["nom_panier"];
    $infos=$mel->exporte_panier($ID_panier);
    $chemin_distant=$infos["biblio"];
    
    $panier=$chemin_panier."/".$nom_panier;
    
    // 2) on copie le fichier de notices biblio en local
    $chemin_local=importe_fichier($chemin_distant);
    
    // 3) on importe les notices
    $taille_fichier=filesize($chemin_local);
    $handle=fopen($chemin_local, "r");
    $tmp=applique_plugin($plugin_get_notice, array("handle"=>$handle, "taille_fichier"=>$taille_fichier, "panier"=>$panier));
    $retour=$tmp;
        
    
} elseif ($operation == "get_url") {
    $mel_page=$_REQUEST["mel_page"];
    $url=$mel->get_url($mel_page);
    $retour["resultat"]["url"]=$url;
} elseif ($operation == "importe_notice_by_ean") {
    $notice_marc=$mel->get_notice_by_ean(array("EAN"=>$_REQUEST["EAN"]));
    //$notice_marc=$infos["notice"];
    if ($notice_marc=="") {
        $retour["erreur"]="Aucune notice trouvee";
        $retour["succes"]=0;
        $output = $json->encode($retour);
        print($output);
        die("");
    }
  
    // conversion de marc en marcxml
    $tmp=applique_plugin($plugin_marc2xml, array("notice"=>$notice_marc));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice_xml=$tmp["resultat"]["notice"];
   
    // intיgration dans la base
    $tmp=applique_plugin($plugin_importe_notice, array("notice"=>$notice_xml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $retour=$tmp;
    
   
    
}

$output = $json->encode($retour);
print($output);
?>