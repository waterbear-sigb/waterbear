<?php



include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/api_mel.php");

/**
 * plugin_catalogue_import_export_get_notice_mel()
 * 
 * 
 * 
 * @param mixed $parametres
 * @param [EAN] => EAN à rechercher
 * @param [identifiant] => ISBN (livres) ou EAN (CD, DVD)
 * 
 * @return void
 */
function plugin_catalogue_import_export_get_notice_mel($parametres) {
    extract ($parametres);
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // on instancie l'objet mel
    $mel=new api_mel ();
    $param=$mel->get_param_registre();
    $mel->load_param_mel($param);
    
    // On récupère la notice correspondant à l'ean
    $notice_marc=$mel->get_notice_by_ean(array("EAN"=>$EAN, "identifiant"=>$identifiant));
    if ($notice_marc=="") {
        $retour["erreur"]="Aucune notice trouvee";
        $retour["succes"]=0;
        return($retour);
    }
    
    // conversion de marc en marcxml
    $tmp=applique_plugin($plugin_marc2xml, array("notice"=>$notice_marc));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $notice_xml=$tmp["resultat"]["notice"];
   
    // intégration dans la base
    $tmp=applique_plugin($plugin_importe_notice, array("notice"=>$notice_xml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    return ($tmp);
  
    
}


?>