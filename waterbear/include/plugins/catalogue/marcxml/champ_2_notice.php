<?php
/**
 * plugin_catalogue_marcxml_champ_2_notice()
 * 
 * Ce plugin permet de convertir un champ (DomNode) en notice. Il est typiquement utilis� pour formater le contenu d'un champ � l'aide
 * de plugins comme get_datafields ou formate_plugins. Comme ces plugins ne peuvent s'appliquer qu'� une notice (et pas un champ),
 * on va cr�er une notice 'bidon' avec un seul champ
 * Cette notice sera ensuite fournie en param�tre au plugin [plugin_formate] avec la signature suivante :
 * 
 * [texte] plugin_formate [tvs_marcxml | champ] 
 * 
 * Si le plugin de formatage ne g�re pas le tvs_marcxml, il faudra utiliser un alias
 * 
 * Si on ne fournit pas de plugin de formatage, le plugin retourne la notice elle-m�me
 * 
 * @param mixed $parametres
 * @param [champ] => champ � convertir en notice (sous forme de domNode)
 * @param [plugin_formate] ** option ** : plugin qui va formater la notice cr��e
 * 
 * @return [texte] (si plugin_formate) OU tvs_marcxml
 */
function plugin_catalogue_marcxml_champ_2_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $champ=$parametres["champ"];
    $plugin_formate=$parametres["plugin_formate"];
    
    // 1) On g�n�re une notice tvs_marcxml � partir du champ
    $tvs_marcxml=new tvs_marcxml (array());
    $tvs_marcxml->new_notice();
    $tmp=$tvs_marcxml->notice->importNode($champ, true);
    if ($tmp == false) {
        $succes=0;
        $erreur="@& Plugin catalogue/marcxml/champ_2_notice : impossible d'importer le champ";
    }
    $tvs_marcxml->record->appendChild($tmp);
    
    
    //2) on applique cette notice au plugin (option)
    if (is_array($plugin_formate)) {
        $tmp=applique_plugin($plugin_formate, array("tvs_marcxml"=>$tvs_marcxml, "champ"=>$tmp));
        return ($tmp);
    }
    
    // SINON, on retourne la notice elle-m�me
    $retour["resultat"]["tvs_marcxml"]=$tvs_marcxml;
    return ($retour);
}


?>