<?php

$retour=array();
$retour["succes"]=1;
$retour["erreur"]="";
$retour["resultat"]="";
$retour["resultat"]["liste"]=array();

$nom_liste=$_REQUEST["nom_liste"];
$langue=$_REQUEST["langue"];
$code=$_REQUEST["code"];
$valeur=$_REQUEST["valeur"];
$nouv_code=$_REQUEST["nouv_code"]; // pour add_elemetn et update_code

if ($langue == "") {
    $langue="_fr";
}

$registre=new tvs_registre();



// Actions possibles
try {
    $noeud_liste=$registre->get_node_by_chemin($nom_liste."/_intitules");
    $ID_liste=$noeud_liste["ID"];
    if ($code != "") {
        $noeud_code=$registre->get_node_by_nom($code, $ID_liste);
        $ID_code=$noeud_code["ID"];
    }
    if ($operation == "update_code") { ////////////////// MAJ CODE
        $noeud_code["nom"]=$nouv_code;
        $registre->niv2_update_node($noeud_code);
    } elseif ($operation == "update_valeur") { ////////// MAJ VALEUR
        $noeud_langue=$registre->get_node_by_nom($langue, $ID_code);
        $noeud_langue["valeur"]=$valeur;
        $registre->niv2_update_node($noeud_langue);
    } elseif ($operation == "delete_element") { ///////// DELETE ELEMENT
        $tmp=$registre->get_node_by_ID($ID_code);
        $chemin=$tmp["chemin"];
        $test=$registre->metawb_is_node_exportable($tmp["chemin"]);
        $registre->delete_tree($ID_code);
        if ($test == "mwb_export") {
            metawb_log_registre ("supprimer_noeud", $chemin, "", "", "");
        }
    } elseif ($operation == "add_element") { //////////// ADD ELEMENT
        $registre->create_node_chemin(array(), $nom_liste."/_intitules/".$code."/".$langue, $valeur, "");
    }
} catch (tvs_exception $e) {
    $retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
    $retour["succes"]=0;
    //$output = $json->encode($retour);
    //print($output);
}

// récupération de la liste des éléments
try {
    $liste1=$registre->get_enfants($ID_liste);
} catch (tvs_exception $e) {
    $retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
    $retour["succes"]=0;
    $output = $json->encode($retour);
    print($output);
}
foreach ($liste1 as $ligne) {
    $ID_ligne=$ligne["ID"];
    $code=$ligne["nom"];
    try {
        $noeud_langue=$registre->get_node_by_nom($langue, $ID_ligne);
    } catch (tvs_exception $e) {
        $noeud_langue["valeur"]="";
    }
    $valeur=$noeud_langue["valeur"];
    array_push($retour["resultat"]["liste"], array("code"=>$code, "valeur"=>$valeur));
}




$output = $json->encode($retour);
print($output);
?>