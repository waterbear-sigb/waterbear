<?php

/**
 * plugin_catalogue_catalogage_grilles_actions_grilles_get_liste_ss_champs()
 * 
 * @param mixed $parametres
 * @param [ID_element]
 * @param [plugin_champ] // d�fini dans le registre (switchers)
 * @return
 * 
 * Ce plugin retourne une liste de sous-champs qu'on peut ins�rer dans un champ dont la d�finition est donn� en param�tres
 * Elle effectue essentiellement un d�doublonnage des ss-champs (au cas o� le m�me ss-champ serait d�fini plusieurs fois dans le champ) et un tri
 * Le champ fourni n'est pas n�cessairement le m�me que le champ utilis� en catalogage (�a peut �tre un mod�le)
 * 
 * ATTENTION ce plugin ne g�re pas la notion de ss-champ r�p�table ou non r�p�table
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