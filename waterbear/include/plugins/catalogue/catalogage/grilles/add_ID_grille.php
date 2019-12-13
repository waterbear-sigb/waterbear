<?php

/**
 * catalogue_catalogage_grilles_add_ID_grille()
 * 
 * @param mixed $parametres
 * @return void
 */
 
 
function plugin_catalogue_catalogage_grilles_add_ID_grille ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $idx=0;
    $onglets=$parametres["onglets"];
    $intitules=$parametres["intitules"];
    
    foreach ($onglets as $clef_onglet => $onglet) {
        $onglets[$clef_onglet]["id"]=$idx;
        $idx++;
        if (is_array($onglet["champs"])) {
            foreach ($onglet["champs"] as $clef_champ => $champ) {
                $onglets[$clef_onglet]["champs"][$clef_champ]["id"]=$idx;
                $idx++;
                foreach ($champ["ss_champs"] as $clef_ss_champ => $ss_champ) {
                    $onglets[$clef_onglet]["champs"][$clef_champ]["ss_champs"][$clef_ss_champ]["id"]=$idx;
                    $idx++;
                }
            }
        }
    }
    $retour["resultat"]["onglets"]=$onglets;
    $retour["resultat"]["intitules"]=$intitules;
    $retour["resultat"]["icones_ss_champ_defaut"]=$parametres["icones_ss_champ_defaut"];
    $retour["resultat"]["icones_champ_defaut"]=$parametres["icones_champ_defaut"];
    $retour["resultat"]["last_ID"]=$idx;
    return ($retour);
    
} // fin de la fonction

?>