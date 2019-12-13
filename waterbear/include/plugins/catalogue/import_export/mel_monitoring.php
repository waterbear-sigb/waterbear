<?php

function plugin_catalogue_import_export_mel_monitoring($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["texte"]="";
    $retour["resultat"]["erreurs"]="";
    
    $plugin_get_notice=$parametres["plugin_get_notice"];
    
    $date=date("Y-m-d");
    $retour["resultat"]["texte"].="recuperation des fichiers a integrer <br>";
    
    $sql="select * from mel_monitoring where date_traitement = '0000-00-00' order by date_creation";
    $lignes=sql_as_array(array("sql"=>$sql, "contexte"=>"plugin_catalogue_import_export_mel_monitoring"));
    
    foreach ($lignes as $ligne) {
        $type=$ligne["type"];
        $url=$ligne["url"];
        $ID=$ligne["ID"];
        $retour["resultat"]["texte"].="$ID : $url ($type) <br>";
        
        if ($type != "biblio") {
            continue; // TMP !!!!
        }
        
        $chemin_local=importe_fichier($url);
        if ($chemin_local === false) {
            $retour["erreur"].="impossible de telecharger le fichier $url <br>";
            $retour["succes"]=0;
            continue;
        }
        $taille_fichier=filesize($chemin_local);
        $handle=fopen($chemin_local, "r");
        if ($handle == false) {
            $retour["erreur"].="impossible d'ouvrir le fichier $chemin_local <br>";
            $retour["succes"]=0;
            continue;
        }
        $tmp=applique_plugin($plugin_get_notice, array("handle"=>$handle, "taille_fichier"=>$taille_fichier, "panier"=>""));
        if ($tmp["succes"] != 1) {
            //return($tmp); => NON ! car ce plugin peut renvoie une erreur à la fin, même si tout s'est bien passé :/
        }
        $nb_notices_trouvees=$tmp["resultat"]["nb_notices_trouvees"];
        $commentaire=$tmp["resultat"]["commentaire"];
        $retour["resultat"]["texte"].="$nb_notices_trouvees notices trouvees : <br> $commentaire <br>";
        $sql="update mel_monitoring set date_traitement = '$date' where ID = $ID";
        sql_query(array("sql"=>$sql, "contexte"=>"plugin_catalogue_import_export_mel_monitoring"));
    }
    
    
    
    return ($retour);   
}



?>