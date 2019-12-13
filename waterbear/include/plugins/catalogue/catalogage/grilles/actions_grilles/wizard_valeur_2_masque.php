<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_valeur_2_masque()
 * 
 * ce plugin va appliquer un masque de ctalogage en fonction de la valeur d'un ss-champ
 * On fournit dans le paramètre [cas] les différentes valeurs possibles (ou _else) et pour chacune le masque
 * correspondant. Si on a une chaine vide pour une valeur, aucun masque spécifique ne sera appliqué (pas de modification)
 * 
 * @param mixed $parametres
 * @param [cas][val1, val2, val3, _else] => nom du masque
 * 
 * 
 * 
 * @return 
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_valeur_2_masque ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $valeur=$_REQUEST["valeur"];
    $cas=$parametres["cas"];
    
    $type_obj=$parametres["type_obj"];
    $ID_element=$parametres["ID_element"];
    $ID_operation=$parametres["ID_operation"];
    
    $masque="";
    
    if (isset($cas[$valeur])) {
        $masque=$cas[$valeur];
    } elseif (isset($cas["_else"])) {
         $masque=$cas["_else"];
    }
    
    $update=array("valeur"=>$valeur);
    $_SESSION["operations"][$ID_operation]["formulator"]->update_element ($ID_element, $update);
    
    if ($masque != "") {
        $retour["resultat"][0]="this_formulator.set_masque_actuel('$masque')";
    }
    
   
    return($retour);
    
    
    
}

?>