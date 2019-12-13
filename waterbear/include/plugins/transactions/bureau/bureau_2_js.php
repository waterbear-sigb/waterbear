<?php

/**
 * plugin_transactions_bureau_bureau_2_js()
 * 
 * Ce plugin alimente le tableau bureau["commandes"] qui contient de mani�re sch�matis�e toutes les commandes javascript qui devront �tre effectu�es par le client
 * Chaque commande contient le nom d'un objet (facultatif), d'une m�thode ou fonction et les param�tres de cette fonction
 * On utilisera g�n�ralement des ## ou des alias pour int�grer des �l�ments du bureau dans les param�tres des commandes
 * 
 * @param mixed $parametres
 * @param [bureau] => le bureau
 * @param [commandes] => liste des commandes js � ajouter
 * @param ---------- [objet] => objet js qui doit ex�cuter la m�thode (si rien, alors simple fonction)
 * @param ---------- [methode] => m�thode � appeler (ou fonction si pas d'objet)
 * @param ---------- [parametres] => param�tres de la m�thode
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