<?php

/**
 * plugin_transactions_bureau_cab_2_infos()
 * 
 * Ce plugin retourne des infos liés à un format de code barre
 * En fonction de la longueur du cab et de son préfixe (+ autres paramètres à venir ?)
 * le plugin retourne des infos dans [infos]
 * L'info principale est le type de cab (lecteur, exemplaire...)
 * mais on peut envisager d'autres infos (bibliothèque du lecteur ou du doc...)
 * 
 * @param mixed $parametres
 * @param [cab] => le code barre à évaluer
 * @param [liste][0,1,2...][longueur|prefixe] => infos pour évaleur le cab (longueur, préfixe...) 
 * @param [liste][0,1,2...][infos][type|????] => infos à retourner sur ce type de cab : type d'objet (lecteur, exemplaire...) + autres infos ? 
 * 
 * @return [infos][type|....] => les infos sur ce code barre
 * 
 */
function plugin_transactions_bureau_cab_2_infos ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["infos"]="";
    
    $cab=$parametres["cab"];
    $long_cab=strlen($cab);
    
    foreach ($parametres["liste"] as $param) {
        // 1) On teste la longueur
        if ($param["longueur"] != $long_cab) {
            continue;
        }
        
        // 2) on teste le préfixe
        if ($param["prefixe"] != "") {
            $long_prefixe=strlen($param["prefixe"]);
            if (substr($cab, 0, $long_prefixe) != $param["prefixe"]) {
                continue;
            }
        }
        
        $retour["resultat"]["infos"]=$param["infos"];
        break;
    }
    
    if ($retour["resultat"]["infos"] == "") {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("bib/transactions/prets/standard", "type_cab_inconnu", array("cab"=>$cab));
    }
    
    
    
    return ($retour);
}


?>