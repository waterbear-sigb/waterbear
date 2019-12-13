<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_get_liste_champs()
 * 
 * @param mixed $parametres
 * @param [idx_onglet]
 * @param [plugin_onglet]
 * @return
 * 
 * Ce plugin retourne une liste de champs qu'on peut ins�rer dans un onglet dont la d�finition est donn� en param�tres
 * Elle effectue essentiellement un d�doublonnage des champs (au cas o� le m�me champ serait d�fini plusieurs fois dans l'onglet) et un tri
 * L'onglet fourni n'est pas n�cessairement le m�me que l'onglet utilis� en catalogage (�a peut �tre un mod�le)
 * 
 * ATTENTION ce plugin ne g�re pas la notion de champ r�p�table ou non r�p�table
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_get_liste_champs ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $tmp=applique_plugin(array("nom_plugin"=>$parametres["plugin_onglet"]), array()); 
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $liste=$tmp["resultat"];
    $liste_retour=array();
    foreach ($liste["champs"] as $onsenfout => $champ) {
        $nom=$champ["nom"];
        $intitule=$champ["intitule"];
        $auto_plugin=$champ["auto_plugin"];
        if (! isset ($liste_retour[$nom])) {
            $liste_retour[$nom]=array("nom"=>$nom, "intitule"=>$intitule, "auto_plugin"=>$auto_plugin);
        }
    }
    ksort ($liste_retour);
    $retour["resultat"][0]=$liste_retour;
    $retour["resultat"][1]="this_formulator.affiche_liste_champs(param)";  
    
    
    return ($retour);
}
?>