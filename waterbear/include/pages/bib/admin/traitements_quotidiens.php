<?php

$traitements=$GLOBALS["affiche_page"]["parametres"]["traitements"];
$log=$GLOBALS["affiche_page"]["parametres"]["log"];
$log_erreurs=$GLOBALS["affiche_page"]["parametres"]["log_erreurs"];
$max_execution_time=$GLOBALS["affiche_page"]["parametres"]["max_execution_time"];
$plugin_mail=$GLOBALS["affiche_page"]["parametres"]["plugin_mail"];
$texte="";

$bool_lance_traitements=$_REQUEST["bool_lance_traitements"];

if ($bool_lance_traitements != 1) {
    $GLOBALS["affiche_page"]["template"]["tmpl_main"]=$GLOBALS["affiche_page"]["template"]["tmpl_main_validation"];
    affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());
    include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");
    die("");
}

$verrou_traitements=get_registre("system/verrous/traitements");
if ($verrou_traitements == 1) {
    $tmp=applique_plugin($plugin_mail, array("body"=>"Des traitements sont deja en cours d'execution", "sujet"=>"Erreur lors des traitements"));
    die ("ERREUR : base bloquee");    
}
set_registre("system/verrous/traitements", "1", "");

ini_set("max_execution_time", $max_execution_time);

tvs_log_txt ($log, array(0=>"lancement des traitements"));
$texte.="lancement des traitements<br>";
$bool_erreurs=0;

foreach ($traitements as $traitement) {
    tvs_log_txt ($log, array(0=>$traitement["nom_plugin"]));
    $texte.="<br><br>===================================================<br><br>";
    $texte.=$traitement["nom_plugin"]."<br>--------------------------<br>";
    $tmp=applique_plugin($traitement, array());
    if ($tmp["succes"] != 1) {
        $bool_erreurs=1;
        tvs_log_txt ($log, array(0=>$tmp["erreur"]));
        tvs_log_txt ($log_erreurs, array(0=>$tmp["erreur"]));
        $texte.=$tmp["erreur"]."<br>";
    } else {
        tvs_log_txt ($log, array(0=>$tmp["resultat"]["texte"]));
        $texte.=$tmp["resultat"]["texte"]."<br>";
    }
}


set_registre("system/verrous/traitements", "0", "");
if ($bool_erreurs == 1) {
    $tmp=applique_plugin($plugin_mail, array("body"=>"Des erreurs ont ete rencontrees durant les traitements. Consultez le log des erreurs", "sujet"=>"Erreur lors des traitements"));
}

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("texte"=>$texte, "bool_erreurs"=>$bool_erreurs)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");

?>