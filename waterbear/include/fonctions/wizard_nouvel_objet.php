<?php

function wizard_nouvel_objet () {
    $nom_obj=$_REQUEST["nom_obj"];
   
    $definitions=array();
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$definitions["catalogage_bib"]=array(); 
$definitions["catalogage_bib"][0]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/catalogage/grilles/$nom_obj/_parametres/favicon", "valeur"=>"IMG/icones/page_white_edit.png", "description"=>"");   
$definitions["catalogage_bib"][1]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/catalogage/grilles/$nom_obj/_parametres/titre_page", "valeur"=>"bib/catalogue/catalogage/grilles/$nom_obj/titre_page", "description"=>"");     
$definitions["catalogage_bib"][2]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_page", "valeur"=>"bib/catalogue/catalogage.php", "description"=>"");      
$definitions["catalogage_bib"][3]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_parametres/masque_defaut", "valeur"=>"aucun", "description"=>"");  
$definitions["catalogage_bib"][4]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_parametres/page_ws", "valeur"=>"bib_ws.php?module=catalogue/catalogage/grilles/$nom_obj/unimarc_standard", "description"=>"");  
$definitions["catalogage_bib"][5]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_parametres/plugin_get_masques/nom_plugin", "valeur"=>"catalogue/catalogage/listes_masques/div/standard", "description"=>"");  

$definitions["catalogage_bib_ws"]=array(); 
$definitions["catalogage_bib_ws"][0]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/catalogage/grilles/$nom_obj/_parametres/type_objet", "valeur"=>"$nom_obj", "description"=>""); 
$definitions["catalogage_bib_ws"][1]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_page", "valeur"=>"bib_ws/catalogue/catalogage/grilles.php", "description"=>""); 
$definitions["catalogage_bib_ws"][2]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_parametres/plugin_formulaire/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard", "description"=>""); 
$definitions["catalogage_bib_ws"][3]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_parametres/plugin_formulaire_modification/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard_modification", "description"=>""); 
$definitions["catalogage_bib_ws"][4]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_parametres/plugin_switcher/nom_plugin", "valeur"=>"catalogue/catalogage/switchers/$nom_obj/unimarc_standard", "description"=>""); 

$definitions["catalogage_bib_langues"]=array(); 
$definitions["catalogage_bib_langues"][0]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_intitules/champ_200_description/_fr", "valeur"=>"Description", "description"=>""); 
$definitions["catalogage_bib_langues"][1]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_intitules/ss_champ_200_a_description/_fr", "valeur"=>"description", "description"=>"");
$definitions["catalogage_bib_langues"][2]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/_intitules/onglet_informations/_fr", "valeur"=>"Informations", "description"=>""); 
$definitions["catalogage_bib_langues"][3]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/catalogage/grilles/$nom_obj/_intitules/titre_page/_fr", "valeur"=>"$nom_obj", "description"=>""); 

$definitions["catalogage_plugins_grilles"]=array(); 
$definitions["catalogage_plugins_grilles"][0]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["catalogage_plugins_grilles"][1]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["catalogage_plugins_grilles"][2]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/parametres/nom", "valeur"=>"200", "description"=>""); 
$definitions["catalogage_plugins_grilles"][3]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/parametres/auto_plugin", "valeur"=>"catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200", "description"=>""); 
$definitions["catalogage_plugins_grilles"][4]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/parametres/!!icones/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_groupes_icones/biblio/unimarc_standard/champ_defaut", "description"=>""); 
$definitions["catalogage_plugins_grilles"][5]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/parametres/??intitule", "valeur"=>"bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/champ_200_description", "description"=>""); 
$definitions["catalogage_plugins_grilles"][6]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200/parametres/ss_champs/!!01-a/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a", "description"=>""); 

$definitions["catalogage_plugins_grilles"][7]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/catalogage/grilles", "description"=>""); 
$definitions["catalogage_plugins_grilles"][8]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"add_ID_grille", "description"=>""); 
$definitions["catalogage_plugins_grilles"][9]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/parametres/!!icones_champ_defaut/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_groupes_icones/biblio/unimarc_standard/champ_defaut", "description"=>"On peut laisser biblio. ce sont les mêmes icones partout"); 
$definitions["catalogage_plugins_grilles"][10]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/parametres/!!icones_ss_champ_defaut/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_groupes_icones/biblio/unimarc_standard/ss_champ_defaut", "description"=>"On peut laisser biblio. ce sont les mêmes icones partout"); 
$definitions["catalogage_plugins_grilles"][11]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/parametres/intitules/??l_ajouter_champ", "valeur"=>"bib/catalogue/catalogage/grilles/biblio/unimarc_standard/l_ajouter_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][12]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/parametres/intitules/??l_ajouter_ss_champ", "valeur"=>"bib/catalogue/catalogage/grilles/biblio/unimarc_standard/l_ajouter_ss_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][13]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/parametres/intitules/??l_suppr_valider", "valeur"=>"bib/catalogue/catalogage/grilles/biblio/unimarc_standard/l_suppr_valider", "description"=>"");
$definitions["catalogage_plugins_grilles"][14]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard/parametres/onglets/!!01 - informations/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_onglets/$nom_obj/unimarc_standard/informations", "description"=>"");
// + patch unimarc_standard_modification (cf plus bas)

$definitions["catalogage_plugins_grilles"][15]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_groupes_evenements/$nom_obj", "valeur"=>"", "description"=>"La plupart du temps on utilise ceux de biblio");
$definitions["catalogage_plugins_grilles"][16]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_groupes_icones/$nom_obj", "valeur"=>"", "description"=>"La plupart du temps on utilise ceux de biblio");
$definitions["catalogage_plugins_grilles"][17]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_icones/$nom_obj", "valeur"=>"", "description"=>"La plupart du temps on utilise ceux de biblio");

$definitions["catalogage_plugins_grilles"][18]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_onglets/$nom_obj/unimarc_standard/informations/chemin_fichier", "valeur"=>"div", "description"=>"");
$definitions["catalogage_plugins_grilles"][19]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_onglets/$nom_obj/unimarc_standard/informations/nom_fonction", "valeur"=>"plugins_2_array", "description"=>"");
$definitions["catalogage_plugins_grilles"][20]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_onglets/$nom_obj/unimarc_standard/informations/parametres/??intitule", "valeur"=>"bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/onglet_informations", "description"=>"");
$definitions["catalogage_plugins_grilles"][21]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_onglets/$nom_obj/unimarc_standard/informations/parametres/champs/!!01-200/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200", "description"=>"");

$definitions["catalogage_plugins_grilles"][22]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/chemin_fichier", "valeur"=>"div", "description"=>"");
$definitions["catalogage_plugins_grilles"][23]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/nom_fonction", "valeur"=>"plugins_2_array", "description"=>"");
$definitions["catalogage_plugins_grilles"][24]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/parametres/!!evenements/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_groupes_evenements/biblio/unimarc_standard/standard", "description"=>"");
$definitions["catalogage_plugins_grilles"][25]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/parametres/??intitule", "valeur"=>"bib/catalogue/catalogage/grilles/$nom_obj/unimarc_standard/ss_champ_200_a_description", "description"=>"");
$definitions["catalogage_plugins_grilles"][26]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/parametres/auto_plugin", "valeur"=>"catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a", "description"=>"");
$definitions["catalogage_plugins_grilles"][27]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/parametres/nom", "valeur"=>"a", "description"=>"");
$definitions["catalogage_plugins_grilles"][28]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_ss_champs/$nom_obj/unimarc_standard/200_a/parametres/type", "valeur"=>"textbox", "description"=>"");

$definitions["catalogage_plugins_grilles"][29]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/listes_masques/$nom_obj", "valeur"=>"", "description"=>"");
$definitions["catalogage_plugins_grilles"][30]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/masques/$nom_obj", "valeur"=>"", "description"=>"");
$definitions["catalogage_plugins_grilles"][31]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/parametrages/$nom_obj", "valeur"=>"", "description"=>"");

$definitions["catalogage_plugins_grilles"][32]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/catalogage/grilles", "description"=>"");
$definitions["catalogage_plugins_grilles"][33]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"switcher", "description"=>"");
$definitions["catalogage_plugins_grilles"][34]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_champ/ajouter_ss_champ/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/ajouter_ss_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][35]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_champ/descendre/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/monter_descendre_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][36]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_champ/descendre/parametres/sens", "valeur"=>"descendre", "description"=>"");
$definitions["catalogage_plugins_grilles"][37]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_champ/monter/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/monter_descendre_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][38]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_champ/monter/parametres/sens", "valeur"=>"monter", "description"=>"");
$definitions["catalogage_plugins_grilles"][39]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_champ/supprimer/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/suppression_element", "description"=>"");
$definitions["catalogage_plugins_grilles"][40]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_onglet/ajouter_champ/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/ajouter_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][41]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_ss_champ/descendre/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/monter_descendre_ss_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][42]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_ss_champ/descendre/parametres/sens", "valeur"=>"descendre", "description"=>"");
$definitions["catalogage_plugins_grilles"][43]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_ss_champ/monter/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/monter_descendre_ss_champ", "description"=>"");
$definitions["catalogage_plugins_grilles"][44]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_ss_champ/monter/parametres/sens", "valeur"=>"monter", "description"=>"");
$definitions["catalogage_plugins_grilles"][45]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_ss_champ/supprimer/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/suppression_element", "description"=>"");
$definitions["catalogage_plugins_grilles"][46]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/defaut_ss_champ/validation/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/simple_validation", "description"=>"");
$definitions["catalogage_plugins_grilles"][47]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/liste_champs/200/get_liste_ss_champs/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/get_liste_ss_champs", "description"=>"");
$definitions["catalogage_plugins_grilles"][48]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/liste_champs/200/get_liste_ss_champs/parametres/plugin_champ", "valeur"=>"catalogue/catalogage/definitions_champs/$nom_obj/unimarc_standard/200", "description"=>"");
$definitions["catalogage_plugins_grilles"][49]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/liste_formulaire/afficher_notice/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/afficher_formulator", "description"=>"");
$definitions["catalogage_plugins_grilles"][50]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/liste_formulaire/enregistrer_notice/nom_plugin", "valeur"=>"catalogue/catalogage/validation/$nom_obj/unimarc_standard", "description"=>"");
$definitions["catalogage_plugins_grilles"][51]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/liste_onglets/0/get_liste_champs/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/actions_grilles/get_liste_champs", "description"=>"");
$definitions["catalogage_plugins_grilles"][52]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/switchers/$nom_obj/unimarc_standard/parametres/liste_onglets/0/get_liste_champs/parametres/plugin_onglet", "valeur"=>"catalogue/catalogage/definitions_onglets/$nom_obj/unimarc_standard/informations", "description"=>"");

$definitions["catalogage_plugins_grilles"][53]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/type_2_grille/defaut/parametres/$nom_obj/grille", "valeur"=>"catalogue/catalogage/grilles/$nom_obj/unimarc_standard", "description"=>"");

$definitions["catalogage_plugins_grilles"][54]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/validation/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/catalogage/grilles", "description"=>"");
$definitions["catalogage_plugins_grilles"][55]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/validation/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"enregistrer_notice", "description"=>"");
$definitions["catalogage_plugins_grilles"][56]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/validation/$nom_obj/unimarc_standard/parametres/plugin_marcxml/nom_plugin", "valeur"=>"catalogue/catalogage/grilles/grille_marc_2_marcxml", "description"=>"");
$definitions["catalogage_plugins_grilles"][57]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/validation/$nom_obj/unimarc_standard/parametres/plugin_notice_2_db/nom_plugin", "valeur"=>"catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard", "description"=>"");

// patch unimarc_standard_modification
$definitions["catalogage_plugins_grilles"][58]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard_modification/chemin_fichier", "valeur"=>"catalogue/catalogage/grilles", "description"=>"");
$definitions["catalogage_plugins_grilles"][59]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard_modification/nom_fonction", "valeur"=>"add_ID_grille_modification", "description"=>"");
$definitions["catalogage_plugins_grilles"][60]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard_modification/parametres/plugin_definition_grille/nom_plugin", "valeur"=>"catalogue/catalogage/definitions_grilles/$nom_obj/unimarc_standard", "description"=>"");

$definitions["catalogage_plugins_db"]=array(); 
$definitions["catalogage_plugins_db"][0]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/get_liste_liens_explicites/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/marcxml/db", "description"=>""); 
$definitions["catalogage_plugins_db"][1]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/get_liste_liens_explicites/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"get_liste_liens_explicites", "description"=>""); 
$definitions["catalogage_plugins_db"][2]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/get_liste_liens_explicites/$nom_obj/unimarc_standard/parametres/!!champs_liens_explicites/nom_plugin", "valeur"=>"catalogue/marcxml/db/param_liste_liens_explicites/$nom_obj/unimarc_standard", "description"=>""); 

$definitions["catalogage_plugins_db"][3]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/maj_liens_implicites/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/marcxml/db", "description"=>""); 
$definitions["catalogage_plugins_db"][4]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/maj_liens_implicites/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"maj_liens_implicites", "description"=>""); 
$definitions["catalogage_plugins_db"][5]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/maj_liens_implicites/$nom_obj/unimarc_standard/parametres/champs_liens_implicites", "valeur"=>"", "description"=>""); 
$definitions["catalogage_plugins_db"][6]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/maj_liens_implicites/$nom_obj/unimarc_standard/parametres/plugin_get_champ_lie/nom_plugin", "valeur"=>"catalogue/marcxml/get_datafields_nodelist", "description"=>""); 
$definitions["catalogage_plugins_db"][7]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/maj_liens_implicites/$nom_obj/unimarc_standard/parametres/plugin_maj_lien_explicite/nom_plugin", "valeur"=>"catalogue/marcxml/db/maj_lien_explicite", "description"=>""); 

$definitions["catalogage_plugins_db"][8]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/marcxml/db", "description"=>""); 
$definitions["catalogage_plugins_db"][9]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"notice_2_db", "description"=>""); 
$definitions["catalogage_plugins_db"][10]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard/parametres/plugin_crea_notice/nom_plugin", "valeur"=>"catalogue/marcxml/db/crea_notice", "description"=>""); 
$definitions["catalogage_plugins_db"][11]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard/parametres/plugin_maj_liens_implicites/nom_plugin", "valeur"=>"catalogue/marcxml/db/maj_liens_implicites/$nom_obj/unimarc_standard", "description"=>""); 
$definitions["catalogage_plugins_db"][12]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard/parametres/plugin_notice_2_infos/nom_plugin", "valeur"=>"catalogue/marcxml/db/notice_2_infos/$nom_obj/unimarc_standard", "description"=>""); 
$definitions["catalogage_plugins_db"][13]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_db/$nom_obj/unimarc_standard/parametres/type_objet", "valeur"=>"$nom_obj", "description"=>""); 

$definitions["catalogage_plugins_db"][14]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_infos/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"catalogue/marcxml/db", "description"=>""); 
$definitions["catalogage_plugins_db"][15]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_infos/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"notice_2_infos", "description"=>""); 
$definitions["catalogage_plugins_db"][16]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_infos/$nom_obj/unimarc_standard/parametres/plugin_acces/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/acces/main/defaut", "description"=>""); 
$definitions["catalogage_plugins_db"][17]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_infos/$nom_obj/unimarc_standard/parametres/plugin_liens_explicites/nom_plugin", "valeur"=>"catalogue/marcxml/db/get_liste_liens_explicites/$nom_obj/unimarc_standard", "description"=>""); 
$definitions["catalogage_plugins_db"][18]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/notice_2_infos/$nom_obj/unimarc_standard/parametres/plugin_tri/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/tris/main/defaut", "description"=>""); 

$definitions["catalogage_plugins_db"][19]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/param_liens_explicites/$nom_obj/unimarc_standard", "valeur"=>"", "description"=>""); 

$definitions["catalogage_plugins_db"][20]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/param_liste_liens_explicites/$nom_obj/unimarc_standard/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["catalogage_plugins_db"][21]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/param_liste_liens_explicites/$nom_obj/unimarc_standard/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["catalogage_plugins_db"][22]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/db/param_liste_liens_explicites/$nom_obj/unimarc_standard/parametres", "valeur"=>"", "description"=>""); 

$definitions["catalogage_plugins_formatage"]=array(); 
$definitions["catalogage_plugins_formatage"][0]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/vedette/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][1]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/vedette/nom_fonction", "valeur"=>"get_datafields", "description"=>""); 
$definitions["catalogage_plugins_formatage"][2]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/vedette/parametres/champs/01 - 200/tag", "valeur"=>"200", "description"=>""); 
$definitions["catalogage_plugins_formatage"][3]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/vedette/parametres/champs/01 - 200/sous-champs/01 - a/code", "valeur"=>"a", "description"=>""); 

$definitions["catalogage_plugins_formatage"][4]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/main/defaut/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][5]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/main/defaut/nom_fonction", "valeur"=>"formate_plugins_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][6]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/acces/main/defaut/parametres/plugins/a_vedette/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/acces/vedette", "description"=>""); 

$definitions["catalogage_plugins_formatage"][7]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][8]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main/nom_fonction", "valeur"=>"get_colonnes_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][9]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main/parametres/colonnes/01 - vedette/nom_champ", "valeur"=>"nom", "description"=>""); 
$definitions["catalogage_plugins_formatage"][10]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main/parametres/colonnes/01 - vedette/nom_colonne", "valeur"=>"a_vedette", "description"=>""); 
$definitions["catalogage_plugins_formatage"][11]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main/parametres/colonnes/02 - ID/nom_champ", "valeur"=>"id", "description"=>""); 
// + patch nom colonne pour ID

$definitions["catalogage_plugins_formatage"][12]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/elem_notice/vedette/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][13]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/elem_notice/vedette/nom_fonction", "valeur"=>"get_datafields", "description"=>""); 
$definitions["catalogage_plugins_formatage"][14]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/elem_notice/vedette/parametres/champs/01 - 200/tag", "valeur"=>"200", "description"=>""); 
$definitions["catalogage_plugins_formatage"][15]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/elem_notice/vedette/parametres/champs/01 - 200/sous-champs/01 - a/code", "valeur"=>"a", "description"=>""); 

$definitions["catalogage_plugins_formatage"][16]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["catalogage_plugins_formatage"][17]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][18]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/parametres/??intitule", "valeur"=>"bib/catalogue/formatage/formats_liste/$nom_obj/tableau_standard", "description"=>""); 
$definitions["catalogage_plugins_formatage"][19]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/parametres/plugin_formate_liste/alias_retour/texte", "valeur"=>"/", "description"=>""); 
$definitions["catalogage_plugins_formatage"][20]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/parametres/plugin_formate_liste/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/liste/tableau_standard", "description"=>""); 
$definitions["catalogage_plugins_formatage"][21]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/parametres/plugin_formate_notice/alias_retour/texte", "valeur"=>"/", "description"=>""); 
$definitions["catalogage_plugins_formatage"][22]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/parametres/plugin_formate_notice/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard", "description"=>""); 
$definitions["catalogage_plugins_formatage"][23]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard/parametres/plugin_formate_notice/alias/ligne|xml", "valeur"=>"notice", "description"=>""); 

$definitions["catalogage_plugins_formatage"][24]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/liens_explicites", "valeur"=>"", "description"=>""); 

$definitions["catalogage_plugins_formatage"][25]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/liste/tableau_standard/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][26]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/liste/tableau_standard/nom_fonction", "valeur"=>"formate_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][27]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/liste/tableau_standard/parametres/avant", "valeur"=>"<table class='ln_liste_defaut'><tr><td><b>Vedette</b></td></tr>", "description"=>""); 

$definitions["catalogage_plugins_formatage"][28]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/listes_formats_liste/defaut/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["catalogage_plugins_formatage"][29]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/listes_formats_liste/defaut/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][30]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/listes_formats_liste/defaut/parametres/!!tableau_standard/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard", "description"=>""); 

$definitions["catalogage_plugins_formatage"][31]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/listes_formats_notice/defaut/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["catalogage_plugins_formatage"][32]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/listes_formats_notice/defaut/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][33]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/listes_formats_notice/defaut/parametres/!!tableau_standard/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/formats_liste/tableau_standard", "description"=>""); 

$definitions["catalogage_plugins_formatage"][34]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][35]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/nom_fonction", "valeur"=>"formate_plugins", "description"=>""); 
$definitions["catalogage_plugins_formatage"][36]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/parametres/plugins/0010 - obj_clic_avant/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/div/objet_cliquable_avant", "description"=>""); 
$definitions["catalogage_plugins_formatage"][37]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/parametres/plugins/0010 - obj_clic_avant/avant", "valeur"=>"<tr><td>", "description"=>""); 
$definitions["catalogage_plugins_formatage"][38]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/parametres/plugins/0020 - vedette/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/elem_notice/vedette", "description"=>""); 
$definitions["catalogage_plugins_formatage"][39]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/parametres/plugins/0030 - obj_clic_apres/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/div/objet_cliquable_apres", "description"=>""); 
$definitions["catalogage_plugins_formatage"][40]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/notice/tableau_standard/parametres/plugins/0030 - obj_clic_apres/apres", "valeur"=>"</td></tr>", "description"=>""); 


$definitions["catalogage_plugins_formatage"][41]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/tris/main/defaut/chemin_fichier", "valeur"=>"catalogue/marcxml", "description"=>""); 
$definitions["catalogage_plugins_formatage"][42]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/tris/main/defaut/nom_fonction", "valeur"=>"formate_plugins_array", "description"=>""); 
$definitions["catalogage_plugins_formatage"][43]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/tris/main/defaut/parametres/plugins/t_vedette/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/acces/vedette", "description"=>""); 

$definitions["catalogage_plugins_formatage"][44]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/formatage/formats_liste/$nom_obj/_intitules/tableau_standard/_fr", "valeur"=>"tableau standard", "description"=>""); 

// patch nom colonne pour ID dans autocomplete
$definitions["catalogage_plugins_formatage"][45]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main/parametres/colonnes/02 - ID/nom_colonne", "valeur"=>"ID", "description"=>""); 



$definitions["recherche_bib"]=array(); 
$definitions["recherche_bib"][0]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/standard/_page", "valeur"=>"bib/catalogue/recherches.php", "description"=>""); 
$definitions["recherche_bib"][1]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/plugin_get_formulaire_defaut/nom_plugin", "valeur"=>"catalogue/recherches/listes_criteres/$nom_obj/standard", "description"=>""); 
$definitions["recherche_bib"][2]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/plugin_get_liste_criteres/nom_plugin", "valeur"=>"catalogue/recherches/listes_criteres/$nom_obj/standard", "description"=>""); 
$definitions["recherche_bib"][3]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/plugin_get_liste_formats_liste/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/listes_formats_liste/defaut", "description"=>""); 
$definitions["recherche_bib"][4]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/plugin_get_liste_formats_notice/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/listes_formats_notice/defaut", "description"=>""); 
$definitions["recherche_bib"][5]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/plugin_get_liste_tris/nom_plugin", "valeur"=>"catalogue/recherches/listes_tris/$nom_obj/standard", "description"=>""); 
$definitions["recherche_bib"][6]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/titre_page", "valeur"=>"$nom_obj", "description"=>""); 
$definitions["recherche_bib"][7]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/tri_defaut", "valeur"=>"t_vedette", "description"=>""); 
$definitions["recherche_bib"][8]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/type_objet", "valeur"=>"$nom_obj", "description"=>""); 
$definitions["recherche_bib"][9]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/ws_url", "valeur"=>"bib_ws.php?module=catalogue/recherches/formulaires/$nom_obj/standard", "description"=>""); 
$definitions["recherche_bib"][10]=array("chemin"=>"profiles/defaut/pages/bib/catalogue/recherches/formulaires/$nom_obj/_parametres/mc_contexte", "valeur"=>"formulaires_recherche_$nom_obj", "description"=>""); 


$definitions["recherche_bib_ws"]=array(); 
$definitions["recherche_bib_ws"][0]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/recherches/formulaires/$nom_obj/standard/_page", "valeur"=>"bib_ws/catalogue/recherches/formulaires.php", "description"=>""); 
$definitions["recherche_bib_ws"][1]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/recherches/formulaires/$nom_obj/standard/_parametres/plugin_formulaire_2_recherche/nom_plugin", "valeur"=>"catalogue/recherches/modificateurs/biblio/standard", "description"=>""); 
$definitions["recherche_bib_ws"][2]=array("chemin"=>"profiles/defaut/pages/bib_ws/catalogue/recherches/formulaires/$nom_obj/standard/_parametres/plugin_recherche/nom_plugin", "valeur"=>"catalogue/recherches/recherche_simple", "description"=>""); 

$definitions["recherche_plugins"]=array(); 
$definitions["recherche_plugins"][1]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_mv/$nom_obj/standard/vedette/chemin_fichier", "valeur"=>"catalogue/recherches", "description"=>""); 
$definitions["recherche_plugins"][2]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_mv/$nom_obj/standard/vedette/nom_fonction", "valeur"=>"recherche_mv", "description"=>""); 
$definitions["recherche_plugins"][3]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_mv/$nom_obj/standard/vedette/parametres/nb_max", "valeur"=>"3", "description"=>""); 
$definitions["recherche_plugins"][4]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_mv/$nom_obj/standard/vedette/parametres/nb_resultats", "valeur"=>"10", "description"=>""); 
$definitions["recherche_plugins"][5]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_mv/$nom_obj/standard/vedette/parametres/plugin_formate/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/div/autocomplete_mv/standard", "description"=>""); 
$definitions["recherche_plugins"][6]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_mv/$nom_obj/standard/vedette/parametres/plugin_recherche/nom_plugin", "valeur"=>"catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/vedette", "description"=>""); 
$definitions["recherche_plugins"][7]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_paniers/$nom_obj/standard/chemin_fichier", "valeur"=>"catalogue/recherches", "description"=>""); 
$definitions["recherche_plugins"][8]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_paniers/$nom_obj/standard/nom_fonction", "valeur"=>"recherche_paniers", "description"=>""); 
$definitions["recherche_plugins"][9]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_paniers/$nom_obj/standard/parametres/type_obj", "valeur"=>"$nom_obj", "description"=>""); 
$definitions["recherche_plugins"][10]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_paniers/$nom_obj/standard/parametres/type_recherche", "valeur"=>"standard", "description"=>""); 
$definitions["recherche_plugins"][11]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/chemin_fichier", "valeur"=>"catalogue/recherches", "description"=>""); 
$definitions["recherche_plugins"][12]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/nom_fonction", "valeur"=>"recherche_simple", "description"=>""); 
$definitions["recherche_plugins"][13]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/bool_parse_contenu", "valeur"=>"0", "description"=>""); 
$definitions["recherche_plugins"][14]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/criteres/01 - a_vedette/##valeur_critere", "valeur"=>"query", "description"=>""); 
$definitions["recherche_plugins"][15]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/criteres/01 - a_vedette/intitule_critere", "valeur"=>"a_vedette", "description"=>""); 
$definitions["recherche_plugins"][16]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/criteres/01 - a_vedette/type_recherche", "valeur"=>"str_contient_commence", "description"=>""); 
$definitions["recherche_plugins"][17]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/format_resultat", "valeur"=>"formate", "description"=>""); 
$definitions["recherche_plugins"][18]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/plugin_formate_liste", "valeur"=>"", "description"=>""); 
$definitions["recherche_plugins"][19]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/plugin_formate_notice/alias/ligne", "valeur"=>"tableau", "description"=>""); 
$definitions["recherche_plugins"][20]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/plugin_formate_notice/alias_retour/texte", "valeur"=>"/", "description"=>""); 
$definitions["recherche_plugins"][21]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/plugin_formate_notice/nom_plugin", "valeur"=>"catalogue/marcxml/formatage/$nom_obj/autocomplete/standard/main", "description"=>""); 
$definitions["recherche_plugins"][22]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut/parametres/param_recherche/type_objet", "valeur"=>"$nom_obj", "description"=>""); 

$definitions["recherche_plugins"][23]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["recherche_plugins"][24]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["recherche_plugins"][25]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/!!booleens/nom_plugin", "valeur"=>"catalogue/recherches/booleens/standard", "description"=>""); 
$definitions["recherche_plugins"][26]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/!!icones/nom_plugin", "valeur"=>"catalogue/recherches/icones_criteres/defaut", "description"=>""); 
$definitions["recherche_plugins"][27]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/!!types_recherches/nom_plugin", "valeur"=>"catalogue/recherches/listes_types_recherches/defaut", "description"=>""); 
$definitions["recherche_plugins"][28]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/??critere_intitule", "valeur"=>"bib/catalogue/recherches/criteres/$nom_obj/standard/vedette", "description"=>""); 
$definitions["recherche_plugins"][29]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/autoplugin/nom_plugin", "valeur"=>"catalogue/recherches/criteres/$nom_obj/standard/vedette", "description"=>""); 
$definitions["recherche_plugins"][30]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/critere", "valeur"=>"a_vedette", "description"=>""); 
$definitions["recherche_plugins"][31]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/criteres/$nom_obj/standard/vedette/parametres/type_champ", "valeur"=>"textbox", "description"=>""); 

$definitions["recherche_plugins"][32]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/listes_criteres/$nom_obj/standard/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["recherche_plugins"][33]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/listes_criteres/$nom_obj/standard/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["recherche_plugins"][34]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/listes_criteres/$nom_obj/standard/parametres/!!01 - vedette/nom_plugin", "valeur"=>"catalogue/recherches/criteres/$nom_obj/standard/vedette", "description"=>""); 

$definitions["recherche_plugins"][35]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/listes_tris/$nom_obj/standard/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["recherche_plugins"][36]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/listes_tris/$nom_obj/standard/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["recherche_plugins"][37]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/listes_tris/$nom_obj/standard/parametres/!!01 - vedette/nom_plugin", "valeur"=>"catalogue/recherches/tris/$nom_obj/standard/vedette", "description"=>""); 

$definitions["recherche_plugins"][38]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/tris/$nom_obj/standard/vedette/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["recherche_plugins"][39]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/tris/$nom_obj/standard/vedette/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["recherche_plugins"][40]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/tris/$nom_obj/standard/vedette/parametres/??intitule", "valeur"=>"bib/catalogue/recherches/tris/$nom_obj/standard/vedette", "description"=>""); 
$definitions["recherche_plugins"][41]=array("chemin"=>"profiles/defaut/plugins/plugins/catalogue/recherches/tris/$nom_obj/standard/vedette/parametres/valeur", "valeur"=>"t_vedette", "description"=>""); 

$definitions["recherche_plugins"][42]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/recherches/criteres/$nom_obj/standard/_intitules/vedette/_fr", "valeur"=>"vedette", "description"=>""); 
$definitions["recherche_plugins"][43]=array("chemin"=>"profiles/defaut/langues/bib/catalogue/recherches/tris/$nom_obj/standard/_intitules/vedette/_fr", "valeur"=>"vedette", "description"=>""); 

$definitions["menus_contextuels"]=array(); 
$definitions["menus_contextuels"][0]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/defaut/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["menus_contextuels"][1]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/defaut/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["menus_contextuels"][2]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/defaut/parametres/menus/02 - cataloguer/??text", "valeur"=>"bib_ws/div/menus_contextuels/cataloguer", "description"=>""); 
$definitions["menus_contextuels"][3]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/defaut/parametres/menus/02 - cataloguer/onclick/fn", "valeur"=>"mc_cataloguer", "description"=>""); 
$definitions["menus_contextuels"][4]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/defaut/parametres/menus/02 - cataloguer/onclick/obj", "valeur"=>"", "description"=>""); 
$definitions["menus_contextuels"][5]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/chemin_fichier", "valeur"=>"div", "description"=>""); 
$definitions["menus_contextuels"][6]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/nom_fonction", "valeur"=>"plugins_2_array", "description"=>""); 
$definitions["menus_contextuels"][7]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/01 - voir/??text", "valeur"=>"bib_ws/div/menus_contextuels/voir", "description"=>""); 
$definitions["menus_contextuels"][8]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/01 - voir/onclick/fn", "valeur"=>"mc_voir", "description"=>""); 
$definitions["menus_contextuels"][9]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/01 - voir/onclick/obj", "valeur"=>"", "description"=>""); 
$definitions["menus_contextuels"][10]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/02 - cataloguer/??text", "valeur"=>"bib_ws/div/menus_contextuels/cataloguer", "description"=>""); 
$definitions["menus_contextuels"][11]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/02 - cataloguer/onclick/fn", "valeur"=>"mc_cataloguer", "description"=>""); 
$definitions["menus_contextuels"][12]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/02 - cataloguer/onclick/obj", "valeur"=>"", "description"=>""); 
$definitions["menus_contextuels"][13]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/03 - selectionner/??text", "valeur"=>"bib_ws/div/menus_contextuels/selectionner", "description"=>""); 
$definitions["menus_contextuels"][14]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/03 - selectionner/onclick/fn", "valeur"=>"mc_selectionner", "description"=>""); 
$definitions["menus_contextuels"][15]=array("chemin"=>"profiles/defaut/plugins/plugins/div/menus_contextuels/$nom_obj/formulaires_recherche_$nom_obj/parametres/menus/03 - selectionner/onclick/obj", "valeur"=>"", "description"=>""); 



$definitions["autocomplete_bib_ws"]=array(); 
$definitions["autocomplete_bib_ws"][0]=array("chemin"=>"profiles/defaut/pages/bib_ws/autocomplete/$nom_obj/standard/vedette/_page", "valeur"=>"bib_ws/catalogue/catalogage/recherches.php", "description"=>""); 
$definitions["autocomplete_bib_ws"][1]=array("chemin"=>"profiles/defaut/pages/bib_ws/autocomplete/$nom_obj/standard/vedette/_parametres/plugin_recherche/nom_plugin", "valeur"=>"catalogue/recherches/autocomplete/recherche_simple/$nom_obj/standard/defaut", "description"=>""); 



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    foreach ($definitions as $nom_action => $liste_actions) {
        if ($_REQUEST[$nom_action]=="1") {
            print ("<br><b><u>$nom_action</u></b><br>");
            foreach ($liste_actions as $definition) {
                $chemin=$definition["chemin"];
                $valeur=$definition["valeur"];
                $description=$definition["description"];
                $eval=set_registre ($chemin, $valeur, $description);
                if ($eval === false) {
                    print ("<b>ERREUR : </b>");
                }
                print ("$chemin => $valeur <br>");
            }
        }
    }
}


?>