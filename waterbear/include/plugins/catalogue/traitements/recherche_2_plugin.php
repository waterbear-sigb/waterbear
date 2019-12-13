<?php

/**
 * plugin_catalogue_traitements_recherche_2_plugin()
 * 
 * 
 * 
 * @param mixed $parametres
 * @return void
 */
function plugin_catalogue_traitements_recherche_2_plugin ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["texte"]="";
    $retour["resultat"]["erreurs"]="";
    
    $plugin_recherche=$parametres["plugin_recherche"];
    $plugin_maj=$parametres["plugin_maj"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $nom_traitement=$parametres["nom_traitement"]; // via le PA
  
    $retour["resultat"]["texte"].="Execution du traitement $nom_traitement <br>";
    // 1) recherche
    $tmp=applique_plugin($plugin_recherche, array());
    if ($tmp["succes"] != 1) {
        return($tmp);
    }
    $lignes=$tmp["resultat"]["notices"];
    $nb_notices=$tmp["resultat"]["nb_notices"];
    $retour["resultat"]["texte"].="$nb_notices notices trouvees <br>";
      
    // 2) modif et enregistrement de chaque notice
    foreach ($lignes as $ligne) {
        $notice=$ligne["xml"];
        $ID_notice=$ligne["ID"];
        $tmp=applique_plugin($plugin_maj, array("notice"=>$notice));
        $retour["resultat"]["erreurs"].="Erreur plugin de maj pour la notice $ID_notice <br>";
        
        $notice=$tmp["resultat"]["notice"];
        $tmp=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID_notice));
        $retour["resultat"]["erreurs"].="Erreur plugin notice_2_db pour la notice $ID_notice <br>";
        
    } // fin du pour chaque notice
    $retour["resultat"]["texte"].="fin du traitement $nom_traitement <br>-----------------------<br><br>";
    
    
    
    return($retour);
}

?>