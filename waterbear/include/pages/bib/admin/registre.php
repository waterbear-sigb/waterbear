<?PHP

$acces_direct=$_REQUEST["acces_direct"];


affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_js"=>array("acces_direct"=>$acces_direct)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");







?>