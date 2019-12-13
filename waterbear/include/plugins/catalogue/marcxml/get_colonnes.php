<?php

/**
 * plugin_catalogue_marcxml_get_colonnes()
 * 
 * Ce plugin permet de mettre en forme des informations fournies sous forme de tableau. Il est typiquement utilisé pour
 * mettre en forme des infos extraites d'une base de données (ligne de résultat) en particulier les tables obj_xxx_acces
 * 
 * @param array $parametres
 * @param SOIT [tableau] => le tableau contenant les colonnes à formater
 * @param SOIT [ID_objet] et [type_obj] => si on veut récupérer le tableau dans la DB
 * @param [colonnes][0,1,2...][nom_colonne | avant | apres | avant_verif] => infos contenant le formatage
 * 
 * 
 * @return [texte] => le texte formaté
 */
function plugin_catalogue_marcxml_get_colonnes ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $tableau=$parametres["tableau"];
    $ID_objet=$parametres["ID_objet"];
    $type_obj=$parametres["type_obj"];
    
    if ($ID_objet != "" AND $type_obj != "") {
        $tableau=get_objet_by_id($type_obj, $ID_objet);
    }
  
    $chaine="";
  
    foreach ($parametres["colonnes"] as $colonne) {

        $element="";
        $nom_colonne=$colonne["nom_colonne"];
        $avant=$colonne["avant"];
        $apres=$colonne["apres"];
        $avant_verif=$colonne["avant_verif"];
        if (! isset ($tableau[$nom_colonne])) {
            $element="";
        } else {
            $element=$tableau[$nom_colonne];
        }
        if ($element !== "") {
            $element=$avant.$element.$apres;
            if ($chaine !== "") {
                $element=$avant_verif.$element;
            }
        }
        $chaine.=$element;
    }
    
    $retour["resultat"]["texte"]=$chaine;
    
    return ($retour);
}



?>