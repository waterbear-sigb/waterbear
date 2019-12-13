<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_formate_date()
 * 
 * ce plugin formate une date au format AAAA-MM-JJ
 * Il détecte si la date est entrée au format US, FR ou sous la forme d'une simple année (considéré alors au 01/01)
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_formate_date ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $date=$_REQUEST["valeur"];
    $type_obj=$parametres["type_obj"];
    $ID_element=$parametres["ID_element"];
    $ID_operation=$parametres["ID_operation"];
    
    $separateurs=array("-", "/", " ", "_", ".");
    $timestamp=false;
    $bool_erreur=0;
    
    if (strlen($date)==4 AND is_numeric($date)) {
        $date=$date."-01-01";
    } else {
        $date2=str_replace($separateurs, $separateurs[0], $date); // remplace tous les séparateurs possibles par le 1er de la liste (-)
        $elements=explode($separateurs[0], $date2);
        if (count($elements) != 3) {
            $bool_erreur=1;
        }
        if (strlen($elements[0])==4) { // format américain
            $timestamp=mktime(0, 0, 0, $elements[1], $elements[2], $elements[0]);
        } elseif (strlen($elements[2])==4) {// format français
            $timestamp=mktime(0, 0, 0, $elements[1], $elements[0], $elements[2]);
        } else {
            $bool_erreur=2;
        }
        
        if ($timestamp != false) {
            $date=date("Y-m-d", $timestamp);
        } else {
            $bool_erreur=3;
        }
    }
    
    // si erreur
    if ($bool_erreur!=0) {
        $update=array("valeur"=>"");
        $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
        array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("");');
        array_push($retour["resultat"], 'alert("saisissez une date sous la forme AAAA-MM-JJ")');
        return ($retour);
    }
    
    $update=array("valeur"=>$date);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    array_push($retour["resultat"], 'this_formulator.liste_objets['.$ID_element.'].set_valeur("'.$date.'");');
    
    return ($retour);
}


?>