<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_get_liste_champs()
 * 
 * @param mixed $parametres
 * @param [idx_onglet]
 * @param [plugin_onglet]
 * @return
 * 
 * Ce plugin retourne une liste de champs qu'on peut insérer dans un onglet dont la définition est donné en paramètres
 * Elle effectue essentiellement un dédoublonnage des champs (au cas où le même champ serait défini plusieurs fois dans l'onglet) et un tri
 * L'onglet fourni n'est pas nécessairement le même que l'onglet utilisé en catalogage (ça peut être un modèle)
 * 
 * ATTENTION ce plugin ne gère pas la notion de champ répétable ou non répétable
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