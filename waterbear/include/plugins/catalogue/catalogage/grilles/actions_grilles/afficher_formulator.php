<?php
/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_afficher_formulator()
 * 
 * @param mixed $parametres
 * @param [infos] => [type_element], [nom_champ], [nom_ss_champ], [idx_onglet], [idx_champ], [idx_ss_champ]
 * @param [ID_element]
 * @param [ID_operation]
 * @param [action]
 * @return array
 * Ce plugin affiche le formulator (coté serveur) sous la forme d'une chaine de caractères
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_afficher_formulator ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_operation=$parametres["ID_operation"];

    $tmp=$_SESSION["operations"][$ID_operation]["formulator"]->onglets;
    $str_retour="";
    foreach ($tmp as $idx_onglet => $onglet) {
        $str_retour.="onglet $idx_onglet ******** \\n ";
        foreach ($onglet["champs"] as $idx_champ => $champ) {
            $str_retour.="    ".$champ["nom"]."\\n";
            foreach ($champ["ss_champs"] as $idx_ss_champ => $ss_champ) {
                $str_retour.="        $".$ss_champ["nom"]." : ".$ss_champ["valeur"]."\\n";
            }
        } 
    }

    $retour["resultat"][0]='alert ("'.$str_retour.'");';

    return ($retour);    
    
}



?>