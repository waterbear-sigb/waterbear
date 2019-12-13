<?php

/**
 * plugin_catalogue_marcxml_modif_marcxml()
 * 
 * Ce plugin permet de modifier une notice marcxml
 * ajouter des champs / ss-champs
 * supprimer des champs / ss-champs
 * modifier des champs / ss-champs
 * 
 * selon des paramètres qui sont fournis dans [modifications]
 * 
 * Ce plugin permet de gérer les liens explicites. Par exemple, on peut générer un champ 700 à partir de l'ID de l'auteur lié + rajouter des ss-champs (code fonction...)
 * 
 * @param mixed $parametres
 * @param SOIT [notice] => la notice DomXml
 * @param SOIT [tvs_marcxml]
 * @param [modifications][0,1,2...] => liste des modifications à apporter à la notice
 * @param ----[recherche] => équation de recherche sur les champs et les ss-champs (cf plus bas) : utilisé pour la recherche des champs (sauf pour add) 
 * @param ----[type_modif_champ] => type de modification sur le champ : add => ajout d'un champ | rename => renommer le champ | delete => suppression | reset => on remplace complètement le contenu du champ | update on modifie certains ss-champs | add_si_absent => on crée le champ si la recherche ne renvoie rien | add_update => si le champ existe on le modifie, sinon on le crée
 * @param ----[def_champ] (attention définition différente pour (add, reset) que pour update) => définition du champ à créer (cf plus bas)
 * @param ----[tag] (pour add ou rename) => le tag du champ à créer (add ou rename uniquement)
 * @param ----[plugin_get_lien_explicite] => va générer le champ à partir d'un ID de notice ou une notice XML de notice liée (lien explicite)
 *                                           Les ss-champs générés s'ajouteront à ceux déjà déclarés 
 * 
 * Option : pour un champ de lien explicite, on pourra regénérer le champ à partir de la notice liée
 * Pour cela fournir un [plugin_get_lien_explicite] au niveau du champ
 * Ce plugin devra lui-même avoir en paramètre
 * ----- [type] => type de la notice
 * ----- [ID] ou [notice] ID de la notice ou notice elle-même en XML
 * ----- [plugin_formate] le plugin va récupérer et formater les infos dans la notice 
 * 
 * On pourra renommer un champ ou un sous-champ avec l'option rename
 * Pour un champ, on fournit[tag]
 * Pour un ss-champ on fournit [nouv_code] ([code] est utilisé pour la recherche)
 * 
 * @note format de [recherche] (cf tvs_marcxml::get_champs_liste()): utilisé pour la recherche des champs, sauf pour add
 * @note ["recherche"][XXX][tag|idx|sous-champs]
 * @note ["recherche"][XXX]["sous-champs"][YYY]["code|valeur|idx"]=> 1 des sous-champs
 * 
 * @note [def_champ][0,1,2...][type_modif_ss_champ] => update (defaut) | add | delete | add_update (on modifie ou crée si existe pas) | add_si_absent (on crée le ss-champ s'il n'y en a pas déjà un) | duplicate : on copie le ss-champ | formate : va modifier le contenu du ss-champ avec le plugin plugin_formate
 * @note format de [def_champ] pour add ou reset (cf tvs_marcxml::add_champ()) :
 * @note [def_champ][0,1,2...][code|valeur|rename]
 * 
 * @note format de [def_champ] pour update  :
 * @note [def_champ][0,1,2...][code|valeur|idx|nouv_valeur|type_modif_ss_champ|nouv_code|plugin_formate]
 * 
 * 
 * @note valeur est la valeur actuelle (pour recherche) tandis que nouv_valeur est la valeur de remplacement
 * 
 * 
 * 
 * @return [notice] => la notice modifiée
 */
function plugin_catalogue_marcxml_modif_marcxml ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["notice"]="";
    
    $notice=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $modifications=$parametres["modifications"];
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    foreach ($modifications as $modification) {
  
        $recherche=$modification["recherche"];
        $type_modif_champ=$modification["type_modif_champ"]; // delete|add|reset|update
        $def_champ=$modification["def_champ"];
        $tag=$modification["tag"];
        $plugin_get_lien_explicite=$modification["plugin_get_lien_explicite"];
        if (!is_array($def_champ)) {
            $def_champ=array();
        }
        if ($plugin_get_lien_explicite != "") {
            $bidon=applique_plugin($plugin_get_lien_explicite, array());
            if ($bidon["succes"] != 1) {
                return ($bidon);
            }
            $def_champ=array_merge($def_champ, $bidon["resultat"]["champ"]);
        }
        if ($type_modif_champ == "add") {
            $tvs_marcxml->add_champ($tag, $def_champ, "");
        } else {
            $liste_champs=$tvs_marcxml->get_champs_liste(array("champs"=>$recherche));
            if (($type_modif_champ=="add_si_absent" OR $type_modif_champ=="add_update") AND count($liste_champs)==0) {
                $tvs_marcxml->add_champ($tag, $def_champ, "");
            }
            foreach ($liste_champs as $champ) {
                if ($type_modif_champ == "delete") {
                    $tvs_marcxml->delete_champ($champ);
                } elseif ($type_modif_champ == "rename") {
                    $tvs_marcxml->rename_champ($champ, $tag);
                } elseif ($type_modif_champ == "reset") {
                    $tvs_marcxml->reset_champ($champ, $def_champ);
                } elseif ($type_modif_champ == "update" OR $type_modif_champ=="add_update") {
                    foreach ($def_champ as $def_ss_champ) {  
                        $code=$def_ss_champ["code"];
                        $nouv_code=$def_ss_champ["nouv_code"];
                        $valeur=$def_ss_champ["valeur"];
                        $idx=$def_ss_champ["idx"];
                        $nouv_valeur=$def_ss_champ["nouv_valeur"];
                        $type_modif_ss_champ=$def_ss_champ["type_modif_ss_champ"];
                        $plugin_formate=$def_ss_champ["plugin_formate"];
                        if ($type_modif_ss_champ == "") {
                            $type_modif_ss_champ = "add_update"; // type de modif par défaut
                        }
                        if ($code=="") {
                            continue; // si aucun code précisé, pas de modif
                        }
                        
                        if ($type_modif_ss_champ == "add") {
                            $tvs_marcxml->add_ss_champ($champ, $code, $nouv_valeur);
                        } else {
                            
                            $ss_champs=$tvs_marcxml->get_ss_champs($champ, $code, $valeur, $idx);
                            if (($type_modif_ss_champ=="add_si_absent" OR  $type_modif_ss_champ == "add_update") AND count($ss_champs)==0) {
                                $tvs_marcxml->add_ss_champ($champ, $code, $nouv_valeur);
                            }
                            foreach ($ss_champs as $ss_champ) {
                                if ($type_modif_ss_champ == "delete") {
                                    $tvs_marcxml->delete_ss_champ($champ, $ss_champ);
                                } elseif ($type_modif_ss_champ == "update" OR $type_modif_ss_champ == "add_update") {
                                    $tvs_marcxml->update_ss_champ($ss_champ, $nouv_valeur);
                                } elseif ($type_modif_ss_champ == "rename") {
                                    $tvs_marcxml->rename_ss_champ($ss_champ, $nouv_code);
                                } elseif ($type_modif_ss_champ == "duplicate") {
                                    $tvs_marcxml->duplicate_ss_champ($champ, $ss_champ, $nouv_code);
                                } elseif ($type_modif_ss_champ == "formate") {
                                    $tvs_marcxml->formate_ss_champ($ss_champ, $plugin_formate);
                                }
                             }
                        }

                         // fin du pour chaque ss-champ
                    } // fin du pour chaque modification de ss-champ
                } // fin du test add|delete|reset|update
            } // fin du pour chaque champ
        } // fin du test add <-> delete|reset|update
    } // fin du pour chaque modification

    $retour["resultat"]["notice"]=$tvs_marcxml->notice;
    return ($retour);
}


?>