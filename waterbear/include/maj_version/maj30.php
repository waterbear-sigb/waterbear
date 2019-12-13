<?PHP

$registre=new tvs_registre();

// on rajoute le compteur system/compteurs/cab_incrementiel_exemplaire_1
try {
    $noeud=$registre->get_node_by_chemin("system/compteurs");
    $ID_parent=$noeud["ID"];
    $registre->niv2_create_node (array("nom"=>"cab_incrementiel_exemplaire_1", "parent"=>$ID_parent, "description"=>"", "valeur"=>"1"));
} catch (tvs_exception $e) {
    $erreur=utf8_encode(get_exception($e->get_infos()));
    print ("mwb_importe_registre::ERREUR : $type - $chemin - $nom - $valeur - $description - $erreur <br>\n");
}

$sqls=array();
$sqls[]="update tvs_registre set description = '@mwb_non_export@' where chemin = 'system'";
$sqls[]="update tvs_registre set description = '@mwb_non_export@' where chemin = 'profiles/defaut/plugins/plugins/div/cab_2_infos'";

foreach ($sqls as $sql) {
    mysql_query($sql) OR print ("erreur sql : $sql");
}


?>