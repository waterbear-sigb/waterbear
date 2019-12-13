<?php

/**
 * plugin_transactions_bureau_bureau_2_js()
 * 
 * Ce plugin alimente le tableau bureau["commandes"] qui contient de manière schématisée toutes les commandes javascript qui devront être effectuées par le client
 * Chaque commande contient le nom d'un objet (facultatif), d'une méthode ou fonction et les paramètres de cette fonction
 * On utilisera généralement des ## ou des alias pour intégrer des éléments du bureau dans les paramètres des commandes
 * 
 * @param mixed $parametres
 * @param [bureau] => le bureau
 * @param [commandes] => liste des commandes js à ajouter
 * @param ---------- [objet] => objet js qui doit exécuter la méthode (si rien, alors simple fonction)
 * @param ---------- [methode] => méthode à appeler (ou fonction si pas d'objet)
 * @param ---------- [parametres] => paramètres de la méthode
 * @return
 */
function plugin_transactions_bureau_bureau_2_js ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $bureau=$parametres["bureau"];
    $commandes=$parametres["commandes"];
    
    foreach ($commandes as $commande) {
        array_push ($bureau["commandes"], $commande);
    }
    
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);

}
?>