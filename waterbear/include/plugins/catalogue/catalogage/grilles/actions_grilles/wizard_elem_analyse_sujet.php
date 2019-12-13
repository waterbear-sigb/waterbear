<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_autorite_matiere()
 * 
 * Ce plugin analyse une chaine sujet et retourne la t�te de vedette et les diff�rentes subdivisions
 * La chaine peut repr�senter une autorit� mati�re (a : b : c) ou une vedette mati�re (a ** b ** c **)
 * 
 * Une syntaxe sp�ciale permet d'indiquer le type de subdivision :
 * :f ou *f => forme
 * :s ou *s => sujet
 * :c ou *c => chronologique
 * :g ou *g => g�ographique
 * 
 * Si la lettre n'est pas fournie (ou vaut '*') sera consid�r� comme la subdivision par [defaut] si fourni ou sujet sinon
 * 
 * @param mixed $parametres
 * @param [chaine] => chaine � analyser
 * @param [separateur] => ':' (pour les autorit�s) OU '*' (pour les vedettes) (attention, une seule �toile) (: par d�faut)
 * @param [defaut] => type de subdivision par d�faut (si non pr�cis� = sujet)
 * 
 * @return [variables][0,1,2...][type | valeur] avec type parmi : tete | j | x | y | z
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_wizard_elem_analyse_sujet ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["variables"]=array();
    
    $chaine=$parametres["chaine"];
    $longueur=strlen($chaine);
    $separateur=$parametres["separateur"];
    $defaut=$parametres["defaut"];
    if ($defaut == "") {
        $defaut="x";
    }
    if ($separateur == "") {
        $separateur=":";
    }
    
    $element="";
    $dernier_type="tete";
    for ($i=0 ; $i<$longueur ; $i++) {
        $car=substr($chaine, $i, 1);
        if ($car == $separateur OR $i >= $longueur-1) { // SI on a rencontr� le s�parateur ou la fin
        
            // On essaye de d�terminer le type
            if ($i < $longueur-1) {
                $type="";
                $car2=substr($chaine, $i+1, 1);
                if ($car2 == "f") {
                    $type="j";
                } elseif ($car2 == "s") {
                    $type="x";
                }  elseif ($car2 == "c") {
                    $type="z";
                } elseif ($car2 == "g") {
                    $type="y";
                } else {
                    $type=$defaut;
                }
                $i++;
            } else {
                $element.=$car;
            }
            
            // On ajoute l'�l�ment et le type au retour
            $element=trim($element);
            if ($element != "") {
                array_push($retour["resultat"]["variables"], array("type"=>$dernier_type, "valeur"=>$element));
                $element="";
            } 
            
            $dernier_type=$type;
            
        } else { // sinon
            $element.=$car;
        }
    }

    
    return ($retour);
}



?>