<?PHP
// on inclut différents scripts
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/array.php"); // fonctions pour manipuler des array


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Vérifie si une vraiable est du type attendu
// INT | ARRAY | NOTNULL (default)
// SI OK retourne "", sinon, retourne un message d'erreur
function verif_variable ($variable, $nom_varaiable, $mode="NOTNULL") {
  	if ($mode="NOTNULL") {
	    if (is_null($variable)) {
		  	return ("$nom_variable n'est pas défini\n");
		}
	} elseif ($mode="INT") {
	  	if (!is_numeric($variable)) {
		  	return ("$nom_variable n'est pas un nombre\n");
		}
	} elseif ($mode="ARRAY") {
	  	if (!is_array($variable)) {
		  	return ("$nom_variable n'est pas un tableau\n");
		}
	}
	return("");
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Teste si une vraible n'est pas définie (NULL ou "") et lui affecte une valeur le cas échéant
function set_defaut ($variable, $defaut) {
  	if (is_null($variable) OR $variable=="") {
	    return ($defaut);
	} else {
	  	return ($variable);
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// analyse $GLOBALS["affiche_page"] et le registre pour trouver : 1) les fichiers à inclure 2) les templates et sous-templates
// Au fur et à mesure, la fonction va mettre à jour $GLOBALS["affiche_page"]. Celle-ci sera utilisée par le script affiche_page.php
// pour inclure les pages
function affiche_page () {
  	$page=$GLOBALS["affiche_page"]["page"];
  	$idx=$GLOBALS["affiche_page"]["idx_page"];
  	if ($page=="") {
	    return (0);
	}
  	$tableau=explode("/",$page);
  	$nb_elements=count($tableau);
  	$chemin="pages";
  	
  	// On génère le chemin pour ce noeud (juste avant)
  	for ($i=0 ; $i<$idx ; $i++) {
	    $chemin.="/".$tableau[$i];
	}
	// On cherche les noeuds successifs à partir du chemin, jusqu'à ce qu'on trouve qqchse à afficher
  	for ($i=$idx ; $i < $nb_elements ; $i++) {
  	  	$chemin=$chemin."/".$tableau[$i];
  		//try {$noeud=p_get_registre($chemin);}
  		try {$noeud=p_get_enfants($chemin);}
  		catch (tvs_exception $e) {throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$chemin));}
  		// On récupère les infos de templates
  		if (isset($noeud["_template"])) {
  		  	try {$noeud2=p_get_registre($chemin."/_template");}
  			catch (Exception $e) {throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$chemin."/_templates"));}
  			$noeud["_template"]=$noeud2;
            if (is_array($noeud["_template"])) {
		  	   $GLOBALS["affiche_page"]["template"]=array_merge($GLOBALS["affiche_page"]["template"], $noeud["_template"]);
            }
		}
		// On récupère les paramètres divers (titre, menus, favicon...)
  		if (isset($noeud["_parametres"])) {
  		  	try {$noeud2=p_get_registre($chemin."/_parametres");}
  			catch (Exception $e) {throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$chemin."/_parametres"));}
  			$noeud["_parametres"]=$noeud2;
  		  	if (isset($noeud["_parametres"]["_profiles"])) {
				$noeud["_parametres"]=page_parametres_profile($noeud["_parametres"]);
				unset ($noeud["_parametres"]["_profiles"]);
			}
            if (is_array($noeud["_parametres"])) {
		  	   $GLOBALS["affiche_page"]["parametres"]=array_merge($GLOBALS["affiche_page"]["parametres"], $noeud["_parametres"]);
            }
		}
		
		// On récupère les infos de page
  		if (isset($noeud["_page"])) {
  		  	try {$noeud2=p_get_registre($chemin."/_page");}
  			catch (Exception $e) {throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$chemin."/_page"));}
  			$noeud["_page"]=$noeud2;
		    $GLOBALS["affiche_page"]["include"]=$noeud["_page"];
		    if ($i == $nb_elements-1) { // si c'est le dernier élément
			  	$GLOBALS["affiche_page"]["page"]="";
			}
		    $GLOBALS["affiche_page"]["idx_page"]=$i+1;
		    return(1);
		}
  	} // fin du for...
  	throw new tvs_exception ("registre/noeud_page_inexistant", array("chemin"=>$chemin));
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction analyse un noeud de page du regstre (dans profiles/pages/...)
// et retourne les paramètres liés à une page (noeud _parametres) en fonction du profile (utilisateur/poste)
function page_parametres_profile ($noeud) {
  	$user=$_SESSION["system"]["user"];
  	$Guser=$_SESSION["system"]["Guser"];
  	$poste=$_SESSION["system"]["poste"];
  	$Gposte=$_SESSION["system"]["Gposte"];
  	$profile_spec=""; // on peut forcer un profile spécifique indépendemment du user et du poste
  	// profiles du moins important au plus important comme ça, les propriétés pourront être héritées
  	// comme on fera des merge successifs
  	$profiles=array("*:*", "*:G$Gposte", "G$Guser:*", "G$Guser:G$Gposte", "*:P$poste", "P$user:*", "G$Guser:P$poste", "P$user:G$Gposte", "P$user:P$poste");
  	
  	if ($profile_spec != "") { // si profile spécial, on ne récupère que lui (pas d'héritage)
	    if (isset($noeud["_profiles"][$profile_spec])) {
		  	$noeud=array_merge($noeud, $noeud["_profiles"][$profile_spec]);
		  	return ($noeud);
		}
	}
	
	foreach ($profiles as $profile) {
	  	if (isset($noeud["_profiles"][$profile])) {
		  	$noeud=array_merge($noeud, $noeud["_profiles"][$profile]);
		}
	}
	return ($noeud);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Analyse les droits de l'utilisateur en cours à accéder à telle page ou tel WS
function verifie_droits_page ($chemin) {
  	$chemin="pages/".$chemin;
  	$droit=$GLOBALS["tvs_global"]["conf"]["ini"]["droit_par_defaut"];
  	$tableau=explode("/",$chemin);
  	$atester="";
  	foreach ($tableau as $element) {
  	  	$atester.="$element";
		//try {$noeud=p_get_registre($atester);}
		try {$noeud=p_get_enfants($atester);}
  		catch (Exception $e) {throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$atester));}
  		if (isset($noeud["_droits"])) {
  		  	try {$noeud2=p_get_registre($atester."/_droits");}
  			catch (Exception $e) {throw new tvs_exception ("registre/noeud_inexistant", array("chemin"=>$atester."/_droits"));}
  			$noeud["_droits"]=$noeud2;
			$droit=verifie_droits_noeud ($noeud, $droit); 	
		}
		$atester.="/";
	}
	return($droit);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Analyse les droits de l'utilisateur en cours à accéder à telle page ou tel WS au niveau d'un noeud
function verifie_droits_noeud ($noeud, $droit) {
	$user=$_SESSION["system"]["user"];
  	$Guser=$_SESSION["system"]["Guser"];
  	$poste=$_SESSION["system"]["poste"];
  	$Gposte=$_SESSION["system"]["Gposte"];
	// Du plus au moins pertinent (on s'arrête dès qu'on a trouvé qqchse)
	$profiles=array("P$user:P$poste", "P$user:G$Gposte", "G$Guser:P$poste", "P$user:*", "*:P$poste", "G$Guser:G$Gposte", "G$Guser:*", "*:G$Gposte", "*:*");
	
	// Gestion des droits d'accès au niveau de la PAGE
	foreach ($profiles as $profile) {
	  	if (isset($noeud["_droits"][$profile])) {
		    $droit=$noeud["_droits"][$profile];
		    break;
		}
		
	}
	
	// Gestion des droits d'accès au niveau des variables de la page
	if (isset($noeud["_droits"]["_variables"])) {
		foreach ($noeud["_droits"]["_variables"] as $nom_variable => $tmp) { // pour chaque variable
		  	foreach ($tmp as $valeur_variable => $tmp2) { // pour chaque valeur possible de cette variable
			    if ($_REQUEST[$nom_variable]==$valeur_variable) {
				  	foreach ($profiles as $profile) { // pour chaque profile
					    if (isset($tmp2[$profile])) {
			    			$droit=$tmp2[$profile];
			    			break 3; // On arrête les vérifs dès qu'une correspondance est trouvée (on gère pas des droits différents selon plusieurs variables...)
						}
					}
				}
			}
		}
	}

  	return ($droit);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupère un intitulé correspondant à une langue donnée
// 2 possibilités : soit on fournit un chemin + une clef, soit on fournit juste une clef complète (ID chemin complet)
// auquel cas, on explode : le dernier élement devient la clef et le reste le chemin
function get_intitule ($chemin, $clef, $variables) {
	//print ("get_intitule ($chemin, $clef) <br>\n");
  	$code_langue=get_code_langue ();
  	// Si $chemin n'est pas fourni, on le récupère à partir de $clef
  	if ($chemin=="") {
		$tmp=explode ("/", $clef);
		$tmp2=array();
		for ($i=0 ; $i<count($tmp);$i++) {
		  	if ($i == count($tmp)-1) {
			    $clef=$tmp[$i];
			} else {
			  	array_push($tmp2, $tmp[$i]);
			}
		}
		$chemin=implode("/", $tmp2);
	}
	$chemin="langues/".$chemin."/_intitules";
 	$code_langue_devel=$GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_devel"]; 
 	$retour="";
    
    try {
        $retour=p_get_registre($chemin."/$clef/$code_langue");
    } catch (Exception $e) {
        try {
            $retour=p_get_registre($chemin."/$clef/$code_langue_devel");
        } catch (Exception $e) {
            //return (" ?? $chemin - $clef ?? ");
            if ($clef=="_void_") {
                return ("");
            }
            return ("$clef");
        }
    }
    
    /**
 	try {$noeud=p_get_registre($chemin);}
  	catch (Exception $e) {return(" ? $chemin - $clef ? ");}
  	if (isset($noeud[$clef][$code_langue])) {
	    $retour=$noeud[$clef][$code_langue];
	} elseif (isset($noeud[$clef][$code_langue_defaut])) {
	  	$retour=$noeud[$clef][$code_langue_defaut];
	} else {
	  	return (" ?? $chemin - $clef ?? ");
	}
    **/
	// On place les variables
	foreach ($variables as $clef => $valeur) {
	  	$retour=str_replace("#".$clef."#", $valeur, $retour);
	}
	return ($retour);
}

// TMP !!!!!!!!!!!!!!!!!!!!!!!!!!!!
function get_code_langue () {
    if ($_SESSION["system"]["langue"] != "") {
        return ($_SESSION["system"]["langue"]);
    } elseif ($GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_defaut"] != "") {
        return ($GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_defaut"]);
    } else {
        return ($GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_devel"]);
    }
    //return ("_fr");
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupère l'intitulé d'une exception
// parametres = array("message"=>la chaine renvoyée, "variables"=>les variables)
// Ces paramètres sont renvoyés tels quels par tvs_exception->get_infos()

/**
 * get_exception()
 * Retourne l'intitulé d'une exception
 * @param mixed $parametres 
 * @param     [message]=>le chemin de l'intitulé dans le registre
 * @param     [variables]=>array() : variables à inclure dans l'intitulé
 * @return l'intitule de l'exception
 */
function get_exception ($parametres) {
  	$exception=$parametres["message"];
  	$variables=$parametres["variables"];
  	$exception="exceptions/".$exception;
  	$tableau=explode ("/", $exception);
  	$t1=array();
  	$clef="";
  	$nb=count($tableau);
  	for ($i=0 ; $i<$nb ; $i++) {
		if ($i < $nb-1) {
		  	array_push($t1, $tableau[$i]);
		} else {
		  	$clef=$tableau[$i];
		}
	}
	$chemin=implode ("/", $t1);
	$tmp=get_intitule ($chemin, $clef, $variables);
	if ($tmp == "") {
		$tmp=utf8_encode("Exception non paramétrée : ").$exception;
	}
	return ($tmp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Génère un n° d'opération unique'
function get_id_operation () {
	$id=uniqid(mt_rand(0,1000), true);
	return($id);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// YYYY-MM-DD ====> timestamp
function date_us_2_timestamp ($chaine) {
    $tmp=explode ("-", $chaine);
    $annee=$tmp[0];
    $mois=$tmp[1];
    $jour=$tmp[2];
    $timestamp=mktime(0, 0, 0, $mois, $jour, $annee);
    return ($timestamp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// JJ/MM/AAAA ====> timestamp
function date_fr_2_timestamp ($chaine) {
    $tmp=explode ("/", $chaine);
    $annee=$tmp[2];
    $mois=$tmp[1];
    $jour=$tmp[0];
    $timestamp=mktime(0, 0, 0, $mois, $jour, $annee);
    return ($timestamp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// YYYY-MM-YY ====> JJ/MM/AAAA
function date_us_2_fr ($chaine) {
    $timestamp=date_us_2_timestamp($chaine);
    $date=date("d/m/Y", $timestamp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// JJ/MM/AAAA ====> YYYY-MM-DD
function date_fr_2_us ($chaine) {
    $timestamp=date_fr_2_timestamp($chaine);
    $date=date("Y-m-d", $timestamp);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function conversion_date ($date, $format_entree, $format_sortie) {
    if ($date == "") {
        $date=time();
        $format_entree="timestamp";
    }
    
    if ($format_entree == "fr") {
        $date=date_fr_2_timestamp($date);
    } elseif ($format_entree == "us") {
        $date=date_us_2_timestamp($date);
    } 
    
    if ($format_sortie == "fr") {
        $date=date("d/m/Y", $date);
    } elseif ($format_sortie == "us") {
        $date=date("Y-m-d", $date);
    }
    
    return ($date);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_user ($login, $mdp) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    
    if (isset($GLOBALS["tvs_global"]["conf"]["ini"]["users_system"][$login])) { // user system
        $branche=$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"][$login];
    } else { // user normal
        // On récupère les infos pour ce login
        try {
            $branche=get_registre("system/users/$login");
        } catch (Exception $e) { // si login inconnu...
            $retour["succes"]=0;
            $retour["erreur"]=get_intitule("erreurs/messages_erreur", "user_inconnu", array("login"=>$login));
            return ($retour);
        }
    }
    
    // on vérifie le mdp
    if ($mdp != $branche["mdp"]) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "mdp_incorrect", array());
        return ($retour);
    }
    
    $retour["resultat"]=$branche;
    return ($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_poste ($ID_poste) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    
    // On récupère les infos pour ce login
    try {
        $branche=get_registre("system/postes/$ID_poste");
    } catch (Exception $e) { // si login inconnu...
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "poste_inconnu", array("poste"=>$ID_poste));
        return ($retour);
    }
        
    $retour["resultat"]=$branche;
    return ($retour);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// copie un fichier distant (via HTTP ou FTP) en local
// le nom du fichier doit être de la forme "http://xxxxxxx"
function importe_fichier ($chemin_distant) {
    $chaine=file_get_contents($chemin_distant);
    if ($chaine=="") {
        return(false);
    }
    $id_upload=get_compteur("id_upload");
    $chemin_local=$GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"]."/upload_".$id_upload.".file";
    $handle=fopen($chemin_local, "w");
    fwrite($handle, $chaine);
    return ($chemin_local);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function registre_2_conf () {
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_registre_2_conf"] == 0) {
        return ("");
    }
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_registre_2_conf"] == 2) { // surcharge autorisée pour certains utilisateurs uniquement
        $user=$_SESSION["system"]["user"];
        if ($user=="") {
            $user=$_REQUEST["login"];
        }
        $liste_users=$GLOBALS["tvs_global"]["conf"]["ini"]["registre_2_conf_users"];
        if (in_array($user, $liste_users)) {
            // on ne fait rien
        } else {
            return ("");
        }
    }
    $tmp=get_registre ("system/conf");
    foreach ($tmp as $element) {
        $clef=$element["clef"];
        $valeur=$element["valeur"];
        $chaine='$GLOBALS["tvs_global"]["conf"]["ini"]';
        $segments=explode("/", $clef);
        foreach ($segments as $segment) {
            $segment=trim($segment);
            $chaine.='["'.$segment.'"]'; 
        }
        $chaine.='="'.$valeur.'";';
        eval($chaine);
    
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function add_skin ($chaine) {
    if ($_SESSION["system"]["skin"]=="" OR $_SESSION["system"]["skin"]=="defaut") {
        return ($chaine);
    }
    $chaine="skins/".$_SESSION["system"]["skin"]."/$chaine";
    return ($chaine);
}


?>