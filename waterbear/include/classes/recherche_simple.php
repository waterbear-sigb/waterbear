<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_paniers.php"); // pour les paniers


/**
 * recherche_simple
 * Cette classe gère en fait toutes les recherches (même les complexes (jointure...)
 * 
 * 
 * 
 * paramètres de la fonction init():
 * [type_objet] => type d'objet à rechercher
 * [criteres][0,1,2...][booleen (AND, OR...) | type_recherche (str_commence, str_contient...) | intitule_critere (a_titre, a_auteur...) | valeur_critere (la chaine à rechercher) | plugin_formate_critere (un plugin pour formater le critère par ex. transformer "an" en "2013")]
 * [tris][0,1,2...] => les colonnes à utiliser pour trier
 * [page] => page à afficher
 * [nb_notices_par page] 
 * [format_resultat] => str_sql : la requete SQL | donnees : une array des résultat (en tableau) contenant ou non la notice xml | formate : tableau ou chaine formatés
 * [bool_parse_contenu] => est-ce qu'on parse la notice pour en faire un objet DOMXML (qui sera ajouté dans la colonne xml)
 * [plugin_formate_notice] => plugin à utiliser pour formater chaque notice (si format_resultat == formate)
 *                            passe en paramètre la ligne (toutes les colonnes + éventuellement la notice xml) dans le paramètre [ligne]
 *                            récupère directement [resultat]
 * [plugin_formate_liste] =>  plugin à utiliser pour formater la liste (si format_resultat == formate)
 *                            si non fourni, on retourne une array
 *                            passe en paramètre le tableau des notices sous [tableau]
 *                            recupère directement [resultat] 
 */
class recherche_simple {

var $type_objet;
var $nom_table;
var $criteres;
var $tris;
var $format_resultat;
var $plugin_formate_notice;
var $plugin_formate_liste;
var $bool_parse_contenu; // si 1, on parse la colonne contenu en XML pour les formats formate et donnees
var $nb_notices_par_page;
var $page;
var $json;


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
function __construct () {
    
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// maj les paramètres de recherche

function init ($parametres) {
    $this->type_objet=$parametres["type_objet"];
    $this->nom_table="obj_".$this->type_objet."_acces";
    $this->criteres=$parametres["criteres"];
    $this->tris=$parametres["tris"];
    if (! is_array($this->tris)) {
        $this->tris=array();
    }
    $this->format_resultat=$parametres["format_resultat"];
    $this->plugin_formate_notice=$parametres["plugin_formate_notice"];
    $this->plugin_formate_liste=$parametres["plugin_formate_liste"];
    $this->bool_parse_contenu=$parametres["bool_parse_contenu"];
    $this->nb_notices_par_page=$parametres["nb_notices_par_page"];
    $this->page=$parametres["page"];
    if ($this->nb_notices_par_page == "") {
        $this->nb_notices_par_page=10;
    }
    ksort($this->criteres);
    ksort($this->tris);
    $this->json= new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql_criteres
// génère une chaine de caractères SQL pour les critères

function genere_sql_criteres ($criteres) {
    $sql="";
    if ($criteres=="") {
        $criteres=$this->criteres;
    }
    foreach ($criteres as $critere) {
        $critere=secure_sql($critere); // on sécurise les infos
        $booleen=$critere["booleen"];
        $type_recherche=$critere["type_recherche"];
        $intitule_critere=$critere["intitule_critere"];
        $valeur_critere=$critere["valeur_critere"];
        $schema_jointure=$critere["schema_jointure"]; // paramètres d'une sous-requête de jointure, dans laquelle valeur_critere est injecté sous forme d'une variable incluse        
        $type_obj_lien=$critere["type_obj_lien"]; // pour liens vers panier (auteur, exemplaire...)
        $type_lien=$critere["type_lien"]; // pour les liens vers panier (701 | 702 ...)
        $sens_lien=$critere["sens_lien"]; // pour les liens vers panier (implicite | explicite)
        
        $nom_panier_comptage=$critere["nom_panier_comptage"];
        $type_comptage=$critere["type_comptage"];
        
        $plugin_formate_critere=$critere["plugin_formate_critere"];
        
              
        if (is_array($plugin_formate_critere)) {
            $tmp=applique_plugin($plugin_formate_critere, array("chaine"=>$valeur_critere));
            if ($tmp["succes"] != 0) {
                $valeur_critere=$tmp["resultat"]["chaine"];
            }
        }
        
        if ($sql == "" AND $booleen == "AND NOT") {
            $booleen="NOT";
        } elseif ($sql == "") {
            $booleen="";
        } elseif ($booleen == "") {
            $booleen = "AND";
        }
        
        // TODO gérer ici les critères ne nécessitant pas de valeur (est vide, est non vide...)
        
        // Si pas de valeur, on ignore le critère (sauf recherches spécifiques du type est nul ou n'est pas nul)
        if ($valeur_critere === "") {
            if ($type_recherche=="is_null" OR $type_recherche == "is_not_null") {
                $valeur_critere="";
            } else {
                continue;
            }
        }
        
        // si jointure fournie via un formulaire de recherche, on remplace valeur_critere par schema_jointure (dans lequel valeur_critere a été au préalable injecté via une variable incluse)
        if ($schema_jointure != "") {
            $valeur_critere=$schema_jointure;
        }
        
        $phrase="";
      
        if ($type_recherche == "str_commence") {
            $phrase=" $booleen ".$this->nom_table.".$intitule_critere like '$valeur_critere%' ";
        } elseif ($type_recherche == "str_chaine_contient") {
            $phrase=" $booleen ".$this->nom_table.".$intitule_critere like '%$valeur_critere%' ";
        } elseif ($type_recherche == "str_contient") {
            $valeur_critere = $this->formate_fulltext($valeur_critere, array("ou"=>false, "commence"=>false, "last_commence"=>false)); 
            $phrase=" $booleen MATCH (".$this->nom_table.".$intitule_critere) AGAINST ('$valeur_critere' IN BOOLEAN MODE) ";
        } elseif ($type_recherche == "str_contient_commence") {
            $valeur_critere = $this->formate_fulltext($valeur_critere, array("ou"=>false, "commence"=>true, "last_commence"=>false)); 
            $phrase=" $booleen MATCH (".$this->nom_table.".$intitule_critere) AGAINST ('$valeur_critere' IN BOOLEAN MODE) ";
        } elseif ($type_recherche == "str_contient_last_commence") {
            $valeur_critere = $this->formate_fulltext($valeur_critere, array("ou"=>false, "commence"=>false, "last_commence"=>true)); 
            $phrase=" $booleen MATCH (".$this->nom_table.".$intitule_critere) AGAINST ('$valeur_critere' IN BOOLEAN MODE) ";
        } elseif ($type_recherche == "str_contient_exact") {
            $phrase=" $booleen MATCH (".$this->nom_table.".$intitule_critere) AGAINST ('+\"$valeur_critere\"' IN BOOLEAN MODE) ";
        }elseif ($type_recherche == "panier") {
            $valeur_critere=$this->panier_2_critere($valeur_critere);
            $phrase=" $booleen  ".$this->nom_table.".$intitule_critere in ($valeur_critere)";
        } elseif ($type_recherche == "panier_lien") {
            $valeur_critere=$this->panier_lien_2_critere($valeur_critere, "", $type_obj_lien, $type_lien, $sens_lien, 1);
            $phrase=" $booleen  ".$this->nom_table.".$intitule_critere in ($valeur_critere)";
        } elseif ($type_recherche == "jointure") {
            $valeur_critere=$this->panier_lien_2_critere("", $valeur_critere, $type_obj_lien, $type_lien, $sens_lien, 1);
            $phrase=" $booleen  ".$this->nom_table.".$intitule_critere in ($valeur_critere)";
        } elseif ($type_recherche == "int_egal") { // pas de guillemets
            $phrase=" $booleen ".$this->nom_table.".$intitule_critere = $valeur_critere ";
        } elseif ($type_recherche == "est_parmi_int") { // in () sans guillemets
            $phrase=" $booleen ".$this->nom_table.".$intitule_critere in ($valeur_critere) ";
        } elseif ($type_recherche == "annee") {
            $phrase=" $booleen YEAR(".$this->nom_table.".$intitule_critere) = '$valeur_critere' ";
        } elseif ($type_recherche == "annee_inf_egal") {
            $phrase=" $booleen YEAR(".$this->nom_table.".$intitule_critere) <= '$valeur_critere' ";
        } elseif ($type_recherche == "annee_sup_egal") {
            $phrase=" $booleen YEAR(".$this->nom_table.".$intitule_critere) >= '$valeur_critere' ";
        } elseif ($type_recherche == "mois") {
            $phrase=" $booleen MONTH(".$this->nom_table.".$intitule_critere) = '$valeur_critere' ";
        } elseif ($type_recherche == "jour") {
            $phrase=" $booleen DAYOFWEEK(".$this->nom_table.".$intitule_critere) = '$valeur_critere' ";
        } elseif ($type_recherche == "jour_mois") {
            $phrase=" $booleen DAYOFMONTH(".$this->nom_table.".$intitule_critere) = '$valeur_critere' ";
        } elseif ($type_recherche == "comptage") {
            $sql_panier_comptage="";
            if ($nom_panier_comptage != "") {
                $sql_panier_comptage=$this->panier_lien_2_critere($nom_panier_comptage, "", $type_obj_lien, $type_lien, $sens_lien, 0);
            }
            $valeur_critere=$this->comptage_2_critere($valeur_critere, $type_comptage, $sql_panier_comptage, $type_obj_lien, $type_lien, $sens_lien, 0);
            $phrase=" $booleen  $valeur_critere ";
        } elseif ($type_recherche == "expert") {
            $valeur_critere=str_replace('\"', '"', $valeur_critere);
            $valeur_critere=str_replace('**', '"', $valeur_critere); // pour des raisons de caractères spéciaux en remplace les " par ** puis on fait l'inverse :/
            $valeur_critere2 = $this->json->decode($valeur_critere);
            $phrase=$this->genere_sql_criteres($valeur_critere2);

        } else { // autres opérateurs 'simples' (=, >, <, >=, <=, != ...)
            $operateur=$this->type_recherche_2_operateur($type_recherche);
            $phrase=" $booleen ".$this->nom_table.".$intitule_critere $operateur '$valeur_critere' ";
        }
        
        
        // TODO : les autres types
        $sql.=$phrase;
    }
    return ($sql);
}

function type_recherche_2_operateur ($type_recherche) {
    $types=array("str_egal" =>"=", "inf"=>"<", "inf_egal"=>"<=", "sup"=>">", "sup_egal"=>">=", "is_null"=>"=", "is_not_null"=>"!=", "non_egal"=>"!=");
    if (isset($types[$type_recherche])) {
        return($types[$type_recherche]);
    }
    return ($type_recherche);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql_tri
// génère une chaine de caractères SQL pour le tri

function genere_sql_tri () {
    $tris=$this->tris;
    $chaine="";
    foreach ($tris as $tri) {
        if ($chaine == "") {
            //$chaine.=" ORDER BY ".$this->nom_table.".$tri ";
            $chaine.=" ORDER BY $tri "; // on essaye sans la nom de la table
        } else {
            $chaine.=", $tri ";
        }
    }
    return ($chaine);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql_pagination
// génère une chaine de caractères SQL pour la pagination

function genere_sql_pagination () {
    if ($this->page == "") {
        return ("");
    }
    $notice_debut=($this->page - 1)*$this->nb_notices_par_page;
    $chaine = " LIMIT $notice_debut, ".$this->nb_notices_par_page." ";
    return ($chaine);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql
// génère une chaine de caractères SQL pour la requete

function genere_sql () {
    $sql="";
    $sql_criteres=$this->genere_sql_criteres();
    if ($sql_criteres != "") {
        $sql_criteres=" where ".$sql_criteres;
    }
    $sql_tri=$this->genere_sql_tri();
    $sql_pagination=$this->genere_sql_pagination();
    $sql="select * from ".$this->nom_table." $sql_criteres $sql_tri $sql_pagination"; // TMP
    return ($sql);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql_panier_dynamique
// génère une chaine de caractères SQL pour la requete. Comme requete normale, mais ID au lieu de * et pas de tri ni pagination

function genere_sql_panier_dynamique () {
    $sql="";
    $sql_criteres=$this->genere_sql_criteres();
    $sql="select ID from ".$this->nom_table." where $sql_criteres "; // TMP
    return ($sql);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql_count
// génère une chaine de caractères SQL pour la requete de type count(*)

function genere_sql_count () {
    $sql="";
    $sql_criteres=$this->genere_sql_criteres();
    if ($sql_criteres != "") {
        $sql_criteres=" where ".$sql_criteres;
    }
    $sql="select count(*) from ".$this->nom_table." $sql_criteres"; // TMP
    return ($sql);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// genere_sql_sum
// génère une chaine de caractères SQL pour la requete de type sum(xxx)

function genere_sql_sum ($col) {
    $sql="";
    $sql_criteres=$this->genere_sql_criteres();
    if ($sql_criteres != "") {
        $sql_criteres=" where ".$sql_criteres;
    }
    $sql="select sum($col) from ".$this->nom_table." $sql_criteres"; // TMP
    return ($sql);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// requete_sql

function requete_sql ($sql) {
    $t1=microtime();
    $liste=sql_as_array(array("sql"=>$sql, "contexte"=>"recherche_simple.php::genere_sql()"));
    $t2=microtime();
    $time=$t2-$t1;
    tvs_log("requete_sql", "requete_sql()", array($sql, $time." ms"));
    return ($liste);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// mise en forme

function formate_resultat () {
    $retour=array();
    $retour["succes"]=1;
    $sql=$this->genere_sql();
    if ($this->page == "") {
        $idx_notice=0;
    } else {
        $idx_notice=($this->page - 1)*$this->nb_notices_par_page;
    }
    
    // 1) Format SQL
    if ($this->format_resultat == "str_sql") {
        $retour["resultat"]=$sql;
        return($retour);
    } else { // sinon, on lance la recherche
        try {
            $liste = $this->requete_sql($sql);
        } catch (tvs_exception $e) {
            $retour["succes"]=0;
            $retour["erreur"]=$e->get_exception();
            return($retour);
        }
        
        // Si bool_parse_contenu == 1 on parse la notice xml
        /**
        if ($this->bool_parse_contenu == 1) {
            foreach ($liste as $idx => $ligne) {
                $liste[$idx]["xml"] = new DOMDocument();
                $liste[$idx]["xml"]->preserveWhiteSpace = false;
                $bool=$liste[$idx]["xml"]->loadXML($ligne["contenu"]);
                if ($bool === false) {
                    $retour["succes"]=0;
                    $retour["erreur"]=get_intitule("erreurs/messages_erreur", "xml_impossible_parser", array("chaine"=>$ligne["contenu"]));
                    return ($retour);
                }
            }
        }
        **/
    }
    
    if ($this->format_resultat == "liste") {
        $str="";
        foreach ($liste as $tmp) {
            if (is_numeric($tmp["ID"]) AND $tmp["ID"] != "") {
                if ($str != "") {
                    $str.=",".$tmp["ID"];
                } else {
                    $str=$tmp["ID"];
                }
            }
        }
        $retour["resultat"]=$str;
        return ($retour);
        
    // Format Données : Tableau pouvant OU NON contenir la notice en XML
    // !!! NON ne peut pas contenir la notice XML pour l'instant (à modifier ???)
    } elseif ($this->format_resultat == "donnees") {
        if ($this->bool_parse_contenu == 1) {
            foreach ($liste as $idx_liste => $tmp_liste) {
                $tmp=$this->notice_str_2_xml($tmp_liste);
                if ($tmp["succes"]==0) {
                    //return ($tmp);
                    $tmp["resultat"]="";
                }
                $liste[$idx_liste]["xml"]=$tmp["resultat"];
            }
        }
        $retour["resultat"]=$liste;
        return($retour);
    } elseif ($this->format_resultat == "formate") {
        $liste_retour=array();
        foreach ($liste as $ligne) {
            $ligne["_idx"]=$idx_notice;
            if ($this->bool_parse_contenu == 1) {
                $tmp=$this->notice_str_2_xml($ligne);
                if ($tmp["succes"]==0) {
                    //return ($tmp);
                    $tmp["resultat"]="";
                }
                $ligne["xml"]=$tmp["resultat"];
            }
            
            $idx_notice++;
            $tmp=applique_plugin($this->plugin_formate_notice, array("ligne"=>$ligne)); 
            if ($tmp["succes"] != 1) {
                //return ($tmp);
                $tmp["resultat"]="";
                tvs_log("application_errors", "ERREUR APPLICATION", array("recherche_simple::formate_resultat()::formate_notice", "Erreur lors du formatage de la notice ".$ligne["ID"]." de type ".$this->type_objet, $tmp["erreur"]));
            }
            array_push ($liste_retour, $tmp["resultat"]);
            //$liste_retour.=$tmp["resultat"];
        }
        if ($this->plugin_formate_liste != "") { // si plugin de formatage
            $tmp=applique_plugin ($this->plugin_formate_liste, array ("tableau"=>$liste_retour));
            if ($tmp["succes"] != 1) {
                //return ($tmp);
                $tmp["resultat"]="";
                tvs_log("application_errors", "ERREUR APPLICATION", array("recherche_simple::formate_resultat()::formate_liste", "Erreur lors du formatage de la liste de type ".$this->type_objet, $tmp["erreur"]));
            }
            $retour["resultat"]=$tmp["resultat"];
        } else { // si pas de plugin de liste, on retourne le tableau
            $retour["resultat"]=$liste_retour;
        }
        return ($retour);

    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Recherche de type count(*)

function count () {
    $retour=array();
    $retour["succes"]=1;
    $sql=$this->genere_sql_count();
    try {
        $tmp=sql_as_value(array("sql"=>$sql, "contexte"=>"recherche_simple.php::count()"));
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
        return ($retour);
    }
    $retour["resultat"]["nb_notices"]=$tmp;
    if ($this->nb_notices_par_page != 0) {
        $retour["resultat"]["nb_pages"]=ceil( $tmp/$this->nb_notices_par_page);
    } else {
        $retour["resultat"]["nb_pages"]=0;
    }
    return ($retour);
    
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Recherche de type sum(xxx)

function sum ($col) {
    $retour=array();
    $retour["succes"]=1;
    $sql=$this->genere_sql_sum($col);
    try {
        $tmp=sql_as_value(array("sql"=>$sql, "contexte"=>"recherche_simple.php::sum()"));
    } catch (tvs_exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
        return ($retour);
    }
    $retour["resultat"]["somme"]=$tmp;
    return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ajoute des '*' à la fin des mots pour une recherche de type "contient mots commençant par..."
function formate_fulltext ($chaine, $parametres) {
    $ou=$parametres["ou"];
    $commence=$parametres["commence"];
    $last_commence=$parametres["last_commence"];
    $phrase="";
    $liste=explode(" ", $chaine);
    $nb_mots=count($liste);
    foreach ($liste as $idx=>$mot) {
        if ($mot == "") {
            continue;
        }
        if ($commence === true) {
            $mot.="*";
        }
        if ($ou === false) {
            $mot="+".$mot;
        }
        if ($last_commence === true AND $idx == $nb_mots - 1) {
            $mot.="*";
        }
        $phrase.=$mot." ";
    }
    return ($phrase);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Retourne une chaine de recherche pour un panier statique ou dynamique

function panier_2_critere($chemin) {
    GLOBAL $json;
    $obj_paniers=new tvs_paniers();
    $ligne=$obj_paniers->get_panier_by_chemin($chemin, $this->type_objet);
    if (!isset($ligne["type"])) {
        return ("");
    }
    $type=$ligne["type"];
    $contenu=$ligne["contenu"];
    if ($type == "statique") {
        if ($contenu=="") {
            $contenu="0";
        }
        return ($contenu);
    } elseif ($type == "dynamique") {
        $tmp_recherche=new recherche_simple();
        $contenu2=$json->decode($contenu);
        $tmp_recherche->init($contenu2["recherchator"]);
        $chaine=$tmp_recherche->genere_sql_panier_dynamique();
        return ($chaine);
    } else {
        return ("");
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Retourne une chaine de recherche pour un lien vers un panier statique ou dynamique
// OU pour une jointure simple (sans panier)
// SI $chemin != "" ===> panier
// SI $criteres != "" ===> jointure
// SOIT $chemin => chemin du panier
// SOIT $criteres => criteres de la jointure
// $type_obj_lien => type d'objet lié
// $type_lien => type de lien (ex. auteur principal ou secondaire) ** opt
// $sens_lien => implicite|explicite
// Si bool_descente vaut == 0, on retourne juste le SQL inclus, c'est à dire sans faire la descente qui permet véritabelment la jointure
//  => utilisé pour le comptage

function panier_lien_2_critere($chemin, $criteres, $type_obj_lien, $type_lien, $sens_lien, $bool_descente) {
    GLOBAL $json; 
    if ($chemin != "") { // Si panier
        $obj_paniers=new tvs_paniers();
        $ligne=$obj_paniers->get_panier_by_chemin($chemin, $type_obj_lien);
        if (!isset($ligne["type"])) {
            return ("");
        }
        $type=$ligne["type"];
        $contenu=$ligne["contenu"];
    } else { // sinon jointure... marche comme un panier dynamique
        $type="jointure";
    }
    
    $table_liee="obj_".$type_obj_lien."_acces";
    $sql_type_lien=""; // opt ** en cas de restriction à un type particulier de lien
    $sql_in="";
    
    
    // 1) on génère le sql inclus (spécifique au panier) ==> on obtient par ex. des ID exemplaires
    if ($type == "statique") {
        $sql_in=$contenu;
    } elseif ($type == "dynamique") {
        $tmp_recherche=new recherche_simple();
        $contenu2=$json->decode($contenu);
        $tmp_recherche->init($contenu2["recherchator"]);
        $chaine=$tmp_recherche->genere_sql_panier_dynamique();
        $sql_in=$chaine;
    } elseif ($type == "jointure") {
        $tmp_recherche=new recherche_simple();
        $tmp_recherche->init($criteres);
        $chaine=$tmp_recherche->genere_sql_panier_dynamique();
        $sql_in=$chaine;
    } else {
        return ("");
    }
    
    // 1) bis - si on ne veut QUE le sql de 1er niveau, on retourne ici
    if ($bool_descente == 0) {
        return ($sql_in);
    }
  
    
    // 2) on génère le sql général ==> pour obtenir par ex. des ID biblio
    if ($sens_lien == "explicite") {
        // 2.a on détermine la table de jointure
        $table_jointure="obj_".$this->type_objet."_liens";
        
        // 2.b on génère le SQL lié au type de lien (701, 702...)
        if ($type_lien != "") {
            $sql_type_lien=" AND $table_jointure.type_lien = '$type_lien' ";
        }
        
        // 2.c général
        $sql="select $table_jointure.ID from $table_jointure, $table_liee where $table_liee.ID in ($sql_in) AND $table_jointure.type_objet = '$type_obj_lien' $sql_type_lien AND $table_jointure.ID_lien = $table_liee.ID ";
    } elseif ($sens_lien == "implicite") {
        // 2.a on détermine la able de jointure
        $table_jointure="obj_".$type_obj_lien."_liens";
        
        // 2.b on génère le SQL lié au type de lien (701, 702...)
        if ($type_lien != "") {
            $sql_type_lien=" AND $table_jointure.type_lien = '$type_lien' ";
        }
        
        // 2.c général
        $type_objet=$this->type_objet; 
        //$sql="select $table_jointure.ID from $table_jointure, $table_liee where $table_liee.ID in ($sql_in) AND $table_jointure.type_objet = '$type_objet' $sql_type_lien AND $table_jointure.ID = $table_liee.ID ";
        $sql="select $table_jointure.ID_lien from $table_jointure, $table_liee where $table_liee.ID in ($sql_in) AND $table_jointure.type_objet = '$type_objet' $sql_type_lien AND $table_jointure.ID = $table_liee.ID ";
    } else {
        return ("");
    }
    
    return ($sql);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 

function notice_str_2_xml ($ligne) {
    $retour=array();
    $retour["succes"]=1;
    $chaine=$ligne["contenu"];
    $domxml = new DOMDocument();
    $domxml->preserveWhiteSpace = false;
    $bool=$domxml->loadXML($chaine);
    if ($bool === false) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "xml_impossible_parser", array("chaine"=>$ligne["contenu"]));
        return ($retour);
    }
    $tvs_marcxml=new tvs_marcxml(array("type_obj"=>$this->type_objet, "ID"=>$ligne["ID"]));
    $tvs_marcxml->load_notice($domxml);
    // On enrichit la notice avec un champ 000 contenant $a ID_notice, $b idx_notice, $c type_objet
    // avant de le faire on supprime ceux qui existaient déjà (le cas échéant)
    $champs_000=$tvs_marcxml->get_champs("000", "");
    foreach ($champs_000 as $champ_000) {
        $tvs_marcxml->delete_champ($champ_000);
    }
    
    $param=array(array("code"=>"a", "valeur"=>$ligne["ID"]), array("code"=>"b", "valeur"=>$ligne["_idx"]), array("code"=>"c", "valeur"=>$this->type_objet));
    $tvs_marcxml->add_champ("000", $param, "");
    $retour["resultat"]=$tvs_marcxml->notice;
    return($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//

function comptage_2_critere ($valeur_critere, $type_comptage, $sql_panier_comptage, $type_obj_lien, $type_lien, $sens_lien, $bool_distinct) {
    $sql="";
    $sql_type_lien="";
    $type_objet=$this->type_objet;
    $operateur=$this->type_recherche_2_operateur($type_comptage);
    $table_acces=$this->nom_table;
    
    if ($sens_lien == "explicite") {
        
        $table_jointure="obj_".$this->type_objet."_liens";
        if ($sql_panier_comptage != "") {
            $sql_panier_comptage=" AND $table_jointure.ID_lien in ($sql_panier_comptage) ";
        }
        if ($type_lien != "") {
            $sql_type_lien=" AND $table_jointure.type_lien = '$type_lien' ";
        }
        
        if ($bool_distinct == 1) {
            $sql="SELECT distinct COUNT(*) AS A FROM $table_jointure WHERE $table_jointure.type_objet = '$type_obj_lien' $sql_type_lien $sql_panier_comptage GROUP BY $table_jointure.ID";
        } else {
            $sql=" (SELECT COUNT(*) FROM $table_jointure WHERE $table_jointure.ID = $table_acces.ID AND $table_jointure.type_objet = '$type_obj_lien' $sql_type_lien $sql_panier_comptage) $operateur $valeur_critere ";
        }
    } elseif ($sens_lien == "implicite") {
        $table_jointure="obj_".$type_obj_lien."_liens";
         if ($sql_panier_comptage != "") {
            $sql_panier_comptage=" AND $table_jointure.ID in ($sql_panier_comptage) ";
        }
        if ($type_lien != "") {
            $sql_type_lien=" AND $table_jointure.type_lien = '$type_lien' ";
        }
        if ($bool_distinct == 1) {
            $sql="SELECT distinct COUNT(*) AS A FROM $table_jointure WHERE $table_jointure.type_objet = '$type_objet' $sql_type_lien $sql_panier_comptage GROUP BY $table_jointure.ID_lien";
        } else {
            $sql=" (SELECT COUNT(*) FROM $table_jointure WHERE $table_jointure.ID_lien = $table_acces.ID AND $table_jointure.type_objet = '$type_objet' $sql_type_lien $sql_panier_comptage) $operateur $valeur_critere ";
        }
    }
    
    return ($sql);
}




   
} // fin de la classe


?>