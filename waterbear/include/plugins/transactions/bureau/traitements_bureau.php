<?php
/**
 * plugin_transactions_bureau_traitements_bureau()
 * 
 * Ce plugin va appliquer une série de plugins sur le bureau
 * Ces plugins vont généralement modifier le bureau (mais pas obligatoirement)
 * 
 * Certains plugins sont susceptibles de retourner une close [break], auquel cas les traitements sont interrompus
 * Le plugin retournera alors lui-même une close [break-1], ce qui fait que plusieurs plugins traitements_bureau peuvent être
 * inclus les uns dans les autres
 * 
 * Si le nom d'un des traitements commence par "_", il n'est pas pris en compte
 * 
 * Signature des plugins du traitement :
 * ([bureau], [break])=plugin([bureau])
 * 
 * @param [bureau] => le bureau
 * @param [traitements][0,1,2...] => liste des traitements à effectuer sur le bureau
 * @param ------ [nom_plugin | parametres...]
 * 
 * @return [bureau] => le bureau modifié
 * @return [break] => une close break
 */



function plugin_transactions_bureau_traitements_bureau ($parametres) {
    
    GLOBAL $dbg_tab_traitements_bureau; 
    if (!isset($dbg_tab_traitements_bureau)) {
        $dbg_tab_traitements_bureau=0;
    }
    $tab=str_repeat("    ", $dbg_tab_traitements_bureau);
    
    
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["bureau"]=$bureau;
    
    $bureau=$parametres["bureau"];
    $traitements=$parametres["traitements"];
    
    //dbg_log ($tab."=================== traitements_bureau ===================");
    $microtime1=microtime(true);
    
    
    $retour["resultat"]["break"]=0;
    $dbg_tab_traitements_bureau++;
    foreach ($traitements as $nom => $traitement) {
        if (substr($nom, 0, 1) == "_") {
            continue;
        }
        $mt1=microtime(true);
        //dbg_log($tab."    -> DEBUT $nom ($nom_plugin)");
        $tmp=applique_plugin($traitement, array("bureau"=>$bureau));
        $mt2=microtime(true);
        $mt=$mt2-$mt1;
        $nom_plugin=$traitement["nom_plugin"];
        //dbg_log($tab."    -> FIN   $nom ($nom_plugin) : $mt");
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        if (isset($tmp["resultat"]["bureau"])) {
            $bureau=$tmp["resultat"]["bureau"];
        }
        if (isset($tmp["resultat"]["break"])) {
            if ($tmp["resultat"]["break"] > 0) {
                $retour["resultat"]["break"]=$tmp["resultat"]["break"]-1;
                break;
            }
        }
    }
    $dbg_tab_traitements_bureau--;
    
    $microtime2=microtime(true);
    $microtime=$microtime2-$microtime1;
    //dbg_log ($tab."=================== FIN traitements_bureau : $microtime ===================");
    
    
    $retour["resultat"]["bureau"]=$bureau;
    return ($retour);
}
?>