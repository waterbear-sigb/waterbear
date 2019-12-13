<?php
// Ce script va inclure les fichiers JS et CSS nécessaires et paramétrés dans le registre
// Les clefs utilisées sont :
// _parametres/include_js => fichiers JS à inclure
// _parametres/include_css => fichiers CSS à inclure
// _parametres/include_js_perso => fichiers JS à inclure (propres au site (personnalisation))
// _parametres/include_css_perso => fichiers CSS à inclure (propres au site (personnalisation))

print ("\n<!--  Scripts CSS et JS inclus automatiquement et définis dans le registre  -->\n");
// CSS
$a_tester=array("include_css", "include_css_perso");
foreach ($a_tester as $chaine) {
    $tmp=$GLOBALS["affiche_page"]["template"][$chaine];
    if (is_array($tmp)) {
        print ("\n<!-- Scripts CSS déclarés dans le registre inclus automatiquement ($chaine) --> \n\n");
        foreach ($tmp as $tmp2) {
            print ("<link rel=\"stylesheet\" type=\"text/css\" href=\"$tmp2\" />\n");
            if ($_SESSION["system"]["skin"] != "" AND $_SESSION["system"]["skin"] != "defaut" AND substr($tmp2, 0, 3) == "css") {
                print ('<link rel="stylesheet" type="text/css" href="skins/'.$_SESSION["system"]["skin"].'/'.$tmp2.'"> \n');
            }
        }
    }
}


// Javascript
$a_tester=array("include_js", "include_js_perso");
foreach ($a_tester as $chaine) {
    $tmp=$GLOBALS["affiche_page"]["template"][$chaine];
    if (is_array($tmp)) {
        print ("\n<!-- Scripts JS déclarés dans le registre inclus automatiquement ($chaine) --> \n\n");
        foreach ($tmp as $tmp2) {
            print ("<script type=\"text/javascript\" src=\"$tmp2\"></script>\n");
        }
    }
}
print ("\n<!--  Fin de l'inclusion automatique des scripts CSS et JS  -->\n");

?>