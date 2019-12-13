<?php

function plugin_catalogue_marcxml_test_xml ($parametres) {
    $plugin_crea=$parametres["plugin_crea"];
    $plugin_maj=$parametres["plugin_maj"];
    
    $tmp=applique_plugin ($plugin_crea, array());
    $notice=$tmp["resultat"]["notice"];
print ("NOTICE DE BASE <br><br>\n\n");
print($notice->saveXML());

    $tmp=applique_plugin ($plugin_maj, array("notice"=>$notice));
    $notice2=$tmp["resultat"]["notice"];
    
print ("\n\n<br><b>NOTICE MODIFIE <br><br>\n\n");
print($notice2->saveXML());

print ("\n\n<br><br><br>\n\n");
    
}


?>