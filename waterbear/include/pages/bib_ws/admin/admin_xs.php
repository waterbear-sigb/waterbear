<?php

// on peut associer plusieurs clef à ce paramètre (un seul paramètre permet de modifier plusieurs endroits du registre qui doivent avoir la même valeur)
// Pour cela, il faut séparar les chemis des clefs par un retour à la ligne (les lignes vides sont ignorées pour + de lisibilité)

$_SESSION["registre"] = array(); // on RAZ le registre en session

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";

// variables
$valeur=$_REQUEST["valeur"];
$clef=$_REQUEST["clef"];
$autres_liens=$_REQUEST["autres_liens"];
if ($autres_liens == "undefined") {
    $autres_liens="";
}

$separateur="$$$$";

$clefs=explode($separateur, $autres_liens);
array_push($clefs, $clef);

if ($operation == "update_clef") {
    foreach ($clefs as $clef) {
        $clef=trim($clef);
        if ($clef == "") {
            continue;
        }
        
        $tmp=set_registre ($clef, $valeur, "");
        
        $retour["resultat"]="$clef => $valeur";
        if ($tmp !== true) {
            $retour["succes"]=0;
            $retour["erreur"]=$tmp;
        }
    }
    
}


$output = $json->encode($retour);
print($output);
?>