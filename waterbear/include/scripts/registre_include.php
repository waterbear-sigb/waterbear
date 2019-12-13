<?php
/**
$ir_chemin="pages/$page_include/".$module."/_registre_include";
try {
    $ir_a_inclure=p_get_registre($ir_chemin);
    foreach ($ir_a_inclure as $ir_a_inclure_elem) {
        $ir_str_include=$GLOBALS["tvs_global"]["conf"]["ini"]["registre_compilation"]."/".$ir_a_inclure_elem.".php";
        tvs_log("registre_querys", "INCLUDE", $ir_str_include);
        include_once($ir_str_include);
    }
} catch (tvs_exception $e) {
    // on ne fait rien
}
**/


?>