<?php

$_SESSION["registre"] = array(); // on RAZ le registre
$erreurs="";

// Structure des paramètres
$tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_get_structure_parametres"], array());
if ($tmp["succes"] != 1) {
    $erreurs.=$tmp["erreur"]."\\n";
}



foreach ($tmp["resultat"] as $idx_onglet =>$onglet) {
    foreach ($onglet["rubriques"] as $idx_rubrique => $rubrique) {
        $lien=$rubrique["lien"];
        try {
            $valeur=get_registre($lien);
        } catch (tvs_exception $e) {
            $valeur="#Erreur#";
        }
        $tmp["resultat"][$idx_onglet]["rubriques"][$idx_rubrique]["valeur"]=$valeur;
    }
}


$structure_parametres=str_replace ('\\', '\\\\', $json->encode($tmp["resultat"]));
$structure_parametres=str_replace ('"', '\"', $structure_parametres);

$erreurs=str_replace ('"', '\"', $erreurs);
affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_js"=>array("erreurs"=>$erreurs, "structure_parametres"=>$structure_parametres)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>