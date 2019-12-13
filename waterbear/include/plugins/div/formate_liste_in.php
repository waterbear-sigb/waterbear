<?php
/**
 * plugin_div_formate_liste_in()
 * 
 * Ce plugin transforme une chaine contenant une liste de crit�res s�pa�s par un caract�re quelconque en une chaine exploitable dans une recherche sql
 * de type select xxx from yyy where zzz in (...)
 * permet par exemple, de transformer une liste de cab s�par�s par des retours � la ligne en une chaine de type '123', '345', '567'...
 * 
 * @param mixed $parametres
 * @param [separateur] => l'�l�ment s�parateur (par d�faut retour � la ligne)
 * @param [bool_guillemets] => si vaut 1 , les �l�ments seront entour�s de guillemets
 * @param [bool_secure_sql] => si vaut 1, chaque �lement de la liste est s�curis� individuellement
 * @param [chaine] => la chaine � d�couper
 * 
 * @return [chaine] => la chaine retourn�e de la forme '123', '345', '567'...
 */
function plugin_div_formate_liste_in ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();

   
    // param�tres
    $separateur=$parametres["separateur"];
    $bool_guillemets=$parametres["bool_guillemets"];
    $bool_secure_sql=$parametres["bool_secure_sql"];
    $chaine=$parametres["chaine"];
    
    // valeurs par d�faut
    if ($separateur=="") {
        $separateur='\n';
    } 
    if ($bool_guillemets === "") {
        $bool_guillemets=1;
    }
    if ($bool_secure_sql === "") {
        $bool_secure_sql=1;
    }
    
    // on explode la chaine
    $retour["resultat"]["chaine"]="";
    $liste=explode($separateur, $chaine);
    foreach ($liste as $element) {
        $element=trim($element);
        if ($element == "") {
            continue;
        }
        if ($bool_secure_sql==1) {
            $element=secure_sql($element);
        }
        if ($bool_guillemets==1) {
            $element="'".$element."'";
        }
        if ($retour["resultat"]["chaine"] != "") {
            $retour["resultat"]["chaine"].=",";
        }
        $retour["resultat"]["chaine"].=$element;
    }
 
    return ($retour);
    
}

?>