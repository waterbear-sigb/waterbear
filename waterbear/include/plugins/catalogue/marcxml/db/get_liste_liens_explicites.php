<?php


/**
 * plugin_catalogue_marcxml_db_get_liste_liens_explicites()
 * 
 * Ce plugin retourne la liste des notices liées à la notice fournie en paramètre
 * Pour chaque notice il indique le type d'objet, le type de lien et le n° de notice
 * Il utilise en paramètre un tableau contenant la liste des champs susceptibles de contenir un lien (+ d'autres infos)
 * De telle sorte qu'en jouant sur ce paramètre, on peut ne récupérer les notices liées à tels ou tels champs
 * 
 * 
 * @param array $parametres
 * @param [notice] => la notice XML
 * @param [champs_liens_explicites] => liste des champs susceptibles de contenir un lien avec les infos nécessaires pour récupérer le lien
 * @param [          """"         ][700,464,...][type | type_lien | ss_champ_jointure]
 *
 * @return array
 * @return [resultat][liens]
 */
function plugin_catalogue_marcxml_db_get_liste_liens_explicites ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["liens"]=array();
    
    //$parametres=plugins_2_param($parametres, array()); // TMP !!!!!!!!!
    $notice=$parametres["notice"];
    $liste_champs=$notice->getElementsByTagName("datafield");
    $nb_champs=$liste_champs->length;
    if ($nb_champs == 0) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/crea_notice", "contenu_vide");
    }
    $xpath=new DOMXPath ($notice);
    for ($i=0 ; $i<$nb_champs ; $i++) { // Pour chaque champ
        $champ=$liste_champs->item($i);
        $nom_champ=$champ->attributes->getNamedItem("tag")->value;
        foreach ($parametres["champs_liens_explicites"] as $champ_lien_explicite => $infos) { // on regarde chaque type de champ susceptible d'être un lien explicite
            $ss_champ_jointure=$infos["ss_champ_jointure"];
            $type=$infos["type"];
            $type_lien=$infos["type_lien"];
            
            if ($nom_champ == $champ_lien_explicite) { // Si ça correspond...
                // 1) on récupère l'ID de la notice liée
                $expression="subfield[@code='$ss_champ_jointure']";
                $liste_ss_champs=$xpath->query($expression, $champ); // liste des sous-champs de CE type
                $nb_ss_champs=$liste_ss_champs->length;
                if ($nb_ss_champs == 0) { // si pas de sous-champ de jointure
                    break;
                }
                $id_notice_jointure=$liste_ss_champs->item(0)->textContent;
                array_push ($retour["resultat"]["liens"], array("ID_lien"=>$id_notice_jointure, "type_objet"=>$type, "type_lien"=>$type_lien, "nom_champ"=>$nom_champ));
            }
        }
    }
  
    
    
    
    
    return ($retour);    
}



?>