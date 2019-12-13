<?php

/**
 * plugin_catalogue_marcxml_modif_marcxml()
 * 
 * Ce plugin permet de modifier une notice marcxml
 * ajouter des champs / ss-champs
 * supprimer des champs / ss-champs
 * modifier des champs / ss-champs
 * 
 * selon des param�tres qui sont fournis dans [modifications]
 * 
 * Ce plugin permet de g�rer les liens explicites. Par exemple, on peut g�n�rer un champ 700 � partir de l'ID de l'auteur li� + rajouter des ss-champs (code fonction...)
 * 
 * @param mixed $parametres
 * @param SOIT [notice] => la notice DomXml
 * @param SOIT [tvs_marcxml]
 * @param [modifications][0,1,2...] => liste des modifications � apporter � la notice
 * @param ----[recherche] => �quation de recherche sur les champs et les ss-champs (cf plus bas) : utilis� pour la recherche des champs (sauf pour add) 
 * @param ----[type_modif_champ] => type de modification sur le champ : add => ajout d'un champ | rename => renommer le champ | delete => suppression | reset => on remplace compl�tement le contenu du champ | update on modifie certains ss-champs | add_si_absent => on cr�e le champ si la recherche ne renvoie rien | add_update => si le champ existe on le modifie, sinon on le cr�e
 * @param ----[def_champ] (attention d�finition diff�rente pour (add, reset) que pour update) => d�finition du champ � cr�er (cf plus bas)
 * @param ----[tag] (pour add ou rename) => le tag du champ � cr�er (add ou rename uniquement)
 * @param ----[plugin_get_lien_explicite] => va g�n�rer le champ � partir d'un ID de notice ou une notice XML de notice li�e (lien explicite)
 *                                           Les ss-champs g�n�r�s s'ajouteront � ceux d�j� d�clar�s 
 * 
 * Option : pour un champ de lien explicite, on pourra reg�n�rer le champ � partir de la notice li�e
 * Pour cela fournir un [plugin_get_lien_explicite] au niveau du champ
 * Ce plugin devra lui-m�me avoir en param�tre
 * ----- [type] => type de la notice
 * ----- [ID] ou [notice] ID de la notice ou notice elle-m�me en XML
 * ----- [plugin_formate] le plugin va r�cup�rer et formater les infos dans la notice 
 * 
 * On pourra renommer un champ ou un sous-champ avec l'option rename
 * Pour un champ, on fournit[tag]
 * Pour un ss-champ on fournit [nouv_code] ([code] est utilis� pour la recherche)
 * 
 * @note format de [recherche] (cf tvs_marcxml::get_champs_liste()): utilis� pour la recherche des champs, sauf pour add
 * @note ["recherche"][XXX][tag|idx|sous-champs]
 * @note ["recherche"][XXX]["sous-champs"][YYY]["code|valeur|idx"]=> 1 des sous-champs
 * 
 * @note [def_champ][0,1,2...][type_modif_ss_champ] => update (defaut) | add | delete | add_update (on modifie ou cr�e si existe pas) | add_si_absent (on cr�e le ss-champ s'il n'y en a pas d�j� un) | duplicate : on copie le ss-champ | formate : va modifier le contenu du ss-champ avec le plugin plugin_formate
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
 * @return [notice] => la notice modifi�e
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
                            $type_modif_ss_champ = "add_update"; // type de modif par d�faut
                        }
                        if ($code=="") {
                            continue; // si aucun code pr�cis�, pas de modif
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