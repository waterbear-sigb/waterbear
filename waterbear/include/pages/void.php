<?PHP
// Ce script g�n�raliste peut �tre utilis� pour appeler les templates 'statiques' c'est � dire ne n�cessitant pas
// de traitements PHP particuliers

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array());

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");







?>