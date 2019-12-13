<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_paniers.php"); // gestion des paniers

$retour=array();
$retour["succes"]=1;
$retour["resultat"]="";



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// valide_formulaire
if ($operation == "valide_formulaire") {
    $action_panier=$_REQUEST["action_panier"];
    $ID_panier=$_REQUEST["ID_panier"];
    $geolocalisation=$_REQUEST["geolocalisation"];
    $bool_exporter=$_REQUEST["bool_exporter"];
    $bool_telecharger=$_REQUEST["bool_telecharger"];
    
    // 1) On récupère les paramètres de recherche
    $param_recherche=$json->decode($_REQUEST["param_recherche"]);
    
    // 2) Eventuellement, on modifie (ou enrichit) les paramètres
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire_2_recherche"], array("array_in"=>$param_recherche)); // on modifie éventuellement les paramètres de recherche
    if ($tmp["succes"] != 1) {
        $output = $json->encode($retour);
        print($output);
        die();
    }
    $param_recherche=$tmp["resultat"]["array_out"];
    
    // 2 bis) si bool_exporter=1, on désactive la limitation de la pagination
    if ($bool_exporter == 1) {
        $param_recherche["nb_notices_par_page"]=10000000;
    }
    
    if ($geolocalisation == "1") {
        // Todo : enlever la pagination
        unset($param_recherche["page"]);
        unset($param_recherche["tris"]);
        $param_recherche["bool_parse_contenu"]=0;
        $param_recherche["plugin_formate_liste"]=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_liste_geolocalisation"];
        $param_recherche["plugin_formate_notice"]=$GLOBALS["affiche_page"]["parametres"]["plugin_formate_notice_geolocalisation"];
        $param_recherche["tris"]=array();
        $param_recherche["tris"][0]="a_coordonnees";
    }
    
    // 3) Si action panier, on supprime les paramètres de tri, page...
    if ($action_panier != "") {
        unset($param_recherche["tris"]);
        unset($param_recherche["page"]);
        $param_recherche["format_resultat"]="liste";
        $param_recherche["bool_parse_contenu"]=0;
    }
    
    // 4) On lance la recherche
    $retour=applique_plugin($GLOBALS["affiche_page"]["parametres"]["plugin_recherche"], array("param_recherche"=>$param_recherche));
    
    
    // 4 bis) si exporter, on affiche tout de suite le résultat
    
    if ($bool_exporter == 1) {
        
        if ($bool_telecharger == 1) {
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=waterbear.htm");
        } else {
            //header('Content-type: text/html; charset=UTF-8');
        }

        if (substr($retour["resultat"]["notices"], 0, 1) == "<" AND substr($retour["resultat"]["notices"], -1, 1) == ">") { // du html ou de l'xml en utf8
            header('Content-type: text/html; charset=UTF-8');
            $retour["resultat"]["notices"]="<html><head><meta http-equiv='Content-type' content='text/html; charset=UTF-8'/></head><body>".$retour["resultat"]["notices"]."</body></html>";
        } else { // de l'unimarc pas en utf8'
            // on ne modifie rien
        }
        
        // options d'export spécifiques à un format de liste donné (pour export en .pan, .xls, .txt, .htm...)
        if (is_array($param_recherche["plugin_formate_liste"]["parametres"]["infos_export"]["html_headers"])) {
            foreach ($param_recherche["plugin_formate_liste"]["parametres"]["infos_export"]["html_headers"] as $nom_header => $valeur_header) {
                if ($nom_header != "" AND $valeur_header != "") {
                    header("$nom_header: $valeur_header");
                }
            }
        }

        print ($retour["resultat"]["notices"]);
        die();
    }
   
    // 6) Si action_panier...
    if ($action_panier == "add_statique") {
        $obj_paniers=new tvs_paniers();
        $retour=$obj_paniers->add_statique($ID_panier, $retour["resultat"]["notices"]);
    } elseif ($action_panier == "remove_statique") {
        $obj_paniers=new tvs_paniers();
        $retour=$obj_paniers->remove_statique($ID_panier, $retour["resultat"]["notices"]);
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// affiche_notice
} elseif ($operation == "affiche_notice") {
    $type_obj=$_REQUEST["type_objet"];
    $ID=$_REQUEST["ID"];
    $plugin_formate_notice=$json->decode($_REQUEST["plugin_formate_notice"]);
    $notice=get_objet_by_id($type_obj, $ID);
    if ($notice=="") {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("plugins/catalogue/marcxml/db/crea_notice", "notice_inexistante", array("type"=>$type_obj, "ID"=>$ID));
        $output = $json->encode($retour);
        print($output);
        die();
    }
    $contenu=$notice["contenu"];
    $objet_xml=new DOMDocument();
    $objet_xml->preserveWhiteSpace = false;
    $objet_xml->loadXML($contenu);
    $notice["xml"]=$objet_xml;
    $tmp=applique_plugin($plugin_formate_notice, array("ligne"=>$notice));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die();
    }
    $retour["resultat"]["notice"]=$tmp["resultat"];  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// affiche_notice_idx (rang de la notice dans une liste de résultat)
} elseif ($operation == "affiche_notice_idx") {
    // 1) On récupère les paramètres de recherche (on ne les modifie pas)
    $param_recherche=$json->decode($_REQUEST["param_recherche"]);
    // 2) Eventuellement, on modifie (ou enrichit) les paramètres
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire_2_recherche"], array("array_in"=>$param_recherche)); // on modifie éventuellement les paramètres de recherche
    if ($tmp["succes"] != 1) {
        $output = $json->encode($retour);
        print($output);
        die();
    }
    $param_recherche=$tmp["resultat"]["array_out"];
    $param_recherche["nb_notices_par_page"]=1;
    // 2) On lance la recherche
    $retour=applique_plugin($GLOBALS["affiche_page"]["parametres"]["plugin_recherche"], array("param_recherche"=>$param_recherche));
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Retourne l'ID d'une notice à partir de son idx
} elseif ($operation == "get_ID_by_idx") {
    $param_recherche=$json->decode($_REQUEST["param_recherche"]);
    // 2) Eventuellement, on modifie (ou enrichit) les paramètres
    $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_formulaire_2_recherche"], array("array_in"=>$param_recherche)); // on modifie éventuellement les paramètres de recherche
    if ($tmp["succes"] != 1) {
        $output = $json->encode($retour);
        print($output);
        die();
    }
    $param_recherche=$tmp["resultat"]["array_out"];
    $param_recherche["nb_notices_par_page"]=1;
    $tmp=applique_plugin($GLOBALS["affiche_page"]["parametres"]["plugin_recherche"], array("param_recherche"=>$param_recherche));
    $tmp2=$tmp["resultat"]["notices"];
    $ID=str_replace(" , ", "", $tmp2);
    $retour["resultat"]["ID"]=$ID;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Statistiques
} elseif ($operation == "statistiques") {
    $param=$json->decode($_REQUEST["param"]);
    $retour=applique_plugin($GLOBALS["affiche_page"]["parametres"]["plugin_stats"], array("param"=>$param));

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche panier
} elseif ($operation == "affiche_panier") {
    $ID_panier=$_REQUEST["ID_panier"];
    $obj_paniers=new tvs_paniers();
    $panier=$obj_paniers->get_panier_by_ID($ID_panier);
    $type=$panier["type"];
    $contenu=$panier["contenu"];
    $nom_complet=$panier["nom"];
    if ($panier["chemin_parent"] != "") {
        $nom_complet=$panier["chemin_parent"]."/".$nom_complet;
    }
    $contenu=$json->decode($contenu);
    if ($type == "statique") {
        $tmp=applique_plugin ($GLOBALS["affiche_page"]["parametres"]["plugin_critere_panier"], array());
        if ($tmp["succes"] != 1) {
            $output = $json->encode($tmp);
            print($output);
            die();
        }
        $critere_formulaire=$tmp["resultat"];
        $critere_formulaire["valeur"]=$nom_complet;
        $retour["resultat"]["formulaire"]=array("01 - panier"=>$critere_formulaire);
    } elseif ($type == "dynamique") {
        $formulaire=array();
        $formulaire_stat=array();
        // pour formulaire recherche
        foreach ($contenu["recherchator"]["criteres"] as $critere_panier) {
            $autoplugin=$critere_panier["autoplugin"];
            $type_recherche=$critere_panier["type_recherche"];
            $booleen=$critere_panier["booleen"];
            $valeur=$critere_panier["valeur_critere"];
            $tmp=applique_plugin($autoplugin, array());
            if ($tmp["succes"] != 1) {
                $output = $json->encode($tmp);
                print($output);
                die();
            }
            $critere_formulaire=$tmp["resultat"];
            $critere_formulaire["type_recherche"]=$type_recherche;
            $critere_formulaire["booleen"]=$booleen;
            $critere_formulaire["valeur"]=$valeur;
            array_push ($formulaire, $critere_formulaire);
        }
        // pour formulaire stats
        
        if (is_array($contenu["statator"]["criteres"])) {
            foreach ($contenu["statator"]["criteres"] as $critere_panier) {
                $autoplugin=$critere_panier["autoplugin"];
                $type_recherche=$critere_panier["type_recherche"];
                $booleen=$critere_panier["booleen"];
                $valeur=$critere_panier["valeur_critere"];
                $tmp=applique_plugin($autoplugin, array());
                if ($tmp["succes"] != 1) {
                    $output = $json->encode($tmp);
                    print($output);
                    die();
                }
                $critere_formulaire=$tmp["resultat"];
                $critere_formulaire["type_recherche"]=$type_recherche;
                $critere_formulaire["booleen"]=$booleen;
                $critere_formulaire["valeur"]=$valeur;
                array_push ($formulaire_stat, $critere_formulaire);
            }
        }
        
        $retour["resultat"]["formulaire"]=$formulaire;
        $retour["resultat"]["formulaire_stat"]=$formulaire_stat;
    } else { // erreur
        $retour["succes"]=0;
        $retour["erreur"]="Vous devez selectionner un panier statique ou dynamique";
        $output = $json->encode($retour);
        print($output);
        die();
    }
}



$output = $json->encode($retour);
print($output);

?>