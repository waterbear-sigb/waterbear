<?php

/**
 * plugin_catalogue_import_export_formate_ss_champ()
 * 
 * Ce plugin met � jour un sous champ d'une notice pass�e via [tvs_marcxml]
 * Il appelle le plugin [plugin_formate] et lui transmet la valeur du ss-champ
 * puis maj la notice (ATTENTION pas enregistr� dans la DB)
 * 
 * [texte] => plugin_formate => [texte]
 * 
 *  
 * @param mixed $parametres
 * @param [tvs_marcxml] =>la notice � modifier
 * @param [ss_champ] => ss-champ � modifier (�l�ment de tvs_marcxml)
 * @param [plugin_formate] => le plugin qui va modifier le ss-champ (en param [texte] et retourne [texte] : si on veut modifier il faut des alias)
 * 
 * @return 
 */
function plugin_catalogue_import_export_formate_ss_champ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $notice=$parametres["notice"];
    $champ=$parametres["champ"];
    $ss_champ=$parametres["ss_champ"];
    $nom_champ=$parametres["nom_champ"];
    $nom_ss_champ=$parametres["nom_ss_champ"];
    
    $plugin_formate=$parametres["plugin_formate"];
    
    $valeur_ss_champ=$tvs_marcxml->get_valeur_ss_champ($ss_champ);
    
    $tmp=applique_plugin($plugin_formate, array("texte"=>$valeur_ss_champ));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nouv_valeur=$tmp["resultat"]["texte"];
    
    $tvs_marcxml->update_ss_champ($ss_champ, $nouv_valeur);
    
    return ($retour);
    
}


?>