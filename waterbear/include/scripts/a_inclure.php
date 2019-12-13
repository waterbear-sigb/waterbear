<?php
// Pour fonctionner il faut que le script qui appelle celui-ci ait défini la variable $page_include (bib, bib_ws, opac, opac_ws...)


$elements=explode("/", $_REQUEST["module"]);
$chemin="";
foreach ($elements as $element) {
    if ($chemin == "") {
        $chemin = $element;
    } else {
        $chemin .= "/".$element;
    }
    if (isset ($GLOBALS["tvs_global"]["conf"]["ini"]["a_inclure"][$page_include][$chemin])) {
        foreach ($GLOBALS["tvs_global"]["conf"]["ini"]["a_inclure"][$page_include][$chemin] as $a_inclure) {
            include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"].$a_inclure);
            
        }
    }
}



?>