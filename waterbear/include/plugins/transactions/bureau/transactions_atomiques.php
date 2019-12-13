<?php

function plugin_transactions_bureau_transactions_atomiques ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // Paramètres passés au script
    $mode=$parametres["mode"];
    $cab_lecteur=$parametres["cab_lecteur"];
    $cab_doc=$parametres["cab_doc"];
    $validation_message=$parametres["validation_message"];
    $bureau=$parametres["bureau"]; // ** option **

    $plugin_main=$parametres["plugin_main"]; // plugin à utiliser
    
        
    // On instancie le bureau
    if (! is_array($bureau)) {
        $bureau=array(); // Bureau
    }
    
    $bureau["param_script"]=array(); // paramètres fournis directement via le WS
    $bureau["param_script"]["mode"]=$mode;
    $bureau["param_script"]["validation_message"]=$validation_message;
    $bureau["commandes"]=array();
    $bureau["messages"]=array();
    $bureau["niveau_message"]=""; // on RAZ niveau message à chaque fois
    
    
    /////////////////////// RETOUR ////////////////////////////////////////////////////////////
    if ($mode=="retour") {
        $bureau["mode"]="retour";
        $bureau["param_script"]["cab"]=$cab_doc;
        $tmp=applique_plugin($plugin_main, array("bureau"=>$bureau));
        if ($tmp["succes"] == 0) {
            return($tmp);
        }
        $bureau=$tmp["resultat"]["bureau"];
        
    /////////////////////// PRET ////////////////////////////////////////////////////////////    
    } elseif ($mode=="pret") {
        // 1) récupère infos lecteur
        $bureau["mode"]="pret";
        
        // Validation message : ATTENTION pour le passage de la carte lecteur il ne faut PAS mettre validation message à oui, car en fait ça interrompt les traitements
        // il faut supprimer la validation puis à la fin du traitement mettre bureau[blocage_lecteur] à 0
        // ensuite on rétablit la validation (pour le prêt d'exemplaires)
        
        $bureau["param_script"]["validation_message"]="";
        // on passe la carte lecteur si on a pas un bureau avec déjà la même carte lecteur
        $bureau["param_script"]["cab"]=$cab_lecteur;
        if ($bureau["infos_lecteur"]["a_cab"] != $cab_lecteur) { // on ne fait ça que si un bureau a été fourni au plugin et que le n° de lecteur correspond
            $tmp=applique_plugin($plugin_main, array("bureau"=>$bureau));
            if ($tmp["succes"] == 0) {
                return($tmp);
            }
            $bureau=$tmp["resultat"]["bureau"];
        }
        if ($validation_message == "oui") {
            $bureau["blocage_lecteur"]=0;
        }
        $bureau["param_script"]["validation_message"]=$validation_message; // on réactive la validation une fois le passage de carte effectué (cf plus haut)
        
        
        
        
        // RAZ des commandes
        $bureau["commandes"]=array();
        
        // 2) on fait le prêt
        $bureau["param_script"]["cab"]=$cab_doc;
        $tmp=applique_plugin($plugin_main, array("bureau"=>$bureau));
        if ($tmp["succes"] == 0) {
           return($tmp);
        }
        $bureau=$tmp["resultat"]["bureau"];
    }


    $retour["resultat"]["commandes"]=$bureau["commandes"];
    $retour["resultat"]["messages"]=$bureau["messages"];
    $retour["resultat"]["arbre"]=$bureau["arbre"];
    $retour["resultat"]["bureau"]=$bureau;
    
    return ($retour);


}

?>