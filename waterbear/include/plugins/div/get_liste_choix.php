<?php

/**
 * plugin_div_get_liste_choix()
 * 
 * Ce plugin retourne une liste du registre (dans langues/listes) en tenant compte de la langue courante
 * Si [bool_texte]==1, on retourne une chaine "<option>xxx</option><option>...."
 * Auquel cas on pourra aussi utiliser [selected] => la valeur s�lectionn�e
 * ATTENTION : ne retourne pas <select></select> car on peut avouir besoin de mettre des choses sp�cifiques dans ces balises
 * 
 * si [langue] est défini, ça écrase le code langue en cours
 * 
 * ** optionnel** On peut appliquer un restricteur. dans ce cas, seuls les �l�ments conteant la chaine restricteur sont retourn�s
 * TODO !! un type resticteur (pour faire commence, contient...)
 * 
 * @param mixed $parametres
 * @param[nom_liste]
 * @param[restricteur]
 * @param[bool_texte]
 * @param["langue"] permet de forcer une langue (indépendamment de la langue en cours)
 * 
 * @return SOIT [0,1,2,...][intitule|valeur]
 * @return SOIT [texte]=>"<option>xxx</option><option>...."
 */
function plugin_div_get_liste_choix ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $nom_liste=$parametres["nom_liste"];
    $chemin="langues/listes/".$nom_liste."/_intitules";
    $langue=$parametres["langue"];
    $code_langue=get_code_langue();
    $code_langue_defaut=$GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_defaut"];
    
    if ($langue != "") {
        $code_langue=$langue;
    }
    
    try {$liste=p_get_registre($chemin);}
  	catch (Exception $e) {
  	     $retour["erreur"]=" ? $chemin ? ";
         $retour["succes"]=0;
         return ($retour);
    }
    $liste_tmp=array();
    foreach ($liste as $valeur => $element) {
        if (isset($element[$code_langue])) {
            $intitule=$element[$code_langue];
        } elseif (isset($element[$code_langue_defaut])) {
            $intitule=$element[$code_langue_defaut];
        } else {
            $intitule="?? $valeur ??";
        }
        array_push($liste_tmp, array("intitule"=>$intitule, "valeur"=>$valeur));
    }
    
    if ($parametres["restricteur"] != "") {
        $liste_tmp2=array();
        foreach ($liste_tmp as $element_tmp) {
            if (stripos($element_tmp["valeur"], $parametres["restricteur"]) !== false) {
                array_push($liste_tmp2, $element_tmp);
            }
        }
        $liste_tmp=$liste_tmp2;
        
    }
    
    if ($parametres["bool_texte"]==1) {
        $selected="";
        if ($parametres["selected"]!="") {
            $selected=$parametres["selected"];
        }
        $tmp="";
        foreach ($liste_tmp as $option) {
            $option_intitule=$option["intitule"];
            $option_valeur=$option["valeur"];
            $bool_selected="";
            if ($selected==$option_valeur) {
                $bool_selected=" selected=\"selected\" ";
            }
            $tmp.="<option value=\"$option_valeur\" $bool_selected>$option_intitule"."</option>\n";
        }
        $retour["resultat"]["texte"]=$tmp;
        return ($retour);
    }
    
    $retour["resultat"]=$liste_tmp;
    return ($retour);
} // fin du plugin


?>