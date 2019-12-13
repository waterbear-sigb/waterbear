<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_paniers.php");

/**
 * plugin_catalogue_import_export_get_notice()
 * 
 * @param mixed $parametres
 * @param SOIT [notice] => notice DOMXML
 * @param SOIT [tvs_marcxml] => notice tvs_marcxml
 * @param SOIT ["taille_fichier"] => taille du fichier (si fichier) et ["handle"] => handle du fichier (si fichier)
 * @param SOIT ["chaine"] => chaine de caractères (si chaine) et ["taille_chaine"] => longueur de la chaine (si chaine)
 * @param ["last_car"] => Position du dernier caractère lu la fois précédente
 * @param ["plugin_split"] Plugin utilisé pour retourner la notice suivante (registre)
 * @param ["plugin_xml"] Plugin utilisé pour convertir en XML (registre)
 * @param [plugin_importe] plugin utilisé pour importer la notice XML
 * @param ["type"] Type d'objet (biblio...) (registre)
 * @param [pas] : nombre de fois qu'il faudra relancer 
 * @param [bool_verif] : Si vaut 1, on n'importe pas la notice : juste pour affichage. Si vaut 2 : diviseur de fichier de notices
 * @param [tempo] : alternative à pas pour le cas où le fichier doit être traité dans sa globalité (batch...) : attente en millisecondes entre chaque notice 
 * @param [panier] : ** option ** panier dans lequel ajouter le panier
 * @param [import_options] : un tableau contenant divers options qui peuvent être saisies dans le formulaire d'import (ex. bib pour rec 995)
 *                           Ces options sont passées aux différents plugins qui pourront les intégrer
 * 
 * @return array
 * @return [last_car] dernier car du fichier
 *         [commentaire] 
 *         [nb_notices_traitees] 
 * 
 * Ce plugin :
 * 1) récupère la notice suivante dans un fichier (PA du plugin paramétré dans le registre => $plugin_split)
 * 2) convertit la notice en marcxml ((PA du plugin paramétré dans le registre => $plugin_convert_xml)
 * 3) récupère les infos de dédoublonnage (PA du plugin paramétré dans le registre => $plugin_get_infos_ddbl)
 * 4) dédoublonne (PA du plugin paramétré dans le registre => $plugin_ddbl)
 * 5) récupère la version affichable des 2 notices (PA du plugin paramétré dans le registre => $plugin_affiche_notice)
 */
 
function plugin_catalogue_import_export_get_notice ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $pas=$parametres["pas"];
    $tempo=$parametres["tempo"];
    $last_car=$parametres["last_car"];
    $bool_verif=$parametres["bool_verif"];
    if ($bool_verif == 2) { // diviseur de fichier
        $pas=$parametres["nb_notices_diviseur"];
        if ($pas=="" OR $pas==0) {
            $pas=10;
        }
        $fichier=download_file(array());
        if ($fichier["erreur"] != "") {
            $retour["succes"]=0;
            $retour["erreur"]=$fichier["erreur"];
            return ($retour);
        }
        $retour["resultat"]["url"]=$fichier["url"];
    }

    
    if ($last_car == "") {
        $last_car=0;
    }
    $commentaire="";
    $succes=1;
    
    $nb_notices_traitees=0;
    
    while ($succes==1) {
        $parametres["last_car"]=$last_car;
        $tmp=plugin_catalogue_import_export_get_notice_pas($parametres);
        $succes=$tmp["succes"];
        if ($succes == 1) {
            $last_car=$tmp["resultat"]["last_car"];
            $commentaire.=$tmp["resultat"]["commentaire"];
            if ($bool_verif==2) {
                fwrite($fichier["file"], $tmp["resultat"]["notice_brute"]);
            }
            $nb_notices_traitees++;
        } else {
            $retour["succes"]=0;
            $retour["erreur"]=$tmp["erreur"];
        }
        if ($pas === "") {
            // on ne fait rien
        } elseif ($pas === 0 OR $pas === "0") {
            $succes=0;
        } elseif ($pas > 0) {
            $pas--;
        }
        
        if ($tempo != "") {
            usleep($tempo);
        }
    }
    
    $retour["resultat"]["last_car"]=$last_car;
    $retour["resultat"]["commentaire"]=$commentaire;
    $retour["resultat"]["nb_notices_traitees"]=$nb_notices_traitees;
    return ($retour);
    
     
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 function plugin_catalogue_import_export_get_notice_pas($parametres) {
    extract ($parametres);
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $tmp_last_car=0;
    $ID_panier="";
    
        
    // 0) teste panier (si nécessaire)
    if ($panier != "") {
        $tvs_paniers=new tvs_paniers();
        $tmp=$tvs_paniers->get_panier_by_chemin($panier, $type);
        if ($tmp == "") { // si le panier n'existe pas
            $infos=$tvs_paniers->chemin_2_infos($panier);
            $test=$tvs_paniers->create_node(array("nom"=>$infos["nom"], "chemin_parent"=>$infos["chemin_parent"], "type"=>"statique", "type_obj"=>$type));
            if ($test["succes"] != 1) {
                return ($test);
            }
            $ID_panier=$test["resultat"]["ID"];
        } elseif ($tmp["type"] != "statique") { // si el panier n'est pas statique
            $retour["succes"]=0;
            $retour["erreur"]="@& Le panier n'est pas statique";
            return ($retour); 
        } else { // si le panier existe et est statique
            $ID_panier=$tmp["ID"];
        }
    }
   
    // 1) Si fichier ou chaine de caratères fournis, on récupère la notice suivante
    $notice_brute="";
    if ($handle != "") { // Fichier
        // On récupère la notice suivante
        $tmp=applique_plugin($plugin_split, array("handle"=>$handle, "taille_fichier"=>$taille_fichier, "last_car"=>$last_car));
        if ($tmp["succes"] != 1) {
            return ($tmp); // on propage l'erreur
        }
        $tmp_last_car=$tmp["resultat"]["last_car"];
        $notice_brute=$tmp["resultat"]["notice"];
        $retour["resultat"]["last_car"]=$tmp_last_car;
    } elseif ($chaine != "") { // chaîne de caractère
        // On récupère la notice suivante
        $tmp=applique_plugin($plugin_split, array("chaine"=>$chaine, "taille_chaine"=>$taille_chaine, "last_car"=>$last_car));
        if ($tmp["succes"] != 1) {
            return ($tmp); // on propage l'erreur
        }
        $tmp_last_car=$tmp["resultat"]["last_car"];
        $notice_brute=$tmp["resultat"]["notice"];
        $retour["resultat"]["last_car"]=$tmp_last_car;
    }
    
    // si diviseur de fichier
    if ($bool_verif == 2) {
        $retour["resultat"]["notice_brute"]=$notice_brute;
    }
    
      
    // 2) Et on la convertit  en DomXml
    if ($notice_brute != "") {
        $tmp=applique_plugin($plugin_xml, array("notice"=>$notice_brute, "import_options"=>$import_options));
        if ($tmp["succes"] != 1) {
            return ($tmp); // on propage l'erreur
        }
        $notice=$tmp["resultat"]["notice"];
    }
    
    // 3) Puis en tvs_marcxml
    if ($notice != "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    // 4) à la fin, si pas de tvs_marcxml => erreur
    if ($tvs_marcxml == "") {
        $retour["succes"]=0;
        $retour["erreur"]="OK";
        return ($retour);    
    }
    
    // 5) On appelle le plugin d'importation pour cette notice
    if ($bool_verif < 1) {
        $tmp=applique_plugin($plugin_importe, array("tvs_marcxml"=>$tvs_marcxml, "import_options"=>$import_options));
        $retour["resultat"]["tvs_marcxml"]=$tmp["resultat"]["tvs_marcxml"];
        $retour["resultat"]["ID_notice"]=$tmp["resultat"]["ID_notice"];
        $commentaire=$tmp["resultat"]["commentaire"];
    } else {
        $commentaire=">> ";
    }
    
    // 5bis) on ajoute la notice au panier
    if ($ID_panier != "") {
        $tvs_paniers->add_statique($ID_panier, $tmp["resultat"]["ID_notice"]);
    }

    // 6) on récupère une version affichable de la notice
    $tmp2=applique_plugin($plugin_formate, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp2["succes"] != 1) {
        return ($tmp2);
    }
    $notice_affiche=$tmp2["resultat"]["texte"];
    $retour["resultat"]["commentaire"]="<b>".$commentaire."</b> - ".$notice_affiche."<br><br>";
    $retour["resultat"]["nb_notices_traitees"]=1;
    /**
    // Si PAS on relance
    if ($pas === "" OR $pas > 0) {
        $auto_plugin=array("nom_plugin"=>"catalogue/import_export/get_notice");
        $parametres["last_car"]=$tmp_last_car;
        $parametres["pas"]=$pas-1;
        $tmp3=applique_plugin($auto_plugin, $parametres);
        if ($tmp3["succes"] != 1) {
            // ??
        }
        $retour["resultat"]["last_car"]=$tmp3["resultat"]["last_car"];
        $retour["resultat"]["commentaire"].=$tmp3["resultat"]["commentaire"];
        $retour["resultat"]["nb_notices_traitees"]=$tmp3["resultat"]["nb_notices_traitees"]+1;
    }
    */

    return ($retour);
    
    
    
    
        
 }


?>