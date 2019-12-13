<?php

/**
 * plugin_catalogue_commandes_teste_ligne_commande_modifiable()
 * 
 * @param mixed $parametres
 * @param [notice] OU [ID_notice] et [type_doc] OU [tvs_marcxml] => la notice exemplaire à tester
 * @param [plugin_get_etat] => plugin qui va retourner la valeur du 500$5 de l'exemplaire (de type get_datafield...)
 *                             On passe à ce plugin les mêmes paramètres, de cette manière ça marchera qu'on fournisse un ID_notice, un notice, un tvs_marcxml...
 * @param [choix]['cours'=>1, 'valide'=>0, 'solde'=>0, ...] => un tableau associant à chaque code état du 500$5, une valeur 0 ou 1 selon que la modif de la notice est possible ou pas
 * @param [defaut] => comportement par défaut si le code n'est pas défini
 * 
 * @return [choix] => 0 ou 1
 */
function plugin_catalogue_commandes_teste_ligne_commande_modifiable ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $plugin_get_etat=$parametres["plugin_get_etat"];
    $choix=$parametres["choix"];
    $defaut=$parametres["defaut"];
    
    
    // 1) On récupère l'état de la commande (champ 500$5 de l'exemplaire)
    // on passe au plugin les mêmes paramètres que ceux reçus. On pourra donc avoir ID_notice, notice, tvs_marcxml...
    $tmp=applique_plugin($plugin_get_etat, $parametres);
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $code_etat=$tmp["resultat"]["texte"];
    
    // 2) On récupère la valeur à retourner en fonction
    $code_retour=$defaut;
    if (isset($choix["code_etat"])) {
        $code_retour=$choix["code_etat"];
    } 
    
    $retour["resultat"]["choix"]=$code_retour;
    return ($retour);
    
}


?>