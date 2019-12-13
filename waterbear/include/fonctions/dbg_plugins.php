<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CREATE TABLE dbgplugins_plugins (ID_script VARCHAR(250), ID_plugin VARCHAR(250), idx INT, nom TEXT, nom_parent TEXT, parent VARCHAR(250), type_plugin VARCHAR(250), PA_init TEXT, parametres_script_init TEXT, parametres_PA_init TEXT, parametres_plugin_init TEXT, type_PA VARCHAR(250), a_inclure TEXT, a_appeler TEXT, param_merge_init TEXT, param_merge_alias TEXT, param_merge_var_inc TEXT, param_merge_plugin_inclus TEXT, retour TEXT, retour_alias TEXT, duree VARCHAR(250), erreur TEXT);
// CREATE TABLE dbgplugins_lnk_plugins (ID_script VARCHAR(250), ID_plugin_parent VARCHAR(250), ID_plugin_enfant VARCHAR(250), type VARCHAR(250), ordre INT);
// CREATE TABLE dbgplugins_alias (ID_script VARCHAR(250), ID_plugin VARCHAR(250), clef VARCHAR(250), old_variable TEXT, new_variable TEXT);
// CREATE TABLE dbgplugins_var_inc (ID_script VARCHAR(250), ID_plugin VARCHAR(250), nom_var TEXT, chemin TEXT, valeur TEXT);
// CREATE TABLE dbgplugins_scripts (ID_script VARCHAR(250), nom TEXT, moment VARCHAR(250));

function dbg_plugins_bool () {
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"]["DBGPLUGINS"]["bool"] == 1) {
        return (true);
    }
    return (false);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_init ($page) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    if ($_REQUEST["module"]=="admin/registre" OR $_REQUEST["module"]=="admin/dbg_plugins") {
        return ("");
    }
    $nom=$page.".php?".$_SERVER["QUERY_STRING"];
    $GLOBALS["tvs_global"]["dbg_plugins"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["ID_script"]=get_id_operation();
    $GLOBALS["tvs_global"]["dbg_plugins"]["idx_plugin"]=0;
    $GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"]=array();
    $ID_script=$GLOBALS["tvs_global"]["dbg_plugins"]["ID_script"];
    $moment=microtime(true);
    $sql="insert into dbgplugins_scripts values ('$ID_script', '$nom', '$moment')";
    sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_init"));
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_init_plugin ($PA, $parametres, $type_plugin) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    $now=microtime(true);
    GLOBAL $json;
    $ID_plugin=get_id_operation();
    $nom=$PA["nom_plugin"];
    if ($nom == "") {
        $nom="????????????";
    }
    $GLOBALS["tvs_global"]["dbg_plugins"]["idx_plugin"]++;
    $idx=$GLOBALS["tvs_global"]["dbg_plugins"]["idx_plugin"];
    $pile_length=count($GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"]);
    if ($pile_length==0) {
        $parent=0;
        $nom_parent="root";
        $type_plugin="root";
    } else {
        $parent=$GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"][$pile_length-1];
        $nom_parent=$GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$parent]["nom"];
    }
    if ($type_plugin == "") {
        $type_plugin="ss_plugin";
    }
    $str_PA=$json->encode($PA);
    $str_parametres=$json->encode($parametres);
    
    // On initialise les variables principales;
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]=array("ID_plugin"=>$ID_plugin, "parent"=>$parent, "type_plugin"=>$type_plugin, "PA_init"=>$str_PA, "parametres_script_init"=>$str_parametres, "idx"=>$idx, "nom"=>$nom, "nom_parent"=>$nom_parent, "debut"=>$now);
    
    
    // Si parent, on ajoute à la liste des enfants
    if ($type_plugin=="plugin_inclus") {
        array_push ($GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"][$pile_length-1]]["plugins_inclus"], $ID_plugin);
    } elseif ($type_plugin=="ss_plugin") {
        array_push ($GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"][$pile_length-1]]["ss_plugins"], $ID_plugin);
    }
    
    // On initialise les autres variables
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["ss_plugins"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["plugins_inclus"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["parametres_PA_init"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["parametres_plugin_init"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["type_PA"]="";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["a_inclure"]="";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["a_appeler"]="";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["param_merge_init"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["alias"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["alias_retour"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["param_merge_alias"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["var_inc"]=array();
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["param_merge_var_inc"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["param_merge_plugin_inclus"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["retour"]="[]";
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["retour_alias"]="[]"; // retour après les alias_retour
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["erreur"]=""; 
    
    // On rajoute à la pile
    array_push ($GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"], $ID_plugin);
    
    return ($ID_plugin);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_set_erreur ($ID_plugin, $erreur) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["erreur"]=utf8_encode($erreur);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_set_params_init ($ID_plugin, $parametres_PA_init, $parametres_plugin_init, $type_PA, $a_inclure, $a_appeler) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    GLOBAL $json;
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["parametres_PA_init"]=$json->encode($parametres_PA_init);
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["parametres_plugin_init"]=$json->encode($parametres_plugin_init);
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["type_PA"]=$json->encode($type_PA);
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["a_inclure"]=$json->encode($a_inclure);
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["a_appeler"]=$json->encode($a_appeler);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_set_params_merge ($ID_plugin, $parametres, $clef) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    GLOBAL $json;
    $GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin][$clef]=$json->encode($parametres);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $clef = alias ou alias_retour

function dbg_plugins_set_alias ($ID_plugin, $old_variable, $new_variable, $clef) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    array_push ($GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin][$clef], array("old_variable" => $old_variable, "new_variable" => $new_variable));
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $clef = alias ou alias_retour

function dbg_plugins_set_var_inc ($ID_plugin, $nom_var, $chemin, $valeur) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    GLOBAL $json;
    array_push ($GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["var_inc"], array("nom_var"=>$nom_var, "chemin"=>$chemin, "valeur"=>$json->encode($valeur)));
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_end_plugin ($ID_plugin) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    $now=microtime(true);
    $debut=$GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["debut"];
    $duree=$now-$debut;
    $debut=$GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]["duree"]=$duree;
    GLOBAL $json;
    dbg_plugins_plugin_2_sql ($ID_plugin);
    array_pop($GLOBALS["tvs_global"]["dbg_plugins"]["pile_plugins"]);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_plugin_2_sql ($ID_plugin) {
    if (! dbg_plugins_bool() ) {
        return (false);
    }
    $tmp=secure_sql($GLOBALS["tvs_global"]["dbg_plugins"]["infos_plugins"][$ID_plugin]);
    extract ($tmp);
    $ID_script=$GLOBALS["tvs_global"]["dbg_plugins"]["ID_script"];
    
    // le plugin
    $sql="INSERT INTO dbgplugins_plugins VALUES('$ID_script', '$ID_plugin', $idx, '$nom', '$nom_parent', '$parent', '$type_plugin', '$PA_init', '$parametres_script_init', '$parametres_PA_init', '$parametres_plugin_init', '$type_PA', '$a_inclure', '$a_appeler', '$param_merge_init', '$param_merge_alias', '$param_merge_var_inc', '$param_merge_plugin_inclus', '$retour', '$retour_alias', '$duree', '$erreur')";
    sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::plugin_2_sql() - plugin"));
    
    // les sous-plugins et plugins inclus
    foreach ($ss_plugins as $idx_plugin => $ss_plugin) {
        $sql="INSERT INTO dbgplugins_lnk_plugins VALUES ('$ID_script', '$ID_plugin', '$ss_plugin', 'ss_plugin', $idx_plugin)";
        sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::plugin_2_sql() - ss-plugin"));
    }
    
    foreach ($plugins_inclus as $idx_plugin => $ss_plugin) {
        $sql="INSERT INTO dbgplugins_lnk_plugins VALUES ('$ID_script', '$ID_plugin', '$ss_plugin', 'plugin_inclus', $idx_plugin)";
        sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::plugin_2_sql() - plugin_inclus"));
    }
    
    // alias
    foreach ($alias as $alias_elem) {
        $old_variable=$alias_elem["old_variable"];
        $new_variable=$alias_elem["new_variable"];
        $sql="INSERT INTO dbgplugins_alias VALUES ('$ID_script', '$ID_plugin', 'alias', '$old_variable', '$new_variable')";
        sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::plugin_2_sql() - alias"));
    }
    
    // alias retour
    foreach ($alias_retour as $alias_elem) {
        $old_variable=$alias_elem["old_variable"];
        $new_variable=$alias_elem["new_variable"];
        $sql="INSERT INTO dbgplugins_alias VALUES ('$ID_script', '$ID_plugin', 'alias_retour', '$old_variable', '$new_variable')";
        sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::plugin_2_sql() - alias"));
    }
    
    // var inc
    foreach ($var_inc as $var_inc_elem) {
        $nom_var=$var_inc_elem["nom_var"];
        $chemin=$var_inc_elem["chemin"];
        $valeur=$var_inc_elem["valeur"];
        $sql="INSERT INTO dbgplugins_var_inc VALUES ('$ID_script', '$ID_plugin', '$nom_var', '$chemin', '$valeur')";
        sql_query(array("sql"=>$sql, "contexte"=>"dbg_plugins::plugin_2_sql() - var_inc"));
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// FONCTIONS CLIENT

function dbg_plugins_client_get_scripts () {
    $sql="select * from dbgplugins_scripts order by moment DESC";
    $resultat=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_scripts ()"));
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=$resultat;
    return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_client_delete_historique () {
    $sql=array();
    $sql[0]="delete from dbgplugins_scripts";
    $sql[1]="delete from dbgplugins_plugins";
    $sql[2]="delete from dbgplugins_lnk_plugins";
    $sql[3]="delete from dbgplugins_alias";
    $sql[4]="delete from dbgplugins_var_inc";
    
    foreach ($sql as $elem) {
        sql_query(array("sql"=>$elem, "contexte"=>"dbg_plugins::dbg_plugins_client_delete_historique ()"));
    }
    
    return (array("succes"=>1, "resultat"=>"OK"));
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function dbg_plugins_client_get_script ($ID_script) {
    $sql="select * from dbgplugins_plugins where ID_script='$ID_script' AND parent='0' order by idx";
    $resultat=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_scripts ()"));
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=$resultat;
    return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function dbg_plugins_client_get_plugin ($ID_script, $ID_plugin) {
    
    // infos
    $sql="select * from dbgplugins_plugins where ID_script='$ID_script' AND ID_plugin='$ID_plugin'";
    $tmp=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_plugin ()"));
    $infos=$tmp[0];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["infos"]=$infos;
    
    // ss_plugins
    $sql="select dbgplugins_plugins.ID_plugin, dbgplugins_plugins.nom from dbgplugins_lnk_plugins, dbgplugins_plugins where dbgplugins_lnk_plugins.ID_script='$ID_script' AND dbgplugins_lnk_plugins.ID_plugin_parent = '$ID_plugin' AND dbgplugins_lnk_plugins.type = 'ss_plugin' AND dbgplugins_lnk_plugins.ID_plugin_enfant = dbgplugins_plugins.ID_plugin AND dbgplugins_plugins.ID_script='$ID_script' order by dbgplugins_lnk_plugins.ordre";
    $ss_plugins=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_plugin ()"));
    $retour["resultat"]["ss_plugins"]=$ss_plugins;
    
    // plugins inclus
    $sql="select dbgplugins_plugins.ID_plugin, dbgplugins_plugins.nom from dbgplugins_lnk_plugins, dbgplugins_plugins where dbgplugins_lnk_plugins.ID_script='$ID_script' AND dbgplugins_lnk_plugins.ID_plugin_parent = '$ID_plugin' AND dbgplugins_lnk_plugins.type = 'plugin_inclus' AND dbgplugins_lnk_plugins.ID_plugin_enfant = dbgplugins_plugins.ID_plugin AND dbgplugins_plugins.ID_script='$ID_script' order by dbgplugins_lnk_plugins.ordre";
    $plugins_inclus=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_plugin ()"));
    $retour["resultat"]["plugins_inclus"]=$plugins_inclus;
    
    // alias
    $sql="select * from dbgplugins_alias where ID_script='$ID_script' AND ID_plugin='$ID_plugin' AND clef='alias'";
    $alias=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_plugin ()"));
    $retour["resultat"]["alias"]=$alias;
    
    // alias_retour
    $sql="select * from dbgplugins_alias where ID_script='$ID_script' AND ID_plugin='$ID_plugin' AND clef='alias_retour'";
    $alias_retour=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_plugin ()"));
    $retour["resultat"]["alias_retour"]=$alias_retour;
    
    // alias_retour
    $sql="select * from dbgplugins_var_inc where ID_script='$ID_script' AND ID_plugin='$ID_plugin'";
    $var_inc=sql_as_array(array("sql"=>$sql, "contexte"=>"dbg_plugins::dbg_plugins_client_get_plugin ()"));
    $retour["resultat"]["var_inc"]=$var_inc;
    
    
    return ($retour);
}
?>