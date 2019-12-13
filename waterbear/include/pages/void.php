<?PHP
// Ce script généraliste peut être utilisé pour appeler les templates 'statiques' c'est à dire ne nécessitant pas
// de traitements PHP particuliers

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");







?>