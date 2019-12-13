<?php


class api_mel {

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
var $param_mel;
var $json;


    
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
    
function __construct() {
    $this->json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function load_param_mel($parametres) {
    $this->param_mel=$parametres;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function mel_ws ($requete) {
    //print($requete);
    $chaine=file_get_contents($requete);
    if ($chaine === false) {
        // todo   
    }
    $tableau=$this->json->decode($chaine);
    return($tableau);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_liste_paniers () {
    $requete=$this->param_mel["host"]."/rechercher/ws_liste_paniers.php?field_login_mel_utilisateur=".$this->param_mel["field_login_mel_utilisateur"]."&field_pwd_mel_utilisateur=".$this->param_mel["field_pwd_mel_utilisateur"];
    $liste_paniers=$this->mel_ws($requete);
    return ($liste_paniers);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function exporte_panier ($ID_panier) {
    $str_format_export="";
    $str_enrichissement_resume="";
    $requete=$this->param_mel["host"]."/rechercher/ws_exporter_pan.php?field_login_mel_utilisateur=".$this->param_mel["field_login_mel_utilisateur"]."&field_pwd_mel_utilisateur=".$this->param_mel["field_pwd_mel_utilisateur"]."&cookie_panier=".$ID_panier;
    if ($this->param_mel["format_export"] != "") {
        $str_format_export="&format_export=".$this->param_mel["format_export"];
    }
    if ($this->param_mel["enrichissement_resume"] != "") {
        $str_enrichissement_resume="&enrichissement_resume=".$this->param_mel["enrichissement_resume"];
    }
    $requete.=$str_format_export.$str_enrichissement_resume;
    $infos_panier=$this->mel_ws($requete);
    return ($infos_panier);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_notice_by_ean ($parametres) {
    $EAN=$parametres["EAN"];
    $identifiant=$parametres["identifiant"];
    if ($identifiant == "") {
        $identifiant="ISBN";
    }
    $requete=$this->param_mel["host"]."/rechercher/exporter_ean.php?field_login_mel_utilisateur=".$this->param_mel["field_login_mel_utilisateur"]."&field_pwd_mel_utilisateur=".$this->param_mel["field_pwd_mel_utilisateur"]."&EAN=".$EAN."&identifiant=".$identifiant;
    $tmp=file_get_contents($requete);
    return ($tmp);
    
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// Fonctions spécifiques : les fonctions qui suivent sont spécifiques à waterbear (récupération des paramètres dans le registre...)

function get_param_registre () {
    $param_user=array();
    $param_glob=array();
    $param=array();
    if ($_SESSION["system"]["infos_user"]["mel"] != "") {
        $param_user=$_SESSION["system"]["infos_user"]["mel"];
    }
    try {
        $param_glob=get_registre("system/mel");
    } catch (Exception $e) { // si login inconnu...
        // on ne fait rien
    }
    $param=array_merge($param_glob, $param_user);
    return($param);
}

function get_url ($page) {
    if ($page == "recherche_par_lot") {
        $url=$this->param_mel["host"]."/rechercher/mel_admin.php?module=export_isbn&field_login_mel_utilisateur=".$this->param_mel["field_login_mel_utilisateur"]."&field_pwd_mel_utilisateur=".$this->param_mel["field_pwd_mel_utilisateur"];
    } elseif ($page == "liste_paniers") {
        $url=$this->param_mel["host"]."/rechercher/mel_admin.php?module=paniers&field_login_mel_utilisateur=".$this->param_mel["field_login_mel_utilisateur"]."&field_pwd_mel_utilisateur=".$this->param_mel["field_pwd_mel_utilisateur"];
    } elseif ($page == "portail") {
        $url=$this->param_mel["host"]."/rechercher/mel_admin.php?module=portail&ttes=6&field_login_mel_utilisateur=".$this->param_mel["field_login_mel_utilisateur"]."&field_pwd_mel_utilisateur=".$this->param_mel["field_pwd_mel_utilisateur"];
    }
    return($url);
}

    
    
    
} // fin de la classe

?>