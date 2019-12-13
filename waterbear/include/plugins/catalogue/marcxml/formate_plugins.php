<?PHP

/**
 * plugin_catalogue_marcxml_formate_plugins()
 * 
 * Ce plugin permet de formater une liste de plugins
 * 
 * Il appelle successicement les plugins comme ceci :
 * [texte]<=([notice])
 * 
 * ATTENTION : � la base ce plugin fonctionnait avec les plugin "get_datafields_xxx" qui attendaient le param�tre en [notice] et retournaient le r�sultat en [notice]
 * Mais il peut aussi fonctionner avec d'autres plugins qui ont une autre signature. Dans ce cas, il faut utiliser des alias
 * 
 * 
 * @param mixed $parametres
 * @param["notice"] => notice XML de base (� passer � tous les autres plugins)
 * @param["plugins"][0,1,2...][nom_plugin]
 *                            [parametres] // parametres du plugin
 *                            [avant]
 *                            [apres]
 *                            [avant_verif]
 *                            [seulement_si_vide] => si vaut 1, on n'appliquera le plugin que si les plugins pr�c�dents n'ont rien apport� (ex. auteur principal : on ne cherche les 701 que si 700 n'a rien donn�...) 
 *                            [defaut] valeur par d�faut si le plugin ne retourne rien 
 *
 *  pour chaque plugin :
 *      $tmp[texte] = plugin([notice])
 * 
 * @return [texte] => le texte format�
 */
function plugin_catalogue_marcxml_formate_plugins ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["notice"]="";
    
    $notice_xml=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $ID_notice=$parametres["ID_notice"];
    $type_doc=$parametres["type_doc"];
    
    if ($notice_xml != "") {
        // on ne fait rien
    } elseif ($tvs_marcxml != "") {
        $notice_xml=$tvs_marcxml->notice;
    } elseif ($ID_notice != "" AND $type_doc != "") {
        $notice_xml=get_objet_xml_by_id($type_doc, $ID_notice);
    } else {
        $retour["succes"]=0;
        $retour["erreur"]="@& get_datafields : Vous n'avez fourni aucune notice";
        return($retour);
    }

    foreach ($parametres["plugins"]  as $plugin) {
        if ($plugin["seulement_si_vide"]==1 AND $retour["resultat"]["texte"] != "") {
            continue;
        }
        $tmp=applique_plugin($plugin, array("notice"=>$notice_xml));
        $tmp=$tmp["resultat"]["texte"];
        if ($plugin["defaut"]!="" AND $tmp=="") {
            $tmp=$plugin["defaut"];
        }
        if ($tmp != "") {
            $tmp = $plugin["avant"].$tmp.$plugin["apres"];
            if ($retour["resultat"]["texte"] != "") {
                $tmp=$plugin["avant_verif"].$tmp;
            }
        }
        
        $retour["resultat"]["texte"].=$tmp;
    }
    
    return ($retour);
}

?>