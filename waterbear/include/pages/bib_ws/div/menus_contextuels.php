<?php
$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

$ID=$_REQUEST["ID"];
$type_obj=$_REQUEST["type_obj"];
$contexte=$_REQUEST["contexte"];
$idx=$_REQUEST["idx"];

if ($opertaion="get_menu_contextuel") {
    $plugin=array();
    $plugin["nom_plugin"]="div/menus_contextuels/$type_obj/$contexte";
    $plugin["parametres"]=array("type_obj"=>$type_obj, "ID"=>$ID, "contexte"=>$contexte, "idx"=>$idx);
    $tmp=applique_plugin($plugin, array());
    if ($tmp["succes"] != 1) {
        $plugin["nom_plugin"]="div/menus_contextuels/$type_obj/defaut";
        $plugin["parametres"]=array("type_obj"=>$type_obj, "ID"=>$ID, "contexte"=>"defaut", "idx"=>$idx);
        $tmp=applique_plugin($plugin, array());
    }
    $output = $json->encode($tmp);
    print($output);
    
}



?>