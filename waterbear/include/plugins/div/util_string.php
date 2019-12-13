<?php
/**
 * plugin_div_util_string()
 * 
 * Ce plugin va effectuer un certain nombre de traitements sur une chaîne de caractères
 * 
 * Les fonctions gèrent l'unicode (quand c'est possible)
 * 
 * 
 * @param mixed $parametres
 * @param [texte] => le texte à transformer
 * @param [traitements][0,1,2...][methode] => substr | strtoupper | strtolower |ucfirst...
 * @param [traitements][0,1,2...][XXXX] => autres arguments variables en fonction de la méthode utilisée
 * 
 * @return [texte]
 */
function plugin_div_util_string ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $traitements=$parametres["traitements"];
    $chaine=$parametres["texte"];
    
    foreach ($traitements as $traitement) {
        $methode=$traitement["methode"];
        if ($methode == "substr") {
            $start=$traitement["start"];
            $longueur=$traitement["longueur"];
            //$chaine=substr($chaine, $start, $longueur);
            $chaine=mb_substr($chaine, $start, $longueur, "UTF-8");
        } elseif ($methode == "strtoupper") {
            //$chaine=strtoupper($chaine);
            $chaine=mb_strtoupper($chaine, "UTF-8");
        } elseif ($methode == "strtolower") {
           // $chaine=strtolower($chaine);
            $chaine=mb_strtolower($chaine, "UTF-8");
        } elseif ($methode == "ucfirst") {
            $chaine = ucfirst($chaine);
        } elseif ($methode == "str_replace") {
            $remplace=$traitement["remplace"];
            $a_remplacer=$traitement["a_remplacer"];
            if ($a_remplacer=="{debut}") {
                $chaine=$remplace.$chaine;
            } elseif ($a_remplacer=="{fin}") {
                $chaine=$chaine.$remplace;
            } else {
                $chaine = str_replace($a_remplacer, $remplace, $chaine);
            }
        } elseif ($methode=="preg_replace") {
            $remplace=$traitement["remplace"];
            $a_remplacer=$traitement["a_remplacer"];
            $chaine=preg_replace($a_remplacer, $remplace, $chaine);
        }
    }
    
    
    
    $retour["resultat"]["texte"]=$chaine;
    return ($retour);
    
}


?>