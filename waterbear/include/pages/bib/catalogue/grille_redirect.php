<?php
/**
 * 
 * Cette page permet de rediriger vers une grille et un masque donn�s en fonction de certaines caratc�ristiques de la notice
 * 
 * param�tres URL : ID_notice
 * param�tres registre :
 * type_obj => type de l'objet
 * plugin_formate => plugin utilis� pour r�cup�rer l'info
 * choix [val1, val2, val3,..., _else][grille|masque OU plugin_formate|choix] : liste des valeurs possibles. A chaque valeur on associe SOIT
 *       une grille et un masque SOIT (de mani�re r�cursive) une nouvelle valeur � tester. la Clef _else permet de mettre une valeur par d�faut 
 * 
 * */


$plugin_formate=$GLOBALS["affiche_page"]["parametres"]["plugin_formate"];
$choix=$GLOBALS["affiche_page"]["parametres"]["choix"];
$type_obj=$GLOBALS["affiche_page"]["parametres"]["type_obj"];
$ID_notice=$_REQUEST["ID_notice"];



// 1) on r�cup�re la notice
$notice=get_objet_xml_by_id($type_obj, $ID_notice);
if ($notice == "") {
    $erreur="la notice de type $type_obj ayant l'ID $ID_notice n'existe pas";
}

$URL_redirect=pages_bib_catalogue_redirect_analyse_choix($notice, $plugin_formate, $choix);
if (!is_array($URL_redirect)) {
    $erreur="impossible de determiner la grille a utiliser : $URL_redirect";
} 

$URL_redirect["erreur"]=$erreur;
$URL_redirect["ID_notice"]=$ID_notice;
$URL_redirect["id_appel"]=$_REQUEST["id_appel"];

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>$URL_redirect));
include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");






////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function pages_bib_catalogue_redirect_analyse_choix ($notice, $plugin_formate, $choix) {
    
    
    // 1) on formate la chaine
    $tmp=applique_plugin($plugin_formate, array("notice"=>$notice));
    if ($tmp["succes"] != 1) {
        return("Erreur lors du formatage : ".$tmp["erreur"]);
    }
    $chaine=$tmp["resultat"]["texte"];
    
    // on r�cup�re les valeurs correspondant � cette chaine
    if (isset($choix[$chaine])) {
        $resultat=$choix[$chaine];
    } elseif (isset($choix["_else"])) {
        $resultat=$choix["_else"];
    } else {
        return("aucune valeur parametree");
    }
    
    // on regarde s'il y a des sous-valeurs
    if (isset($resultat["plugin_formate"])) {
        $resultat2=pages_bib_catalogue_redirect_analyse_choix ($notice, $resultat["plugin_formate"], $resultat["choix"]);
        return($resultat2);
    } else {
        return ($resultat);
    }
}



?>