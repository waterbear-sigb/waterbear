<?PHP

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/dbg_plugins.php");


/**
 * applique_plugin()
 * 
 * $PA peut être un string. Dans ce cas c'est le chemin du point d'accès dans le registre (à partir de profiles/defaut/plugins/points_acces)
 * Mais ça peut aussi être un array(). Dans ce cas, le point d'accès est directement fourni ($PA["nom_plugin"] et $PA["parametres"])
 * 
 * @param mixed $PA
 * @param mixed $parametres
 * @return
 */
function applique_plugin ($PA, $parametres, $type_plugin="") {
    $json = new Services_JSON();
    $ID_plugin=dbg_plugins_init_plugin($PA, $parametres, $type_plugin);
    if (is_array($PA)) {
        $nom_plugin=$PA["nom_plugin"];
        $param_PA=$PA["parametres"];
        $alias=$PA["alias"];
        $alias_retour=$PA["alias_retour"];
        if ($nom_plugin == "") {
            $retour=array();
            $retour["succes"]=0;
            $retour["erreur"]=utf8_encode("Vous n'avez pas fourni le nom du plugin (clef nom_plugin dans le registre)");
            tvs_log("plugins_querys", "RETOUR", array("", "ERREUR : Vous n'avez pas fourni le nom du plugin (clef nom_plugin dans le registre)", $json->encode($retour)));
            tvs_log("plugins_errors", "ERREUR PARAM", array("", "", "Vous n'avez pas fourni le nom du plugin (clef nom_plugin dans le registre)"));
            dbg_plugins_set_erreur ($ID_plugin, "Vous n'avez pas fourni le nom du plugin (clef nom_plugin dans le registre)");
            dbg_plugins_set_params_init($ID_plugin, $param_PA, array(), "???", "???", "???");
            dbg_plugins_end_plugin ($ID_plugin);
            return ($retour);
        }
    } elseif ($PA=="") {
        $retour=array();
        $retour["succes"]=0;
        $retour["erreur"]=utf8_encode("Aucun point d'accès n'est fourni à la fonction apllique_plugin()");
        tvs_log("plugins_querys", "RETOUR", array($PA, "ERREUR : Aucun point d'accès n'est fourni à la fonction apllique_plugin()", $json->encode($retour)));
        tvs_log("plugins_errors", "ERREUR PARAM", array($PA, "", "Aucun point d'accès n'est fourni à la fonction apllique_plugin()"));
        dbg_plugins_set_erreur ($ID_plugin, "Aucun point d'accès n'est fourni à la fonction apllique_plugin()");
        dbg_plugins_set_params_init($ID_plugin, $param_PA, array(), "???", "???", "???");
        dbg_plugins_end_plugin ($ID_plugin);
        return ($retour);
    } else { // !!! déprécié
        $retour=array();
        $retour["succes"]=0;
        $retour["erreur"]=utf8_encode("Le point d'accès doit être une array : PA = $PA");
        tvs_log("plugins_querys", "RETOUR", array($PA, "ERREUR : le point d'accès n'est pas une array", $json->encode($retour)));
        tvs_log("plugins_errors", "ERREUR PARAM", array($PA, "", "le point d'accès n'est pas une array"));
        dbg_plugins_set_erreur ($ID_plugin, "Le point d'accès n'est pas une array");
        dbg_plugins_set_params_init($ID_plugin, $param_PA, array(), "???", "???", "???");
        dbg_plugins_end_plugin ($ID_plugin);
        return ($retour);
    }
    
 	
    try { // Si le plugin est défini dans le registre
        $registre_plugin=get_registre("profiles/defaut/plugins/plugins/".$nom_plugin);
        $nom_fonction=$registre_plugin["nom_fonction"];
  	    $chemin_fichier=$registre_plugin["chemin_fichier"];
        $param_plugin=$registre_plugin["parametres"];
        $type_PA="dynamique";
    } catch (tvs_exception $e) { // Si on n'a pas défini le plugin dans le registre, on prend des valeurs par défaut
        $nom_fonction="";
        $chemin_fichier="";
        $param_plugin="";
        $type_PA="statique";
    }
  	
    $elements=explode("/", $nom_plugin);
    $elem_fonction=array_pop($elements);
    $elem_chemin=implode("/", $elements);
    if ($nom_fonction == "") {
        $nom_fonction=$elem_fonction;
    }
    if ($chemin_fichier == "") {
        $chemin_fichier=$elem_chemin;
    }
    $a_inclure=$GLOBALS["tvs_global"]["conf"]["ini"]["plugins_path"]."/".$chemin_fichier."/".$nom_fonction.".php";
    $a_appeler="plugin_".str_replace("/", "_", $chemin_fichier)."_".$nom_fonction;
    
    dbg_plugins_set_params_init($ID_plugin, $param_PA, $param_plugin, $type_PA, $a_inclure, $a_appeler);
    
	//paramètres
	if (is_array($param_PA)) {
	  	$parametres=array_merge($param_PA, $parametres);
	}
	if (is_array($param_plugin)) {
	  	$parametres=array_merge($param_plugin, $parametres);
	}
    
    dbg_plugins_set_params_merge ($ID_plugin, $parametres, "param_merge_init");
    
    
    // ALIAS
    // on renomme certaines variables
    if (is_array($alias)) {
        foreach ($alias as $old_variable => $new_variable) {
            dbg_plugins_set_alias ($ID_plugin, $old_variable, $new_variable, "alias");
            $parametres=traite_alias($parametres, $old_variable, $new_variable);
        }
    }
    
    dbg_plugins_set_params_merge ($ID_plugin, $parametres, "param_merge_alias");
    
    // Gestion des ## et des ??
    $tmp=enrichir_parametres($parametres, $parametres, $ID_plugin);
    $parametres=$tmp["parametres"];
    
    dbg_plugins_set_params_merge ($ID_plugin, $parametres, "param_merge_var_inc");
    
    if ($tmp["bool_plugin_inclus"] == 1) {
        $parametres=plugins_2_param($parametres, $parametres); // gestion des !!
    }
    
    dbg_plugins_set_params_merge ($ID_plugin, $parametres, "param_merge_plugin_inclus");
    
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_querys"]["bool"]==1) {
        //tvs_log("plugins_querys", "APPEL", array($a_inclure, $a_appeler, $json->encode($parametres)));
        tvs_log("plugins_querys", "APPEL", array($a_inclure, $a_appeler));
        $microtime1=microtime(true);
    }
	
	// On inclut le script
	if (!include_once ($a_inclure)) {
        $retour=array();
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "plugin_inexistant_file", array("include"=>$a_inclure));
        tvs_log("plugins_querys", "ERREUR", array($PA, "ERREUR : impossible d'inclure le fichier : $a_inclure"));
        tvs_log("plugins_errors", "ERREUR INCLUDE", array($a_inclure));
        dbg_plugins_set_erreur ($ID_plugin, "impossible d'inclure le fichier : $a_inclure");
        dbg_plugins_end_plugin ($ID_plugin);
        return ($retour);
	}
	
	// On appelle la fonction
	$retour=array();
    if (! function_exists ($a_appeler)) {
        $retour=array();
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "plugin_inexistant_fonction", array("include"=>$a_inclure, "fonction"=>$a_appeler));
        tvs_log("plugins_querys", "ERREUR", array($PA, "ERREUR : impossible d'appeler la fonction $a_appeler dans $a_inclure"));
        tvs_log("plugins_errors", "ERREUR FONCTION", array($a_inclure, $a_appeler, ""));
        dbg_plugins_set_erreur ($ID_plugin, "impossible d'appeler la fonction $a_appeler dans $a_inclure");
        dbg_plugins_end_plugin ($ID_plugin);
        return ($retour);
    }
    try {
        $retour=$a_appeler($parametres);
        dbg_plugins_set_params_merge ($ID_plugin, $retour, "retour");
        // ALIAS RETOUR
        // on renomme certaines variables
        if (is_array($alias_retour)) {
            foreach ($alias_retour as $old_variable => $new_variable) {
                dbg_plugins_set_alias ($ID_plugin, $old_variable, $new_variable, "alias_retour");
                $retour["resultat"]=traite_alias($retour["resultat"], $old_variable, $new_variable);
            }
        }
        dbg_plugins_set_params_merge ($ID_plugin, $retour, "retour_alias");
        dbg_plugins_end_plugin ($ID_plugin);
        if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"]["plugins_querys"]["bool"]==1) {
            $microtime2=microtime(true);
            $microtime=$microtime2-$microtime1;
            //tvs_log("plugins_querys", "RETOUR", array($a_inclure, $a_appeler, $json->encode($retour)));
            tvs_log("plugins_querys", "RETOUR", array($a_inclure, $a_appeler, $microtime));
        }
        if ($retour["erreur"] != "") {
            if ($GLOBALS["tvs_global"]["conf"]["ini"]["trace_erreurs_plugins"]==1) {
                $retour["erreur"]="in $a_inclure \n".$retour["erreur"];
            }
        }
        return($retour);
    } catch (tvs_exception $e) {
        $retour=array();
        $retour["succes"]=0;
        $retour["erreur"]=$e->get_exception();
        tvs_log("plugins_querys", "RETOUR", array($a_inclure, $a_appeler, $json->encode($retour)));
        tvs_log("plugins_errors", "ERR_EXE", array($a_inclure, $a_appeler, $e->get_exception()));
        dbg_plugins_set_erreur ($ID_plugin, "Le plugin a retourné une exception : ". $e->get_exception());
        dbg_plugins_end_plugin ($ID_plugin);
        return ($retour);
    }
  	
} // fin de la fonction


function enrichir_parametres ($parametres, $parametres_origine, $ID_plugin) {
    GLOBAL $json;
    $retour=array();
    $retour["parametres"]=array();
    $retour["bool_plugin_inclus"]=0;
    if (! is_array($parametres)) {
        $retour["parametres"]=$parametres;
        return ($retour);
    }
    foreach ($parametres as $intitule => $element) {
        if (substr($intitule, 0, 2) == "??") {
            $intitule=substr($intitule, 2, strlen($intitule) - 2);
            $retour["parametres"][$intitule]=get_intitule("", $element, array());
        } elseif (substr($intitule, 0, 2) == "##") {
            $intitule=substr($intitule, 2, strlen($intitule) - 2);
            $recup=get_parametres_by_chemin($parametres_origine, $element);
            $retour["parametres"][$intitule]=$recup;
            dbg_plugins_set_var_inc ($ID_plugin, $intitule, $element, $recup);
            //tvs_log("VAR_INC", "##", array("$intitule <= $element", $json->encode($recup)));
        } elseif (substr($intitule, 0, 2) == "!!") {
            $retour["bool_plugin_inclus"]=1;
            $tmp=enrichir_parametres($element, $parametres_origine, $ID_plugin);
            $retour["parametres"][$intitule]=$tmp["parametres"];
        } elseif (substr($intitule, 0, 2) == "::") {
            $intitule=substr($intitule, 2, strlen($intitule) - 2);
            $recup=get_parametres_by_chemin($_SESSION, $element);
            $retour["parametres"][$intitule]=$recup;
            dbg_plugins_set_var_inc ($ID_plugin, $intitule, $element, $recup);
        } elseif (! is_array($element)) {
            $retour["parametres"][$intitule]=$element;
        } else { // Array
             $tmp=enrichir_parametres($element, $parametres_origine, $ID_plugin);
             if ($tmp["bool_plugin_inclus"]==1) {
                $retour["bool_plugin_inclus"]=1;
             }
             $retour["parametres"][$intitule]=$tmp["parametres"];
        }
    }
    return ($retour);
    
}

/**
 * @param $tableau : le tableau à analyser
 * @param $parametres : les parametres qui seront fournis quand un plugin est rencontré
 * 
 * Cette fonction retourne de manière récursive les éléments fournis en paramètre 
 * SI un élément commence par "!!", il est considéré correspondre à un plugin. Dans ce cas le script retourne à cet endroit ce qui a été retourné par le plugin
 * Par exemple, si on a en paramères[tableau]
 * [intitule]=>toto
 * [valeur]=>tutu
 * [!!liste_champs]=>[nom_plugin]=>aa/bb/cc
 *                   [parametres]=>???
 *                   [passe_parametres]
 *                                    [nom_parametre1]=>[chemin_parametre1], [nom_parametre2]=>[chemin_parametre2]...
 *                   [emplacement_resultat]
 * 
 * le plugin retournera
 * [intitule]=>toto
 * [valeur]=>tutu
 * [liste_champs]=> XXXX (ce que retourne le plugin aa/bb/cc)
 * 
 * On peut faire en sorte de passer certains paramètres du plugin appelant au plugin appelé. pour cela, il faut spécifier le champ [passe_parametre]
 * par exemple si un plugin a pour paramètres :
 * [toto]=>tutu
 * [!!titi]=>[nom_plugin]=>turlututu, [passe_parametres]=>[caca]=>toto
 * alors le plugin turlututu qui est inclus recevra dans son paramètre [caca] la valeur "toto"
 * et donc le parametre [!!titi] du plugin appelant pourra avoir son contenu modifié en conséquence.
 * Si [nom_parametre] vaut "_root", les infos retournées remplaceront directement le noeud parametres du plugin appelé
 * *** NON ! *** Si [chemin_pametre] vaut "/", il retournera la totalité des paramètres
 * Sinon, la notation est de la forme "aa/bb/cc" pour récupérer $parametres["aa"]["bb"]["cc"]
 * 
 * 
 * 
 * SI un élément commence par "??" il est considéré correspondre à un intitulé. Dans ce cas, WB appliquera la fonction get_intitule() sur le contenu
 * 
 */
 
function plugins_2_param ($tableau) {
    GLOBAL $json;
    $retour = array ();
    foreach ($tableau as $intitule => $element) { // Pour chaque élément du tableau à évaluer
        if (substr($intitule, 0, 2) == "!!") { // Si plugin
            //tvs_log("PLUGIN_INC", "A!!", array("$intitule => ".$json->encode($element)));
            $tmp=applique_plugin($element, array(), "plugin_inclus");
            //tvs_log("PLUGIN_INC", "R!!", array("$intitule => ".$json->encode($tmp)));
            if ($tmp["succes"] != 1) {
                return ($tmp);
            }
            $intitule=substr($intitule, 2, strlen($intitule) - 2);
            $retour[$intitule]=$tmp["resultat"];
        }elseif (! is_array($element)) { // Si String
            $retour[$intitule]=$element;
        } else { // Si array
            $tmp=plugins_2_param ($element, $parametres);
            $retour[$intitule]=$tmp;
        }
    }
    return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_parametres_by_chemin ($parametres, $chemin_parametre) {
    
    if (is_object ($parametres)) {
        print_r($_SESSION);
        die ("parametres = objet : $chemin_parametre");
    }
    
    //print ("get_parametres_by_chemin() : chemin => $chemin_parametre <br>\n");
    //print_r($parametres);
    //print ("<br>\n");
    // 1) si vaut "/" on retourne tout
    if ($chemin_parametre == "/") {
        //return ($parametres); // déscativé car risque de récurence à l'infini
        return (array());
    }
    
    // 2) on récupère la 1ere clef et le reste du chemin
    $tmp=explode("/", $chemin_parametre, 2);
    $clef=$tmp[0];
    
    // 2bis) si branche non définie, on retourne false
    if (is_numeric($clef)) {
        if (!isset($parametres[$clef])) {
            return (false);
        }
    } else {
        if (!isset($parametres["$clef"])) {
            return (false);
        }
    }
    
    // 3) S'il n'y a qu'un seul élément
    if (count($tmp)==1) {
        if (is_numeric($clef)) {
            return ($parametres[$clef]);
        } else {
            return ($parametres["$clef"]);
        }
    }
    
    // 4) s'il y en a plusieurs => récurence
    $chemin=$tmp[1];
    if (is_numeric($clef)) {
        $retour=get_parametres_by_chemin($parametres[$clef], $chemin);
    } else {
        $retour=get_parametres_by_chemin($parametres["$clef"], $chemin);
    }
    return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function maj_valeur_array ($parametres, $chemin_parametre, $valeur) {
    
    $tmp=explode("/", $chemin_parametre, 2);
    if (! is_array($parametres)) {
        $parametres == array();
    }
    
    if (count($tmp)==1) {
        $clef=$tmp[0];
        $parametres[$clef]=$valeur;
        return ($parametres);
    }
    
    $clef=$tmp[0];
    $chemin=$tmp[1];
    $parametres[$clef]=maj_valeur_array($parametres[$clef], $chemin, $valeur);
    
    return ($parametres);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function unset_parametres_by_chemin ($parametres, $chemin_parametre) {
    $tmp=explode("/", $chemin_parametre, 2);
    
    if (count($tmp)==1) {
        $clef=$tmp[0];
        if (is_array($parametres) AND isset($parametres[$clef])) {
            unset ($parametres[$clef]);
        }
        return ($parametres);
    }
    
    $clef=$tmp[0];
    $chemin=$tmp[1];
    $parametres[$clef]=unset_parametres_by_chemin($parametres[$clef], $chemin);
    return ($parametres);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Il peut y avoir plusieurs nouvelles valeurs séparées par des retours à la ligne

function traite_alias ($parametres, $old_var, $new_var) {
    GLOBAL $json;

    $old_var=str_replace("|", "/", $old_var);
    // 1) on récupère l'ancienne var
    if ($old_var == "/") {
        $valeur=$parametres;
    } else {
        $valeur=get_parametres_by_chemin($parametres, $old_var);
    }
    
    // 2) On insère cette valeur dans le nouveau chemin
    $liste_new_var=explode("\n", $new_var);
    foreach ($liste_new_var as $new_var) {
        if ($new_var == "/") {
            $parametres=$valeur;
        } else {
            $parametres=maj_valeur_array($parametres, $new_var, $valeur);
        }
    }
    
    // 3) On efface l'ancienne valeur
    //if ($new_var != "/" AND $old_var != "/") {
    //    $parametres=unset_parametres_by_chemin($parametres, $old_var);
    //}
    
    return ($parametres);
}


















?>