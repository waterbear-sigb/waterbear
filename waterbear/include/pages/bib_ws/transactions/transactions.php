<?PHP

// variables passées en paramètre
$cab=$_REQUEST["cab"]; // action effectuée sur un élément du formulaire
$validation_message=$_REQUEST["validation_message"]; // oui => accepter, non => refuser
$mode=$_REQUEST["mode"]; // pret | retour
$cab=trim($cab);

// Variables passées via le registre
$plugin_main=$GLOBALS["affiche_page"]["parametres"]["plugin_main"]; // plugin à utiliser
$plugin_id_2_cab=$GLOBALS["affiche_page"]["parametres"]["plugin_id_2_cab"]; // pour convertir un id de notice lecteur en cab

// Conversion de id_notice en cab si nécessaire
if (substr($cab, 0, 3)=="id:") {
    $cab=substr($cab, 3);
    $tmp=applique_plugin($plugin_id_2_cab, array("query"=>$cab));
    if ($tmp["succes"] == 0) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $cab=$tmp["resultat"]["ID"];
    $cab=trim($cab);
    
}


// On place sur le bureau les informations de base
$_SESSION["operations"][$ID_operation]["bureau"]["param_script"]=array(); // RAZ : ce sont des infos propres à chaque appel
$_SESSION["operations"][$ID_operation]["bureau"]["param_script"]["cab"]=$cab;
$_SESSION["operations"][$ID_operation]["bureau"]["param_script"]["validation_message"]=$validation_message;
$_SESSION["operations"][$ID_operation]["bureau"]["param_script"]["mode"]=$mode;
$_SESSION["operations"][$ID_operation]["bureau"]["commandes"]=array();
$_SESSION["operations"][$ID_operation]["bureau"]["messages"]=array();
$_SESSION["operations"][$ID_operation]["bureau"]["niveau_message"]=""; // on RAZ niveau message à chaque fois

// Debug
if ($operation == "debug") {
    print_r($_SESSION["operations"][$ID_operation]["bureau"]);
    die("");
}


$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";




$tmp=applique_plugin($plugin_main, array("bureau"=>$_SESSION["operations"][$ID_operation]["bureau"]));
if ($tmp["succes"] == 0) {
    $output = $json->encode($tmp);
    print($output);
    die("");
}

//dbg_log (var_export ($tmp["resultat"]["bureau"], true)); //TMP !!!!!!!


$_SESSION["operations"][$ID_operation]["bureau"]=$tmp["resultat"]["bureau"];

$retour=array();
$retour["succes"]=1;
$retour["resultat"]=array();
$retour["resultat"]["commandes"]=$tmp["resultat"]["bureau"]["commandes"];
$retour["resultat"]["messages"]=$tmp["resultat"]["bureau"]["messages"];
$retour["resultat"]["arbre"]=$tmp["resultat"]["bureau"]["arbre"];

$output = $json->encode($retour);
print($output);

?>