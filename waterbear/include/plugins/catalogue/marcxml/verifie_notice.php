<?php

/**
 * plugin_catalogue_marcxml_verifie_notice()
 * 
 * Ce plugin vérifie la validité d'une notice marc au regard des champs / sous champs obligatoires et non répétables (todo)
 * 
 * il prend en paramètres la notice [notice] ou [tvs_marcxml]
 * les champs obligatoires sont fournis dans [obligatoires]
 * 
 * @param mixed $parametres
 * @return
 */
function plugin_catalogue_marcxml_verifie_notice($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $notice=$parametres["notice"];
    $obligatoires=$parametres["obligatoires"];
    $non_repetables=$parametres["non_repetables"];
    
    $message="";
    $bool_erreur=0;
    
    // on récupère $tvs_marcxml
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    // Contrôle des champs et ss-champs obligatoires
    foreach ($obligatoires as $nom_champ => $obligatoire) {
        //if (!is_array($obligatoire)) {
           // $nom_champ=$obligatoire;
        //}
        $liste_champs=$tvs_marcxml->get_champs($nom_champ, "");
        if (count($liste_champs) == 0) {
            $message.="champ $nom_champ obligatoire \n ";
            continue;
        }
        if (is_array($obligatoire)) {
            foreach ($obligatoire as $ss_champ_obligatoire => $bidon) {
                foreach ($liste_champs as $champ) {
                    $liste_ss_champs=$tvs_marcxml->get_ss_champs($champ, $ss_champ_obligatoire, "", "");
                    if (count($liste_ss_champs)==0) {
                        $message.="sous-champ $nom_champ - $ss_champ_obligatoire obligatoire \n ";
                    } else {
                        foreach ($liste_ss_champs as $ss_champ) {
                            $valeur=$tvs_marcxml->get_valeur_ss_champ($ss_champ);
                            if ($valeur == "") {
                                $message.="sous-champ $nom_champ - $ss_champ_obligatoire ne doit pas etre vide \n ";
                            }
                        }
                    }
                }
            }
        }
    }
    
    if ($message != "") {
        $bool_erreur=1;
    }
    $retour["resultat"]["message"]=$message;
    $retour["resultat"]["bool_erreur"]=$bool_erreur;
    return ($retour);
    
    
    
    
}



?>