<?php

function plugin_catalogue_import_export_meta_format_xml2marc ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    $retour["resultat"]["nb_encode"]=0;

    

    
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $champs_codes=$parametres["champs_codes"];
    $plugin_datafield_2_controlfield=$parametres["plugin_datafield_2_controlfield"];
    $plugin_encodage=$parametres["plugin_encodage"];
    
    $array_marc=array();
    $array_marc["champs"]=array();
    $array_marc["label"]="";// TODO mettre une valeur par défaut
    
    // 1) on récupère la liste des champs
    $liste_champs=$tvs_marcxml->get_champs("", "");
    foreach ($liste_champs as $champ) { // pour chaque champ...
        $nom_champ=$tvs_marcxml->get_nom_champ($champ);

        if ($nom_champ == "label") { // Si label
            $tmp=applique_plugin($plugin_datafield_2_controlfield, array("tvs_marcxml"=>$tvs_marcxml, "champ"=>$champ, "definition"=>$champs_codes["label"]));
            $array_marc["label"]=$tmp["resultat"]["chaine"];
        } elseif (isset($champs_codes[$nom_champ])) { // si champ (ou ss-champ) codé)
            $tmp=applique_plugin($plugin_datafield_2_controlfield, array("tvs_marcxml"=>$tvs_marcxml, "champ"=>$champ, "definition"=>$champs_codes[$nom_champ]));
            $chaine=$tmp["resultat"]["chaine"];
            $id1=$tmp["resultat"]["id1"];
            $id2=$tmp["resultat"]["id2"];
            $array_champ=array("nom"=>$nom_champ, "ss_champs"=>array(), "id1"=>" ", "id2"=>" ", "valeur"=>"");
            if ($id1 != "") {
                $array_champ["id1"]=$id1;
            }
            if ($id2 != "") {
                $array_champ["id2"]=$id2;
            }
            $nom_ss_champ=$champs_codes[$nom_champ]["nom_ss_champ"];
            if ($nom_ss_champ == "") {
                $array_champ["valeur"]=$chaine;
            } else {
                array_push($array_champ["ss_champs"], array("nom"=>$nom_ss_champ, "valeur"=>$chaine));
            }
            array_push($array_marc["champs"], $array_champ);
            
        }else { // champ classique
            $array_champ=array("nom"=>$nom_champ, "ss_champs"=>array(), "id1"=>" ", "id2"=>" ", "valeur"=>"");
            $liste_ss_champs=$tvs_marcxml->get_ss_champs($champ, "", "", "");
            foreach ($liste_ss_champs as $ss_champ) { // pour chaque ss-champ...
                $nom_ss_champ=$tvs_marcxml->get_nom_ss_champ($ss_champ);
                $valeur_ss_champ=$tvs_marcxml->get_valeur_ss_champ($ss_champ);
                
                if ($plugin_encodage != "" AND $valeur_ss_champ != "") {
                    $retour["resultat"]["nb_encode"]++;
                    $tmp=applique_plugin($plugin_encodage, array("chaine"=>$valeur_ss_champ));
                    if ($tmp["succes"]==1) {
                        $valeur_ss_champ=$tmp["resultat"]["chaine"];
                    }
                }
                
                
                
                            
                
                if ($nom_ss_champ == "id1") {
                    $array_champ["id1"]=$valeur_ss_champ;
                } elseif ($nom_ss_champ == "id2") {
                    $array_champ["id2"]=$valeur_ss_champ;
                } else {
                    array_push($array_champ["ss_champs"], array("nom"=>$nom_ss_champ, "valeur"=>$valeur_ss_champ));
                }
            } // fin du pour chaque ss-champ
            array_push($array_marc["champs"], $array_champ);
        }
       
        
    } // ...fin du pour chaque champ
    
    
    $retour["resultat"]["notice"]=$array_marc;
    return($retour);   
    
}


?>