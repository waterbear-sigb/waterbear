<?php

$retour=array();
$retour["succes"]=1;
$retour["resultat"]=array();
$retour["resultat"]["commandes"]=array();
$retour["resultat"]["messages"]=array();

// variables passées en paramètre
$ID_pret=$_REQUEST["ID_pret"]; // si on prolonge un prêt en particulier
$ID_lecteur=$_REQUEST["ID_lecteur"]; // si on prolonge tous les prêts du lecteur
$bool_force=$_REQUEST["bool_force"]; // si on passe outre les messages

// Variables passées via le registre
$plugin_prolonger=$GLOBALS["affiche_page"]["parametres"]["plugin_prolonger"]; // plugin pour prolonger 1 notice
$plugin_formate_couleur=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_couleur"]; // plugin formater la couleur de la date der retour
$plugin_get_prets_lecteur=$GLOBALS["affiche_page"]["parametres"]["plugin_get_prets_lecteur"]; // récupérer tous les prêts en cours du lecteur

$code_message=$GLOBALS["affiche_page"]["parametres"]["code_message"];


if ($ID_pret != "") { // prolonger un prêt en particulier
    $tmp=applique_plugin($plugin_prolonger, array("ID_pret"=>$ID_pret, "bool_force"=>$bool_force));
    if ($tmp["succes"] != 1) {
        array_push($retour["resultat"]["messages"], $tmp["erreur"]);
        $output = $json->encode($retour);
        print($output);
    }
    if ($tmp["resultat"]["message"] != "") {
        array_push($retour["resultat"]["messages"], array("code"=>$code_message, "message"=>$tmp["resultat"]["message"]));
    } else {
        $date_retour=$tmp["resultat"]["date_retour"];
        $nb_prolongations=$tmp["resultat"]["nb_prolongations"];
        $tmp=applique_plugin($plugin_formate_couleur, array("date"=>$date_retour));
        if ($tmp["succes"] == 1) {
            $date_retour=$tmp["resultat"]["chaine"];
        }
        array_push($retour["resultat"]["commandes"], array("methode"=>"maj_date_retour", "parametres"=>array("id_pret"=>$ID_pret, "date_retour"=>$date_retour, "nb_prolongations"=>$nb_prolongations)));
    }
} elseif ($ID_lecteur != "") { // Prolonger tous les prêts du lecteur
    $nb_messages=0;
    $tmp=applique_plugin($plugin_get_prets_lecteur, array("ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"] != 1) {
        array_push($retour["resultat"]["messages"], $tmp["erreur"]);
        $output = $json->encode($retour);
        print($output);
    }
    foreach ($tmp["resultat"]["notices"] as $pret) {
        $ID_pret=$pret["ID"];
        $tmp=applique_plugin($plugin_prolonger, array("ID_pret"=>$ID_pret, "bool_force"=>"0"));
        if ($tmp["succes"] != 1) {
            array_push($retour["resultat"]["messages"], $tmp["erreur"]);
            $output = $json->encode($retour);
            print($output);
        }
        if ($tmp["resultat"]["message"] != "") {
            $nb_messages++;
        } else {
            $date_retour=$tmp["resultat"]["date_retour"];
            $nb_prolongations=$tmp["resultat"]["nb_prolongations"];
            $tmp=applique_plugin($plugin_formate_couleur, array("date"=>$date_retour));
            if ($tmp["succes"] == 1) {
                $date_retour=$tmp["resultat"]["chaine"];
            }
            array_push($retour["resultat"]["commandes"], array("methode"=>"maj_date_retour", "parametres"=>array("id_pret"=>$ID_pret, "date_retour"=>$date_retour, "nb_prolongations"=>$nb_prolongations)));
        }
        
    }
}






$output = $json->encode($retour);
print($output);
?>