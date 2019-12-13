<?php

//include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_formulator_server.php");

function plugin_catalogue_recherches_recherche_grille_auteurs ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["notices"]=array();

    $ID_operation=$_REQUEST["ID_operation"];
    $query=$_REQUEST["query"];
    
    // on analyse la chaine passée
    $pos0=strrpos($query, ","); // emplacement de la dernière virgule
    $pos1=strrpos($query, ";");
    if ($pos0 > $pos1) {
        $pos=$pos0;
    } else {
        $pos=$pos1;
    }
    if ($pos===false) {
        $racine="";
        $chaine=$query;
    } else {
        $racine=substr($query, 0, $pos+1);
        $chaine=substr($query, $pos+1, strlen($query)-($pos+1));
    }
    
    //dbg_log ("$racine - $chaine");
    
    
    $formulator=$tmp=$_SESSION["operations"][$ID_operation]["formulator"];
    $onglets=$formulator->onglets;
    foreach ($onglets as $onglet) {
        $champs=$onglet["champs"];
        foreach ($champs as $champ) {
            $nom_champ=$champ["nom"];
            $ss_champs=$champ["ss_champs"];
            if (substr($nom_champ, 0, 1)=="7") { // champs 7**
                $vedette="";
                $ss_champ_a="";
                $ss_champ_b="";
                $ss_champ_4="";
                $fonction="";
                foreach ($ss_champs as $ss_champ) {
                    $nom_ss_champ=$ss_champ["nom"];
                    $valeur_ss_champ=$ss_champ["valeur"];
                    if ($nom_ss_champ=="a") {
                        $ss_champ_a=$valeur_ss_champ;
                    } elseif ($nom_ss_champ=="b") {
                        $ss_champ_b=$valeur_ss_champ;
                    } elseif ($nom_ss_champ == "4") {
                        $ss_champ_4=$valeur_ss_champ;
                    }
                }
                
                
                if ($ss_champ_a != "") {
                    if ($ss_champ_4 != "" AND $ss_champ_4 != "710") {
                        $fonction=get_intitule("listes/catalogue/catalogage/grilles/biblio/intitules_fonctions", $ss_champ_4, array());
                    }
                    if (is_numeric($fonction)) {
                        $fonction="";
                    }
                    $vedette=implode(" ", array($fonction, $ss_champ_b, $ss_champ_a));
                    $vedette=trim($racine." ".$vedette);
                    if (stripos($vedette, $chaine) !== false OR $chaine == " ") {
                        array_push ($retour["resultat"]["notices"], array("nom"=>$vedette, "id"=>$vedette));
                    }
                }
            }
        }
    }
    //print_r($retour);
    
    return ($retour);
}

?>