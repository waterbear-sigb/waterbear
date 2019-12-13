<?php

/**
 * plugin_catalogue_import_export_meta_format_tab2xml()
 * 
 * Ce plugin G�n�re une notice XML � partir d'une ligne de colonnes. Ces colonnes peuvent �tre s�par�es par des tabultations
 * ou par d'autres caract�res.
 * Le s�parateur est fourni soit dans [separateur] soit dans hex_separateur o� on met le code hexad�cimal du s�pareteur.
 * �a peut �tre plusieurs caract�res s�pas par des "|"
 * 
 * La structure des donn�es attendues est fournie dans [liste_colonnes] qui a la forme :
 * [0,1,2...][nom]=>nom de la colonne
 *           [plugin_formate] ** option ** peut permettre de formater la valeur  chaine => [plugin_formate] => chaine
 *           [defaut] valeur par d�faut 
 * On utilise ensuite le plugin [plugin_crea_notice] pour g�n�rer la notice adns laquelle on injecte les donn�es des colonnes
 * � utiliser en variables incluses via param_colonnes/xxx
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_import_export_meta_format_tab2xml ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    
    $notice=$parametres["notice"];
    
    $plugin_crea_notice=$parametres["plugin_crea_notice"];
    $liste_colonnes=$parametres["liste_colonnes"];
    $separateur=$parametres["separateur"];
    $hex_separateur=$parametres["hex_separateur"];
    $import_options=$parametres["import_options"];
    $bool_utf8=$parametres["bool_utf8"]; // si vaut 1 on doit convertir les colonnes en utf8
    
    //
    if ($hex_separateur != "") {
        $separateur="";
        $tmp=explode("|",$hex_separateur);
        foreach ($tmp as $car) {
            $separateur.=chr($car);
        }
    }
    
    // on calcule le nb de colonnes attendu
    $nb_col=count($liste_colonnes);
    
    $tmp=explode($separateur, $notice);
    
    // 1) on v�rifie qu'on a le bon nombre de colonnes
    $nb_col2=count($tmp);
    if ($nb_col2 != $nb_col) {
        $retour["succes"]=0;
        $retour["erreur"]="ERREUR $nb_col2 colonnes dans la notice($nb_col attendues)";
    }
    
    // 2) On g�n�re l'array de param�tres
    $param_colonnes=array();
    foreach ($liste_colonnes as $idx_col => $parm_col) {
        $nom_col=$parm_col["nom"];
        if ($nom_col=="") {
            $nom_col="parm_".$idx_col;
        }
        $plugin_formate_col=$parm_col["plugin_formate"];
        $valeur_defaut_col=$parm_col["defaut"];
        $valeur_col=trim($tmp[$idx_col]);
        $valeur_col=str_replace('"', '', $valeur_col);
        if ($bool_utf8 == 1) {
            $valeur_col=utf8_encode($valeur_col);
        }
        if ($valeur_col === "" AND $valeur_defaut_col !== "") {
            $valeur_col=$valeur_defaut_col;
        }
        if (is_array($plugin_formate_col)) {
            $toto=applique_plugin($plugin_formate_col, array("chaine"=>$valeur_col));
            if ($toto["succes"] != 0) {
                $valeur_col=$toto["resultat"]["chaine"];
            }
        }
        $param_colonnes[$nom_col]=$valeur_col;
    } // fin du pour chaque colonne...
    
    // 3) On cr�e l'objet
    $toto=applique_plugin($plugin_crea_notice, array("param_colonnes"=>$param_colonnes, "import_options"=>$import_options));
    
    return($toto);
    



}
?>