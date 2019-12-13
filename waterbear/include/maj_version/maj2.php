<?PHP 
// ancien timestamp : 1000 
// ancienne version : 1 
// ancien nom : 0.1.0 
// Pour faire une maj : copier le contenu de cette page et le coller dans un fichier maj1.php dans le repertoire include/maj_version 
// Ne pas oublier de modifier le script conf/version.php pour indiquer la derniere version 


$chaine_registre='[{"timestamp":"1387909616","type":"niv2_create_node","chemin":"profiles\\/defaut\\/plugins\\/plugins\\/test","nom":"nouveau noeud","valeur":"","description":""},{"timestamp":"1387909622","type":"niv2_update_node","chemin":"profiles\\/defaut\\/plugins\\/plugins\\/test\\/nouveau noeud","nom":"ceci est un test","valeur":"","description":""},{"timestamp":"1387909625","type":"niv2_create_node","chemin":"profiles\\/defaut\\/plugins\\/plugins\\/test\\/ceci est un test","nom":"nouveau noeud","valeur":"","description":""},{"timestamp":"1387909629","type":"niv2_update_node","chemin":"profiles\\/defaut\\/plugins\\/plugins\\/test\\/ceci est un test\\/nouveau noeud","nom":"toto","valeur":"","description":""},{"timestamp":"1387909672","type":"niv2_update_node","chemin":"profiles\\/defaut\\/plugins\\/plugins\\/test\\/plugin_crea_xml","nom":"plugin_crea_xml_toto","valeur":"","description":"toto "},{"timestamp":"1387909683","type":"supprimer_noeud","chemin":"profiles\\/defaut\\/plugins\\/plugins\\/test\\/test_xml","nom":"","valeur":"","description":""}]';
$chaine_objets='[{"timestamp":"1387909742","type":"acces_valide_form","type_objet":"ville","nom":"toto","nom_colonne":"a_toto","ancien_nom_colonne":"","type_colonne":"VARCHAR(250)","description_colonne":"","type_index":"","multivaleurs":"0"}]';
$chaine_paniers='[{"ID":"152","nom":"recherches","description":"","chemin_parent":"waterbear","type":"repertoire","type_obj":"pret","nb":"0","date_creation":"2013-08-15","proprietaire":"","contenu":""},{"ID":"153","nom":"statistiques","description":"","chemin_parent":"waterbear","type":"repertoire","type_obj":"pret","nb":"0","date_creation":"2013-08-15","proprietaire":"","contenu":""},{"ID":"162","nom":"recherches","description":"","chemin_parent":"waterbear","type":"repertoire","type_obj":"lecteur","nb":"0","date_creation":"2013-08-19","proprietaire":"","contenu":""},{"ID":"163","nom":"statistiques","description":"","chemin_parent":"waterbear","type":"repertoire","type_obj":"lecteur","nb":"0","date_creation":"2013-08-19","proprietaire":"","contenu":""}]';
$descriptif='???';
$nom='???';
$version='2';
mwb_importe_registre($chaine_registre);
mwb_importe_objets($chaine_objets);
mwb_importe_paniers($chaine_paniers);
?> 
