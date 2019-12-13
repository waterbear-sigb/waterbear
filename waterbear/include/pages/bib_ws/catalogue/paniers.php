<?php
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_paniers.php");

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

$type_objet=$_REQUEST["type_objet"];
$chemin_parent=$_REQUEST["chemin_parent"];
$type=$_REQUEST["type"];

$obj_paniers=new tvs_paniers();

if ($operation == "create_node") { // Créer un nouveau noeud vide (panier ou répertoire)
    // 1) on crée le noeud
    $tmp=$obj_paniers->create_node(array("type_obj"=>$type_objet, "chemin_parent"=>$chemin_parent, "type"=>$type));
    if ($tmp["succes"] != 1) {
        $retour=$tmp;
        $output = $json->encode($retour);
        print($output);
        die("");
    }
    $ID=$tmp["resultat"]["ID"];
    
    // 2) on récupère le contenu du répertoire
    $tmp=$obj_paniers->get_contenu_repertoire($chemin_parent, $type_objet);
    
    // 3) retour
    $retour["resultat"]["liste"]=$tmp;
    $retour["resultat"]["ID"]=$ID;
    $retour["resultat"]["chemin_parent"]=$chemin_parent;
} elseif ($operation == "get_liste") {
    $tmp=$obj_paniers->get_contenu_repertoire($chemin_parent, $type_objet);
    $retour["resultat"]["liste"]=$tmp;
} elseif ($operation == "get_panier_by_ID") {
    $ID=$_REQUEST["ID"];
    $tmp=$obj_paniers->get_panier_by_ID($ID);
    $retour["resultat"]=$tmp;
}elseif ($operation == "save") {
    $ID=$_REQUEST["ID"];
    $nom=$_REQUEST["nom"];
    $description=$_REQUEST["description"];
    $tmp=$obj_paniers->save($ID, $nom, $description);
    $retour=$tmp;
} elseif ($operation == "delete_panier") {
    $ID=$_REQUEST["ID"];
    $retour=$obj_paniers->delete_panier($ID);
} elseif ($operation == "add_dynamique") {
    $crea_panier=$_REQUEST["crea_panier"];
    $ID=$_REQUEST["ID"];
    if ($crea_panier == 1) {
        $tmp=$obj_paniers->panier_auto(array("type_obj"=>$type_objet, "chemin_parent"=>"system/recherches", "type"=>"dynamique"));
        $ID=$tmp["resultat"]["ID"];
    }
    
    $contenu=$_REQUEST["contenu"];
    $contenu_stat=$_REQUEST["contenu_stat"];
    if ($contenu_stat == "") {
        $contenu_stat=array();
    }
    $contenu_array=$json->decode($contenu);
    $contenu_stat_array=$json->decode($contenu_stat);
    $tmp_array=array("recherchator"=>$contenu_array, "statator"=>$contenu_stat_array);
    $tmp_str=$json->encode($tmp_array);
    $retour=$obj_paniers->add_dynamique($ID, $tmp_str);
    if ($crea_panier == 1) {
        $retour["resultat"]["nom"]=$tmp["resultat"]["nom"];
        $retour["resultat"]["ID"]=$tmp["resultat"]["ID"];
        $retour["resultat"]["chemin_parent"]=$tmp["resultat"]["chemin_parent"];
    }
} elseif ($operation == "add_statique") {
    $crea_panier=$_REQUEST["crea_panier"];
    $ID=$_REQUEST["ID"];

    if ($crea_panier == 1) {
        $tmp=$obj_paniers->panier_auto(array("type_obj"=>$type_objet, "chemin_parent"=>"system/recherches", "type"=>"statique"));
        $ID=$tmp["resultat"]["ID"];
    }
    
    $contenu=$_REQUEST["contenu"];
    $retour=$obj_paniers->add_statique($ID, $contenu);
    if ($crea_panier == 1) {
        $retour["resultat"]["nom"]=$tmp["resultat"]["nom"];
        $retour["resultat"]["ID"]=$tmp["resultat"]["ID"];
        $retour["resultat"]["chemin_parent"]=$tmp["resultat"]["chemin_parent"];
    }
} elseif ($operation == "remove_statique") {
    $ID=$_REQUEST["ID"];
    $contenu=$_REQUEST["contenu"];
    $retour=$obj_paniers->remove_statique($ID, $contenu);
} elseif ($operation == "copie_panier") {
    $ID=$_REQUEST["ID"];
    $chemin_dest=$_REQUEST["chemin_dest"];
    $bool_suppr=$_REQUEST["bool_suppr"];
    $retour=$obj_paniers->copie_panier($ID, $chemin_dest, $bool_suppr);
} else {
    
}


$output = $json->encode($retour);
print($output);
?>