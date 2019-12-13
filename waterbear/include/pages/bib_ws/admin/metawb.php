<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/metawb.php");

if ($operation=="master_exporte") {
    mwb_exporte (array("version"=>"???", "descriptif"=>"???"));
}


?>