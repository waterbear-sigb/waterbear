<?php

$retour=array();
$retour["succes"]=1;
$retour["erreur"]="";
$retour["resultat"]="";
$retour["resultat"]["liste"]=array();

$nom_liste=$_REQUEST["nom_liste"];
$nom_liste_codes=$_REQUEST["nom_liste_codes"];
$code=$_REQUEST["code"];
$valeur=$_REQUEST["valeur"];
$nouv_code=$_REQUEST["nouv_code"]; // pour add_elemetn et update_code


$registre=new tvs_registre();



// Actions possibles

try {
    $noeud_liste=$registre->get_node_by_chemin($nom_liste."/liste_codes");
    $ID_liste=$noeud_liste["ID"];
    
    $noeud_code_defaut=$registre->get_node_by_chemin($nom_liste."/defaut_code");
    $noeud_decode_defaut=$registre->get_node_by_chemin($nom_liste."/defaut_decode");
    
    
    if ($code != "") {
        $noeud_code=$registre->get_node_by_nom($code, $ID_liste);
        $ID_code=$noeud_code["ID"];
    }
    if ($operation == "update_code") { ////////////////// MAJ CODE
        $noeud_code["nom"]=$nouv_code;
        $registre->niv2_update_node($noeud_code);
    } elseif ($operation == "update_valeur") { ////////// MAJ VALEUR
        $noeud_code["valeur"]=$valeur;
        $registre->niv2_update_node($noeud_code);
    } elseif ($operation == "delete_element") { ///////// DELETE ELEMENT
        $tmp=$registre->get_node_by_ID($ID_code);
        $chemin=$tmp["chemin"];
        $test=$registre->metawb_is_node_exportable($tmp["chemin"]);
        $registre->delete_tree($ID_code);
        if ($test == "mwb_export") {
            metawb_log_registre ("supprimer_noeud", $chemin, "", "", "");
        }
    } elseif ($operation == "add_element") { //////////// ADD ELEMENT
        $registre->create_node_chemin(array(), $nom_liste."/liste_codes/".$code, $valeur, "");
    } elseif ($operation == "update_code_defaut") {
        $noeud_code_defaut["valeur"]=$valeur;
        $registre->niv2_update_node($noeud_code_defaut);
    } elseif ($operation == "update_decode_defaut") {
        $noeud_decode_defaut["valeur"]=$valeur;
        $registre->niv2_update_node($noeud_decode_defaut);
    }
} catch (tvs_exception $e) {
    $retour["erreur"]=utf8_encode(get_exception($e->get_infos()));
    $retour["succes"]=0;
    //$output = $json->encode($retour);
    //print($output);
}


// r�cup�ration de la liste des �l�ments
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
    $valeur=$ligne["valeur"];
    array_push($retour["resultat"]["liste"], array("code"=>$code, "valeur"=>$valeur));
}

// liste des codes
$liste_codes=array();
$tmp=applique_plugin(array("nom_plugin"=>"div/get_liste_choix"), array("nom_liste"=>$nom_liste_codes));
if ($tmp["succes"]!==1) {
    $retour["erreur"]="impossible de recuperer la liste $nom_liste_codes";
    $retour["succes"]=0;
    $output = $json->encode($retour);
    print($output);
} 
$liste_codes=$tmp["resultat"];

$retour["resultat"]["liste_codes"]=$liste_codes;

// valeurs par défaut
$code_defaut=$noeud_code_defaut["valeur"];
$decode_defaut=$noeud_decode_defaut["valeur"];
$retour["resultat"]["code_defaut"]=$code_defaut;
$retour["resultat"]["decode_defaut"]=$decode_defaut;



$output = $json->encode($retour);
print($output);
?>