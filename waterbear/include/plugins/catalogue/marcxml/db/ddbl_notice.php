<?php

/**
 * plugin_catalogue_marcxml_db_ddbl_notice()
 * 
 * @param mixed $parametres
 * @param [notice] => la notice en XML
 * @param [ddbl][0,1,2...][plugin_formate] => le plugin utilis� pour g�n�rer une chaine de d�doublonnage � partir de la notice
 * @param [ddbl][0,1,2...][plugin_recherche] => le plugin utilis� pour rechercher un doublon � partir de la chaine trouv�e
 * @param [ddbl][0,1,2...][politique_ddbl] => politique � appliquer pour ce crit�re de d�doublonnage parmi les suivants :
 * 
 * politique_ddbl : 
 * > arreter_si_non_trouve : si la chaine n'�tait pas vide, mais on ne trouve aucun doublon, on arr�te (ex. ISBN)
 * > continuer_si_non_trouve (defaut) : si la chaine n'�tait pas vide, mais on ne trouve aucun doublon, on passe au crit�re suivant (ex. n� de notice d'origine)
 * 
 * @return array 
 * @return [notice] => la notice XML trouv�e (ou vide si rien trouv�)
 * @return [ID_notice] => ID de la notice trouv�e ou 0
 */
function plugin_catalogue_marcxml_db_ddbl_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["notice"]="";
    $retour["resultat"]["ID_notice"]=0;
    
    $ddbl=$parametres["ddbl"];
    $notice=$parametres["notice"];
    
    
    foreach ($ddbl as $clef) {
        $plugin_formate=$clef["plugin_formate"];
        $plugin_recherche=$clef["plugin_recherche"];
        $politique_ddbl=$clef["politique_ddbl"];
        
        // On r�cup�re la chaine de d�doublonnage
        $tmp=applique_plugin ($plugin_formate, array("notice"=>$notice));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $chaine=$tmp["resultat"]["texte"];
        
        // si chaine vide, on passe au crit�re suivant
        if ($chaine == "") {
            continue;
        }
        
        // On lance la recherche
        $tmp=applique_plugin($plugin_recherche, array("query"=>$chaine)); // Il faudra utiliser un alias
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        // si doublon trouv�
        if ($tmp["resultat"]["nb_notices"] > 0) {
            $retour["resultat"]["notice"]=$tmp["resultat"]["notices"][0]["xml"];
            $retour["resultat"]["ID_notice"]=$tmp["resultat"]["notices"][0]["ID"];
            return ($retour);
        }
        // si pas de doublon
        if ($politique_ddbl == "arreter_si_non_trouve") {
            return ($retour);
        } else {
            continue;
        }
        
        
        
    } // fin du pour chaque crit�re

    return ($retour);
}


?>