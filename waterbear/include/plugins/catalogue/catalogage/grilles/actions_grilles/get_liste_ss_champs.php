<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_get_liste_ss_champs()
 * 
 * @param mixed $parametres
 * @param [ID_element]
 * @param [plugin_champ] // défini dans le registre (switchers)
 * @return
 * 
 * Ce plugin retourne une liste de sous-champs qu'on peut insérer dans un champ dont la définition est donné en paramètres
 * Elle effectue essentiellement un dédoublonnage des ss-champs (au cas où le même ss-champ serait défini plusieurs fois dans le champ) et un tri
 * Le champ fourni n'est pas nécessairement le même que le champ utilisé en catalogage (ça peut être un modèle)
 * 
 * ATTENTION ce plugin ne gère pas la notion de ss-champ répétable ou non répétable
 */
function plugin_catalogue_catalogage_grilles_actions_grilles_get_liste_ss_champs ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $ID_element=$parametres["ID_element"];
    $tmp=applique_plugin(array("nom_plugin"=>$parametres["plugin_champ"]), array()); 
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $liste=$tmp["resultat"];
    $liste_retour=array();
    foreach ($liste["ss_champs"] as $onsenfout => $ss_champ) {
        $nom=$ss_champ["nom"];
        $intitule=$ss_champ["intitule"];
        $auto_plugin=$ss_champ["auto_plugin"];
        if (! isset ($liste_retour[$nom])) {
            $liste_retour[$nom]=array("nom"=>$nom, "intitule"=>$intitule, "auto_plugin"=>$auto_plugin);
        }
    }
    ksort ($liste_retour);
    $retour["resultat"][0]=$liste_retour;
    $retour["resultat"][1]="this_formulator.affiche_liste_ss_champs('$ID_element', param)";  
    
    
    return ($retour);
}
?>