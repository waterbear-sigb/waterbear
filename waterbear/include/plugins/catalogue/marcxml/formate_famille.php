<?php

/**
 * plugin_catalogue_marcxml_formate_famille()
 * 
 * ce plugin génère l'affichage d'une famille : d'abord le chef de famille puis les membres
 * si carte autonome, on affiche juste la carte
 * si id_famille=="" on a soit chef de famille soit carte autonome SINON carte membre de famille
 * si membre de famille : on recherche d'abord le chef de famille (plugin_recherche_lecteur[id_famille]) puis les membres (plugin_recherche_membres[id_famille])
 * 
 * SINON : on recherche les membres de cette carte : plugin_recherche_membre[id_lecteur] (peut être vide) et le chef de famille (ou carte autonome) : plugin_recherche_lecteur[id_lecteur]
 * 
 * 
 * @param mixed $parametres
 * @param [id_famille] => ID de la famille
 * @param [id_lecteur] => ID du lecteur
 * @param [plugin_recherche_lecteur] : recherche un lecteur par son ID
 * @param [plugin_recherche_membres] : recherche des lecteurs par leur id_famille
 * 
 * 
 * @return [chaine] => la chaine transformée
 */
function plugin_catalogue_marcxml_formate_famille ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["chaine"]="";
    
    $id_famille=$parametres["id_famille"];
    $id_lecteur=$parametres["id_lecteur"];
    $plugin_recherche_lecteur=$parametres["plugin_recherche_lecteur"];
    $plugin_recherche_membres=$parametres["plugin_recherche_membres"];
    $plugin_formate_array=$parametres["plugin_formate_array"];
    
    $chaine_chef="";
    $membres=array();
    
    $id=$id_lecteur;
    if ($id_famille != "" AND $id_famille != "0") { // si membre d'une famille'
        $id=$id_famille;
    }
        
    $tmp=applique_plugin($plugin_recherche_lecteur, array("id_lecteur"=>$id));
    if($tmp["succes"] != 1) {
        return($tmp);
    }
    $chaine_chef=$tmp["resultat"]["notices"][0];
        
    $tmp=applique_plugin($plugin_recherche_membres, array("id_famille"=>$id));
    if($tmp["succes"] != 1) {
        return($tmp);
    }
    $membres=$tmp["resultat"]["notices"];
    array_unshift($membres, $chaine_chef);
    
   
    $tmp=applique_plugin($plugin_formate_array, array("tableau"=>$membres));
    if($tmp["succes"] != 1) {
        return($tmp);
    }

    
    $retour["resultat"]["chaine"]=$tmp["resultat"]["texte"];
       
    
    
    return ($retour);
}


?>