<?php

$descriptif='???';
$nom='???';
$version='22';

// ré-enregistrement de toutes les notices biblio pour créer la colonne a_unimarc
$sql="select * from obj_biblio_acces where a_unimarc IS NULL";

$resultat=sql_query(array("sql"=>$sql, "contexte"=>"maj22"));


while ($ligne=mysql_fetch_assoc($resultat)) {
    $ID=$ligne["ID"];
    $notice=get_objet_xml_by_id("biblio", $ID);
    if ($notice === false) {
        continue;
    }
    $plugin_enregistrement=array("nom_plugin"=>"catalogue/marcxml/db/notice_2_db/biblio/unimarc_standard", "parametres"=>array());
    $tmp=applique_plugin($plugin_enregistrement, array("ID_notice"=>$ID, "notice"=>$notice));
    if ($tmp["succes"] != 1) {
        // die ("Erreur lors de la réindexation de la table obj_biblio_acces : ".$tmp["erreur"]);
        print ("Erreur lors de la réindexation de la table obj_biblio_acces : ID=$ID : ".$tmp["erreur"]."<br>\n");
    } else {
        print ("$ID OK <br>\n");
    }
}

?>