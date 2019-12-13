<?php



/**
 * get_objet_by_id()
 * 
 * retourne un objet à partir de son type et de son ID
 * retourne un tableau de la forme [contenu|acces...]
 * 
 * @param mixed $type
 * @param mixed $ID
 * @return array si OK ou string vide si erreur
 */
function get_objet_by_id ($type, $ID) {
    $table="obj_".$type."_acces";
    $sql="select * from $table where ID=$ID";
    try {
        $tmp=sql_as_array(array("sql"=>$sql, "contexte"=>"objets.php::get_objet_by_id()"));
    } catch (tvs_exception $e) {
        return("");
    }
    if (count($tmp)==0) {
        return("");
    } else {
        return ($tmp[0]);
    }
}

/**
 * get_objet_xml_by_id()
 * 
 * comme get_objet_by_id() mais retourne uniquement la notice en XML (DOMDocument)
 * 
 * @param mixed $type
 * @param mixed $ID
 * @return
 */
 
function get_objet_xml_by_id ($type, $ID) {
    $tmp=get_objet_by_id ($type, $ID);
    if ($tmp=="") {
        return ("");
    }
    $contenu=$tmp["contenu"];
    $objet_xml=new DOMDocument();
    $objet_xml->preserveWhiteSpace = false;
    $test=$objet_xml->loadXML($contenu);
    if ($test === false) {
        return (false);
    }
    
    $tvs_marcxml=new tvs_marcxml(array("ID"=>$ID, "type_obj"=>$type));
    $tvs_marcxml->load_notice($objet_xml);
    $tvs_marcxml->add_champ_000();
    $objet_xml=$tvs_marcxml->notice;
    
    return ($objet_xml);
}


/**
 * get_objets_lies()
 * 
 * Retourne tous les objets de type $type_objet ayant un lien de type $type_lien avec la notice ayant pour ID $ID_notice_origine
 * retourne un tableau de la forme [0,1,2...][ID|type_objet|type_lien|ID_lien]
 * 
 * @param mixed $type_objet => le type des objets à chercher
 * @param mixed $type_lien => le type de liens à chercher
 * @param mixed $ID_notice_origine => ID de la notice liée aux objets qu'on cherche
 * @param mixed $type_objet_origine => le type de la notice liée aux objets qu'on cherche'
 * @return void
 * 
 * !!!!!!!!!!!!!!!!!!! DEPRECIE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * 
 */
function get_objets_lies ($type_objet, $type_lien, $ID_notice_origine, $type_objet_origine) {
    $retour=array();
    $sql_lien="";
    if ($type_lien != "") {
        $sql_lien=" AND type_lien='$type_lien' ";
    }
    $sql="select * from obj_".$type_objet."_liens where ID_lien=$ID_notice_origine $sql_lien AND type_objet='$type_objet_origine'";
    $resultat=sql_as_array(array("sql"=>$sql, "contexte"=>"objets.php::get_objets_lies()"));
    foreach ($resultat as $ligne) {
        $notice=get_objet_by_id($type_objet, $ligne["ID"]);
        array_push ($retour, $notice);
    }
    return ($retour);
}

/**
 * get_objets_xml_lies()
 * 
 * Retourne des objets liés à un autre objet.
 * Le lien peut être implicite ou explicite
 * 
 * Si bool_xml vaut 0, on retourne une ligne [0,1,2...][ID|type_objet|type_lien|ID_lien]
 * Si bool_xml vaut 1, on rajoute un champ [xml] contenant la notice en xml
 * 
 * Retourne tous les objets de type $type_objet ayant un lien de type $type_lien avec la notice ayant pour ID $ID_notice_origine
 * retourne un tableau de la forme [0,1,2...][ID|type_objet|type_lien|ID_lien] (+ éventuellement [xml])
 * 
 * @param mixed $type_objet => le type des objets à chercher
 * @param mixed $sens_lien => implicite ou explicite
 * @param mixed $type_lien => le type de liens à chercher
 * @param mixed $ID_notice_origine => ID de la notice liée aux objets qu'on cherche
 * @param mixed $type_objet_origine => le type de la notice liée aux objets qu'on cherche
 * @param mixed $bool_xml => si 0 => retourne une ligne, sinon retourne un objet XML
 * @return void
 * 
 * 
 */

function get_objets_xml_lies ($type_objet, $sens_lien, $type_lien, $ID_notice_origine, $type_objet_origine, $bool_xml) {
    $retour=array();
    $sql_lien="";
    if ($type_lien != "") {
        $sql_lien=" AND type_lien='$type_lien' ";
    }
    if ($sens_lien == "implicite") {
        $sql="select * from obj_".$type_objet."_liens where ID_lien=$ID_notice_origine $sql_lien AND type_objet='$type_objet_origine'";
        $resultat=sql_as_array(array("sql"=>$sql, "contexte"=>"objets.php::get_objets_xml_lies()"));
        foreach ($resultat as $ligne) {
            if ($bool_xml == 0) {
                $notice=get_objet_by_id($type_objet, $ligne["ID"]);
            } else {
                $notice=get_objet_by_id($type_objet, $ligne["ID"]);
                $notice_xml=get_objet_xml_by_id($type_objet, $ligne["ID"]);
                $notice["xml"]=$notice_xml;
            }
            
            array_push ($retour, $notice);
        }
    } else { // lien explicite
        $sql="select * from obj_".$type_objet_origine."_liens where ID=$ID_notice_origine $sql_lien AND type_objet='$type_objet'";
        $resultat=sql_as_array(array("sql"=>$sql, "contexte"=>"objets.php::get_objets_xml_lies()"));
        foreach ($resultat as $ligne) {
            if ($bool_xml == 0) {
                $notice=get_objet_by_id($type_objet, $ligne["ID_lien"]);
            } else {
                $notice=get_objet_by_id($type_objet, $ligne["ID_lien"]);
                $notice_xml=get_objet_xml_by_id($type_objet, $ligne["ID_lien"]);
                $notice["xml"]=$notice_xml;
            }
            array_push ($retour, $notice);
        }
    }
    return ($retour);
}

/**
 * add_champ_000()
 * 
 * Cette fonction rajoute un champ 000 à une notice (ATTENTION pas une notice tvs_marcxml : dans ce cas utiliser directement la méthode du même nom de cette classe)
 * 
 * @param mixed $notice
 * @param mixed $ID_notice
 * @param mixed $type_obj
 * @return
 */
function add_champ_000 ($notice, $ID_notice, $type_obj) {
    $tvs_marcxml=new tvs_marcxml(array("ID"=>$ID_notice, "type_obj"=>$type_obj));
    $tvs_marcxml->load_notice($notice);
    $tvs_marcxml->add_champ_000();
    $objet_xml=$tvs_marcxml->notice;
    return ($objet_xml);
}

function metawb_log_objets ($type, $type_objet, $nom, $nom_colonne, $ancien_nom_colonne, $type_colonne, $description_colonne, $type_index, $multivaleurs) {
    // on ne logue que si on est sur un site master
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["mwb_bool_master"] != 1) {
        return ("");
    }
    if ($type=="create_objet" OR $type=="delete_objet" OR $type=="acces_valide_form" OR $type=="tri_valide_form" OR $type=="acces_delete" OR $type=="tri_delete") {
        // on ne fait rien
    } else {
        return("");
    }
    
    $now=time();
    $type_objet=secure_sql($type_objet);
    $nom=secure_sql($nom);
    $nom_colonne=secure_sql($nom_colonne);
    $ancien_nom_colonne=secure_sql($ancien_nom_colonne);
    $type_colonne=secure_sql($type_colonne);
    $description_colonne=secure_sql($description_colonne);
    $type_index=secure_sql($type_index);
    $multivaleurs=secure_sql($multivaleurs);
    
    $sql="INSERT INTO metawb_log_objets values ('$now', '$type', '$type_objet', '$nom', '$nom_colonne', '$ancien_nom_colonne', '$type_colonne', '$description_colonne', '$type_index', '$multivaleurs')";
    sql_query(array("sql"=>$sql, "contexte"=>"objets::metawb_log_objets()"));
    
}




?>