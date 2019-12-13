<?php

class tvs_marcxml {

//////////////////////////////////////////////////////////////////////////////////////////
// variables
var $notice;
var $record; // �l�ment racine
var $type_obj;
var $ID;
var $index;
var $index_all;
var $bool_modif_index; // si vaut 1 l'index doit �tre reg�n�r� (initialisation ou modification des champs)

//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
function __construct ($parametres) {
    $this->type_obj=$parametres["type_obj"];
    $this->ID=$parametres["ID"];
    $this->bool_modif_index=1;
    //$this->index=array();
}  

/**
 * tvs_marcxml::load_notice()
 * 
 * importe une notice DomXML OU STRING
 * 
 * @param mixed $notice
 * @return void
 */
function load_notice ($notice) {
    if (is_string($notice)) {
        $this->notice = new DOMDocument();
        $this->notice->loadXML($notice);
        if ($this->notice === false) {
            return(false);
        }
    } else {
        $this->notice=$notice;
    }
    $this->record=$this->notice->documentElement;
}  


/**
 * tvs_marcxml::new_notice()
 * 
 * cr�e une nouvelle notice vierge <record></record>
 * 
 * @return void
 */
function new_notice () {
    $this->notice = new DOMDocument();
    $this->notice->preserveWhiteSpace = false;
    $this->record=$this->notice->createElement("record");
    $this->notice->appendchild($this->record);
}


/**
 * tvs_marcxml::nodelist_2_array()
 * 
 * convertit un DomNodeList en Array
 * 
 * @param mixed $nodelist // DomNodeList
 * @return array
 */
function nodelist_2_array ($nodelist) {
    $length=$nodelist->length;
    $retour=array();
    for ($i=0 ; $i<$length ; $i++) {
        array_push($retour, $nodelist->item($i));
    }
    return ($retour);
}

/**
 * tvs_marcxml::formate_element()
 * 
 * formate une chaine en y ajoutant un �l�ment � la fin avec �ventuellement des s�parateurs
 * $chaine est re�ue en param�tre, modifi�e par l'adjonction de $element et des s�parateurs
 * puis retourn�e
 * 
 * L'�l�ment peut �tre format� par un plugin (option). Dans ce cas, le texte de l'�l�ment sera fourni dans l''attribut [texte] et r�cup�r� dans l'attribut [texte]'
 * 
 * @param mixed $chaine // la chaine � transformer
 * @param mixed $element // l'�l�ment � ajouter
 * @param mixed $avant // � mettre avant l'�l�ment
 * @param mixed $apres // � mettre apr�s l'�l�ment
 * @param mixed $avant_verif // � mettre avant l'�l�ment si $chaine n'est pas vide
 * @param mixed $plugin_formate // �ventuellement un plugin pour formater l'�l�ment
 * 
 * @return �l�ment format� (string)
 */
function formate_element ($chaine, $element, $avant, $apres, $avant_verif, $plugin_formate) {
    if ($chaine != "") {
        $chaine.=$avant_verif;
    }
    if (is_array($plugin_formate)) {
        $tmp=applique_plugin($plugin_formate, array("texte"=>$element));
        if ($tmp["succes"]==1) {
            $element=$tmp["resultat"]["texte"];
        }
    }
    
    $chaine.=$avant.$element.$apres;
    return ($chaine);
}

/**
 * tvs_marcxml::get_nom_champ()
 * 
 * retourne le nom d'un champ (200, 210, 700...)
 * 
 * @param mixed $champ
 * 
 * @return nom du champ (100, 200...)
 */
function get_nom_champ ($champ) {
    $attributs=$champ->attributes;
    $tag_champ=$attributs->getNamedItem("tag")->nodeValue;
    return ($tag_champ);
}

/**
 * tvs_marcxml::get_nom_ss_champ()
 * 
 * Retourne le nom d'un sous-champ (a,b,c...)
 * 
 * @param mixed $ss_champ
 * 
 * @return nom du ss-champ (a,b,c...)
 */
function get_nom_ss_champ ($ss_champ) {
    $attributs=$ss_champ->attributes;
    $tag_ss_champ=$attributs->getNamedItem("code")->nodeValue;
    return ($tag_ss_champ);
}

/**
 * tvs_marcxml::get_valeur_ss_champ()
 * 
 * Retourne la valeur d'un ss-champ'
 * 
 * @param mixed $ss_champ
 * 
 * @return valeur du ss-champ
 */
function get_valeur_ss_champ ($ss_champ) {
    $valeur=$ss_champ->textContent;
    return ($valeur);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// g�n�ration de l'index
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function genere_index() {
    $this->index=array();
    $this->index_all=array();
    $tous_les_champs=$this->record->childNodes;
    $nb_champs=$tous_les_champs->length;
    for ($i=0 ; $i<$nb_champs ; $i++) {
        $champ=$tous_les_champs->item($i);
        $tag_champ=$this->get_nom_champ($champ);
        array_push($this->index_all, $champ);
        if ($tag_champ != "") {
            if (!is_array($this->index[$tag_champ])) {
                $this->index[$tag_champ]=array();
            }
            array_push($this->index[$tag_champ], $champ);
        }
    }
    $this->bool_modif_index=0;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// RECHERCHES DE CHAMPS / SOUS-CHAMPS
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////    

/**
 * tvs_marcxml::get_champs()
 * 
 * retourne des champs par nom et position dans la liste
 * 
 * Si $tag est vide => retourne tous les champs
 * 
 * @param mixed $tag // tag du champ
 * @param mixed $idx // position du champ ou last
 * 
 * @return array de champs
 */

function get_champs ($tag, $idx) {
//dbg_log ("get_champs_old - $tag - $idx");
    $tmp=array();
    $retour=array();
    $tous_les_champs=$this->record->childNodes;
    $nb_champs=$tous_les_champs->length;
    for ($i=0 ; $i<$nb_champs ; $i++) {
        $champ=$tous_les_champs->item($i);
        $tag_champ=$this->get_nom_champ($champ);
        if ($tag_champ == $tag OR $tag=="") {
            array_push($tmp, $champ);
        }
    }
    if (is_numeric($idx) AND $idx != 0) {
        if ($tmp[$idx-1] != "") {
            array_push($retour, $tmp[$idx-1]);
        }
    } elseif ($idx == "last") {
        array_push($retour, $tmp[count($tmp)-1]);
    } else {
        $retour=$tmp;
    }
    return ($retour);
    
}

function get_champs_xxx ($tag, $idx) {
dbg_log ("get_champs - $tag - $idx");
    $tmp=array();
    $retour=array();
    if ($this->bool_modif_index==1) {
        $this->genere_index();
    }
    
    if ($tag=="") {
        //return ($this->get_champs_old($tag, $idx));
        $tmp=$this->index_all;
    }
    
    if (is_array($this->index[$tag])) {
        $tmp=$this->index[$tag];
    }
    
    
    /**
    $tous_les_champs=$this->record->childNodes;
    $nb_champs=$tous_les_champs->length;
    for ($i=0 ; $i<$nb_champs ; $i++) {
        $champ=$tous_les_champs->item($i);
        $tag_champ=$this->get_nom_champ($champ);
        if ($tag_champ == $tag OR $tag=="") {
            array_push($tmp, $champ);
        }
    }
    **/
    if (is_numeric($idx) AND $idx != 0) {
        array_push($retour, $tmp[$idx-1]);
    } elseif ($idx == "last") {
        array_push($retour, $tmp[count($tmp)-1]);
    } else {
        $retour=$tmp;
    }
    return ($retour);
    
}

/**
 * tvs_marcxml::get_champ_unique()
 * 
 * retourne la premi�re occurence d'un champ (utile pour les champs non r�p�tables : �vite d'avoir ensuite � s�lectionner dans une liste)
 * 
 * 
 * @param mixed $tag
 * @param mixed $defaut
 * 
 * @return champ trouv�
 */
function get_champ_unique ($tag, $defaut) {
    $tmp=$this->get_champs($tag, "");
    if (count($tmp)==0) {
        return ($defaut);
    } else {
        return ($tmp[0]);
    }
}


/**
 * tvs_marcxml::get_ss_champ_unique()
 * 
 * retourne la permi�re occurence d'un ss-champ dans un champ
 * Utile pour les ss-champs non r�p�tables : �vite d'avoir � s�lectionner ensuite dans une liste
 * 
 * @param mixed $champ => le champ
 * @param mixed $code => code duss-champ
 * @param mixed $valeur => valeur du ss-champ (ou vide)
 * @param mixed $defaut => valeur � retourner si ss-champ pas trouv�
 * 
 * @return valeur du ss-champ ou $defaut si pas trouv�
 */
function get_ss_champ_unique ($champ, $code, $valeur, $defaut) {
    $tmp=$this->get_ss_champs($champ, $code, $valeur, "");
    if (count($tmp)==0) {
        return ($defaut);
    } else {
        return ($tmp[0]);
    }
}

/**
 * tvs_marcxml::get_ss_champs()
 * 
 * recherche des ss-champs dans un champ � partir de leur code et �ventuellement de leur valeur et de leur position dans la liste
 * 
 * si $code == "" => retourne ts les ss champs
 * 
 * @param mixed $champ // le champ dans lequel on va chercher les ss-champs
 * @param mixed $code // code du ss-champ
 * @param mixed $valeur // �ventuellement valeur du ss-champ
 * @param mixed $idx // �ventuellement position du ss-champ dans la liste ou last
 * 
 * @return liste de ss-champs (array)
 */

function get_ss_champs ($champ, $code, $valeur, $idx) {
    //dbg_log ("  get_ss_champ - $code - $idx - $valeur");
    $tmp=array();
    $retour=array();
    $tous_les_ss_champs=$champ->childNodes;
    $nb_ss_champs=$tous_les_ss_champs->length;
    for ($i=0 ; $i<$nb_ss_champs ; $i++) {
        $ss_champ=$tous_les_ss_champs->item($i);
        $tag_ss_champ=$this->get_nom_ss_champ($ss_champ);
        $valeur_ss_champ=$ss_champ->nodeValue;
        if ($tag_ss_champ == $code OR $code == "") {
            if ($valeur=="" OR $valeur==$valeur_ss_champ) {
                array_push($tmp, $ss_champ);
            }
        }
    }

    if (is_numeric($idx) AND $idx != 0) {
        array_push($retour, $tmp[$idx-1]);
    } elseif ($idx === "last") { // encore une de ces �tranget�s de PHP... si on ne met pas === il trouve toujours que $idx == "last" m�me si $idx vaut "0" :/
        array_push($retour, $tmp[count($tmp)-1]);
    } else {
        $retour=$tmp;
    }
    return ($retour);
    
}

/**
 * tvs_marcxml::get_champs_liste()
 * 
 * 
 * Cette m�thode retourne un tableau (!! PAS un nodelist) de tous les champs (DomNode) d'une notice pr�sentant certains crit�res :
 * le nom du champ (200, 700, 464...)
 * la pr�sence de certains sous-champs ($a, $b...)
 * La valeur des sous-champs
 * la position du champ ou du ss-champ dans la liste
 * NOTE : c'est une disjonction. Il suffit que la condition soit remplie pour UN sous sous-champ, et le champ sera valid� // p-� � modifier plus tard
 * 
 * @param array $parametres
 * @param ["champs"] => liste des champs � extraire
 * @param ["champs"][XXX]["tag"]=> 1 des champs
 * @param ["champs"][XXX]["idx"]=> position si plusieurs champs identiques : � partir de 1 ou "last()" pour le dernier
 * @param ["champs"][XXX]["sous-champs"]=> sous-champs devant �tre pr�sents pour extraire ce champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["code"]=> 1 des sous-champs
 * @param ["champs"][XXX]["sous-champs"][YYY]["valeur"]=> valeur que doit prendre le sous-champ. Si vide, on acc�de n'importe quelle valeur du moement que le ss-champ est pr�sent
 * @param ["champs"][XXX]["sous-champs"][YYY]["idx"]=> position si plusieurs ss-champs identiques : � partir de 1 ou "last()" pour le dernier
 * 
 * @return $retour => liste (array) des champs (array)
 */
function get_champs_liste ($parametres) {
    $champs=$parametres["champs"];
    $retour=array();
    foreach ($champs as $champ) { // pour chaque type de champ
        $liste1=$this->get_champs($champ["tag"], $champ["idx"]);
        foreach ($liste1 as $champ_node) {            
            if (!is_array($champ["sous-champs"])) {
                array_push ($retour, $champ_node);
            } else {
                foreach ($champ["sous-champs"] as $sous_champ) { // pour chaque ss-champ dont on va tester la valeur
                    $liste2=$this->get_ss_champs($champ_node, $sous_champ["code"], $sous_champ["valeur"], $sous_champ["idx"]);
                    if (count($liste2) > 0) {
                        array_push($retour, $champ_node);
                        break;
                    }
                } // fin du pour chaque ss-champ dont on va tester la valeur
            }
        } // fin du pour chaque champ de CE type
    } // fin du pour chaque type de champ
    return ($retour);
} 

////////////////////////////////////////////////////////////////////////////////////

/**
 * tvs_marcxml::get_liste_champs()
 * 
 * M�thode utilis�e pour le formatage de la notice par get_champs_formate_string()
 * retourne les champs demand�s ainsi que la d�finition des ss-champs � formater
 * 
 * Pour la description compl�te des param�tres, cf get_champs_formate_string()
 * 
 * Principaux param�tres utilis�s :
 * 
 * @param mixed $parametres
 * @param [sequentiel] : si vaut 1 les champs seront parcourus dans l'ordre
 * @param [champs] (array) : liste de champs, avaec pour chacun la d�finition n�cessaire 1) � la r�cup�ration du champ 2) � son formatage
 * @param [champs][0,1,2...][tag]
 * @param [champs][0,1,2...][idx]
 * @param [champs][0,1,2...][plugin_inclus]
 * @param [champs][0,1,2...][defaut]
 * 
 * @return array liste des champs trouv�s
 * @return [0,1,2][champ] => champ trouv�
 * @return [0,1,2][definition] => d�finition des ss-champs � chercher et formater
 */
function get_liste_champs ($parametres) {
    $retour=array();
    if ($parametres["sequentiel"] == "1") {
        $tous_les_champs=$this->get_champs("", "");
        foreach ($tous_les_champs as $champ) {
            $nom_champ=$this->get_nom_champ($champ);
            if (isset($parametres["champs"][$nom_champ])) {
                array_push ($retour, array("champ"=>$champ, "definition"=>$parametres["champs"][$nom_champ]));
            } elseif (isset($parametres["champs"]["defaut"])) {
                array_push ($retour, array("champ"=>$champ, "definition"=>$parametres["champs"]["defaut"]));
            }
        }
    } else {
        foreach ($parametres["champs"] as $definition) {
            $nom_champ=$definition["tag"];
            $idx_champ=$definition["idx"];
            $plugin_inclus=$definition["plugin_inclus"];
            if ($plugin_inclus != "") {
                array_push ($retour, array("champ"=>"", "definition"=>$definition));
            } else {
                $tous_les_champs=$this->get_champs($definition["tag"], $definition["idx"]);
                if (count($tous_les_champs) > 0) {
                    foreach ($tous_les_champs as $champ) {
                        array_push ($retour, array("champ"=>$champ, "definition"=>$definition));
                    }
                } elseif ($definition["defaut"] != "") {
                    array_push ($retour, array("champ"=>"", "definition"=>$definition));
                } 
            }
        }
    }
    return ($retour);
}

////////////////////////////////////////////////////////////////////////////////////

/**
 * tvs_marcxml::get_liste_ss_champs()
 * 
 * m�thode utilis�e pour la r�cup�ration et le formatage des ss-champs dans get_champs_formate_string()
 * Pour le champ fourni, on r�cup�re les ss-champs demand�s ainsi que la d�finition n�cessaire � leur formatage
 * 
 * Pour la description compl�te des param�tres, cf get_champs_formate_string()
 * 
 * Principaux param�tres utilis�s :
 * 
 * @param mixed $champ
 * @param mixed $parametres
 * 
 * [sous-champs][0,1,2...][code]
 * [sous-champs][0,1,2...][idx]
 * [sous-champs][0,1,2...][plugin_inclus]
 * [sous-champs][0,1,2...][valeur]
 * [sous-champs][0,1,2...][defaut]
 * 
 * @return
 */
function get_liste_ss_champs ($champ, $parametres) {
    $retour=array();
    if ($parametres["sequentiel"] == "1") {
        $tous_les_ss_champs=$this->get_ss_champs($champ, "", "", "");
        foreach ($tous_les_ss_champs as $ss_champ) {
            $nom_ss_champ=$this->get_nom_ss_champ($ss_champ);
            if (isset($parametres["sous-champs"][$nom_ss_champ])) {
                array_push ($retour, array("ss_champ"=>$ss_champ, "definition"=>$parametres["sous-champs"][$nom_ss_champ]));
            } elseif (isset($parametres["sous-champs"]["defaut"])) {
                array_push ($retour, array("champ"=>$champ, "definition"=>$parametres["sous-champs"]["defaut"]));
            }
        }
    } else {
        foreach ($parametres["sous-champs"] as $definition) {
            $nom_ss_champ=$definition["code"];
            $idx_ss_champ=$definition["idx"];
            $plugin_inclus=$definition["plugin_inclus"];
            if ($plugin_inclus != "") {
                array_push ($retour, array("ss_champ"=>"", "definition"=>$definition));
            } else {
                $tous_les_ss_champs=$this->get_ss_champs($champ, $definition["code"], $definition["valeur"], $definition["idx"]);
                if (count($tous_les_ss_champs) > 0) {
                    foreach ($tous_les_ss_champs as $ss_champ) {
                        array_push ($retour, array("ss_champ"=>$ss_champ, "definition"=>$definition));
                    }
                } elseif ($definition["defaut"] != "" OR $definition["bool_affiche_vide"] == 1) {
                    array_push ($retour, array("ss_champ"=>"", "definition"=>$definition));
                } 
            }
        }
    }
    return ($retour); 
}

////////////////////////////////////////////////////////////////////////////////////
/**
 * tvs_marcxml::get_champs_formate_string()
 * 
 * Cette m�thode retourne des champs et sous-champs format�s sous forme de string.
 * 
 * Le formatage peut se faire soit dans l'ordre des champs/ss-champs fournis dans le registre, soit dans l'odre des champs/ss-champs catalogu�s
 * C'est par exemple utilse pour formater le champ 200 o� l'ordre des ss-champs compte.
 * Dans ce cas, on met une clef [sequentiel] = 1 au niveau des champs ou des ss-champs (cf ci-dessous)
 * On sp�cifie les noms des champs / ss-champs sans fioriture (par exemple [a] et non pas [001 - a])
 * et pas besoin de sp�cifier [tag] ou [code] (selon le cas)
 * 
 * @param array $parametres
 * 
 * @param ["sequentiel"] => si vaut 1, les champs sont pris dans l'ordre de catalogage (attention : mettre le nom des champs sans fioriture : "200" pas "001 - 200") et pas besoin de mettre [tag]
 * @param ["defaut"] => valeur par d�faut pour l'ensemble du plugin
 * @param ["champs"] => liste des champs � extraire
 * @param ["avant|apres"] => chaines de caract�res � placer avant ou apr�s l'ensemble du contenu format�
 * @param ["champs"][XXX]["plugin_inclus"]=> on peut ins�rer le contenu d'un plugin en lieu et place d'infos extraites de la notice [texte] plugin_inclus [notice][champ] ([champ] = d�finition du champ)
 * @param ["champs"][XXX]["tag"]=> 1 des champs
 * @param ["champs"][XXX]["idx"]=> position du champ dans la liste ou last
 * @param ["champs"][XXX][avant|avant_verif|apres] => chaines de caract�res � placer avant, avant (si d�j� qqchse avant) ou apr�s le contenu du champ
 * @param ["champs"][XXX][plugin_formate] => plugin pour formater le contenu du champ g�n�r� [texte]plugin_formate[texte]
 * @param ["champs"][XXX][defaut] => Valeur par d�faut � retourner si aucun champ n'est trouv�
 * @param ["champs"][XXX][sequentiel] => si vaut 1, les ss-champs seront pris dans l'ordre de catalogage (pour le champ 200...) (attention : mettre le nom des ss-champs sans fioriture : "a" pas "001 - a") et pas besoin de mettre [code]
 * @param ["champs"][XXX]["sous-champs"]=> sous-champs � extraire pour ce champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["plugin_inclus"]=>  on peut ins�rer le contenu d'un plugin en lieu et place d'infos extraites de la notice [texte] plugin_inclus [notice][sous_champ] ([sous_champ] = d�finition du sous champ)
 * @param ["champs"][XXX]["sous-champs"][YYY]["code"]=> 1 des sous-champs
 * @param ["champs"][XXX]["sous-champs"][YYY]["idx"]=> position du ss-champ dans la liste ou last
 * @param ["champs"][XXX]["sous-champs"][YYY]["valeur"]=> Valeur requise pour un sous-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][avant|avant_verif|apres] => chaines de caract�res � placer avant, avant (si d�j� qqchse avant) ou apr�s le contenu du ss-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][plugin_formate] => le contenu du ss-champ peut �tre format� par un plugin [texte] plugin_formate [texte]
 * @param ["champs"][XXX]["sous-champs"][YYY][defaut] => valeur par d�faut � retourner si aucun ss-champ trouv�
 * @param ["champs"][XXX]["sous-champs"][YYY][bool_affiche_vide] => si vaut 1, on pourra formater un ss-champ vide ou inexistant
 * 
 * @return string
 * 
 * @return
 */ 

function get_champs_formate_string ($parametres) {
    $liste_champs=$this->get_liste_champs($parametres); // on r�cup�re la liste des champs et leur d�finition de formatage
    $retour="";
    foreach ($liste_champs as $tmp_champ) { // pour chaque champ
        $champ=$tmp_champ["champ"];
        $definition_champ=$tmp_champ["definition"];
        if ($definition_champ["plugin_inclus"] != "") { // Si plugin inclus...
            $tmp=applique_plugin($definition_champ["plugin_inclus"], array("notice"=>$this->notice, "champ"=>$definition_champ));
            if ($tmp["succes"] == 1) {
                $str_tmp=$tmp["resultat"]["texte"];
            } else {
                $str_tmp="#erreur#";
            }
            $retour=$this->formate_element($retour, $str_tmp, $definition_champ["avant"], $definition_champ["apres"], $definition_champ["avant_verif"], $definition_champ["plugin_formate"]);
        } elseif ($champ=="" AND $definition_champ != "") { // si rien trouv� mais defaut d�fini
            $retour=$this->formate_element($retour, $definition_champ["defaut"], $definition_champ["avant"], $definition_champ["apres"], $definition_champ["avant_verif"], $definition_champ["plugin_formate"]);
        } else { // si on a un champ
            $liste_ss_champs=$this->get_liste_ss_champs($champ, $definition_champ); // on r�cup�re la liste des ss_champs et leur d�finition
            $str_champ="";
            foreach ($liste_ss_champs as $tmp_ss_champ) { // pour chaque ss_champ...
                $ss_champ=$tmp_ss_champ["ss_champ"];
                $valeur_ss_champ=$this->get_valeur_ss_champ($ss_champ);
                $definition_ss_champ=$tmp_ss_champ["definition"];
                if ($definition_ss_champ["plugin_inclus"] != "") { // si plugin inclus
                    $tmp=applique_plugin($definition_ss_champ["plugin_inclus"], array("notice"=>$this->notice, "sous_champ"=>$ss_champ));
                    if ($tmp["succes"] == 1) {
                        $str_tmp=$tmp["resultat"]["texte"];
                    } else {
                        $str_tmp="#erreur#";
                    }
                    $str_champ=$this->formate_element($str_champ, $str_tmp, $definition_ss_champ["avant"], $definition_ss_champ["apres"], $definition_ss_champ["avant_verif"], $definition_ss_champ["plugin_formate"]);
                } elseif ($valeur_ss_champ=="" AND $definition_ss_champ["defaut"] != "" ) { // si sous champ n'existe pas ou valeur nulle et valeur d�faut d�finie
                    $str_champ=$this->formate_element($str_champ, $definition_ss_champ["defaut"], $definition_ss_champ["avant"], $definition_ss_champ["apres"], $definition_ss_champ["avant_verif"], $definition_ss_champ["plugin_formate"]);
                } elseif ($valeur_ss_champ != "") { // si sous-champs avec une valeur
                    $str_champ=$this->formate_element($str_champ, $valeur_ss_champ, $definition_ss_champ["avant"], $definition_ss_champ["apres"], $definition_ss_champ["avant_verif"], $definition_ss_champ["plugin_formate"]);
                } else { // ss-champ n'existe pas ou valeur nulle, mais pas de valeur par d�faut d�finie
                    if ($definition_ss_champ["bool_affiche_vide"]==1) { // on formate un champ vide
                        $str_champ=$this->formate_element($str_champ, $valeur_ss_champ, $definition_ss_champ["avant"], $definition_ss_champ["apres"], $definition_ss_champ["avant_verif"], $definition_ss_champ["plugin_formate"]);
                    }
                }
            }
            if ($str_champ != "") { // si le champ est != "" on l'ajoute au reste ** TODO ** possibilit� de formater un champ vide (bool_affiche_vide pour le champ)
                $retour=$this->formate_element($retour, $str_champ, $definition_champ["avant"], $definition_champ["apres"], $definition_champ["avant_verif"], $definition_champ["plugin_formate"]);
            }
        }
    }
    if ($retour == "" AND $parametres["defaut"] != "") {
        $retour=$parametres["defaut"];
    }
    if ($retour != "") {
        $retour=$parametres["avant"].$retour.$parametres["apres"];
    }
    
    return ($retour);
} 


/**
 * tvs_marcxml::get_champs_formate_tableau()
 * 
 * m�me chose que get_champs_formate_string mais seuls les sous-champs sont format�s pour former un champ (string)
 * en revanche, les champs ne sont pas format�s entre eux mais retourn�s sous forme d'array comme :
 * [700]
 *      [0] => "Pennac, Daniel (1975-...)"
 *      [1] => "toto, tutu"
 * [600]
 *      [0] => "Histoire : France : xxx"
 * 
 * @return
 */
function get_champs_formate_tableau () {
    $champs=$parametres["champs"];
    $retour=array();
    foreach ($champs as $champ) { // pour chaque type de champ
        $liste1=$this->get_champs($champ["tag"], $champ["idx"]);
        foreach ($liste1 as $champ_node) {  // pour chaque champ de CE type      
            $str_champ="";  // chaine format�e pour ce champ  
            foreach ($champ["sous-champs"] as $sous_champ) { // pour chaque ss-champ dont on va tester la valeur
                $liste2=$this->get_ss_champs($champ_node, $sous_champ["code"], $sous_champ["valeur"], $sous_champ["idx"]);
                if (count($liste2) == 0 AND $sous_champ["defaut"] != "") {
                    $str_champ=$this->formate_element ($str_champ, $sous_champ["defaut"], $sous_champ["avant"], $sous_champ["apres"], $sous_champ["avant_verif"], $sous_champ["plugin_formate"]);
                }
                foreach ($liste2 as $ss_champ_node) {
                    $contenu_ss_champ=$ss_champ_node->textContent;
                    $str_champ=$this->formate_element ($str_champ, $contenu_ss_champ, $sous_champ["avant"], $sous_champ["apres"], $sous_champ["avant_verif"], $sous_champ["plugin_formate"]);
                }
            } // fin du pour chaque ss-champ dont on va tester la valeur
            if (! is_array($retour[$champ["tag"]])) {
                $retour[$champ["tag"]]=array();
            }
            array_push($retour[$champ["tag"]], $str_champ);
        } // fin du pour chaque champ de CE type
    } // fin du pour chaque type de champ
    return ($retour);
} 
    
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// AJOUT / SUPPRESSION DE CHAMPS / SOUS-CHAMPS
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////   
 
/**
 * tvs_marcxml::get_pos_insere_champ()
 * 
 * Cette m�thode permet de d�terminer � quel endroit ins�rer un champ.
 * On ins�re le champ juste avant le 1er champ strictement sup�rieur au champ � ins�rer
 * ou bien tout � la fin s'il n'y a aucun champ ou aucun champ strictement sup�rieur
 * Si un champ sup�rieur est trouv�, on retourne ce champ (qu'on utilisera avec la m�thode insertBefore())
 * Sinon, on retourne "last" et il faudra alors utiliser la m�thode appendChild()
 * 
 * @param mixed $tag
 * 
 * @return DomNode OU "last"
 */
function get_pos_insere_champ ($tag) {
    $tous_les_champs=$this->record->childNodes;
    $nb_champs=$tous_les_champs->length;
    if ($nb_champs == 0) {
        return ("last");
    }
    for ($i=0 ; $i<$nb_champs ; $i++) {
        $champ=$tous_les_champs->item($i);
        $attributs=$champ->attributes;
        $tag_champ=$attributs->getNamedItem("tag")->nodeValue;
        if ($tag_champ > $tag) {
            return ($champ);
        }
    }
    return ("last");
} 

/**
 * tvs_marcxml::get_pos_insere_ss_champ()
 * 
 * Cette m�thode permet de d�terminer � quel endroit ins�rer un ss-champ.
 * On ins�re le ss-champ juste avant le 1er ss-champ strictement sup�rieur au ss-champ � ins�rer
 * ou bien tout � la fin s'il n'y a aucun ss-champ ou aucun ss-champ strictement sup�rieur
 * Si un ss-champ sup�rieur est trouv�, on retourne ce ss-champ (qu'on utilisera avec la m�thode insertBefore())
 * Sinon, on retourne "last" et il faudra alors utiliser la m�thode appendChild()
 * 
 * @param mixed $code
 * @param DomNode $champ
 * 
 * @return DomNode OU "last"
 */
function get_pos_insere_ss_champ ($champ, $code) {
    $tous_les_ss_champs=$champ->childNodes;
    $nb_ss_champs=$tous_les_ss_champs->length;
    if ($nb_ss_champs == 0) {
        return ("last");
    }
    for ($i=0 ; $i<$nb_ss_champs ; $i++) {
        $ss_champ=$tous_les_ss_champs->item($i);
        $attributs=$ss_champ->attributes;
        $code_ss_champ=$attributs->getNamedItem("code")->nodeValue;
        if ($code_ss_champ > $code) {
            return ($ss_champ);
        }
    }
    return ("last");
} 

/**
 * tvs_marcxml::champ_2_definition()
 * 
 * Retourne la d�finition d'un champ sous la forme d'une array [0,1,2...][code|valeur]
 * 
 * @param mixed $champ
 * @return array
 */
function champ_2_definition ($champ) {
    $retour=array();
    $ss_champs=$this->get_ss_champs($champ, "", "", "");
    foreach ($ss_champs as $ss_champ) {
        $tmp=array();
        $code=$this->get_nom_ss_champ($ss_champ);
        $valeur=$this->get_valeur_ss_champ($ss_champ);
        $tmp=array("code"=>$code, "valeur"=>$valeur);
        array_push ($retour, $tmp);
    }
    return ($retour);
}


/**
 * tvs_marcxml::add_champ()
 * 
 * Cette m�thode ajoute un champ avec une d�finition :
 * [0,1,2...][code|valeur]
 * On peut sp�cifier l'emplacement o� ins�rer, sinon, on l'ins�re � sa place dans l'ordre des champs.
 * 
 * @param mixed $tag // le tag du champ � ins�rer
 * @param mixed $definition // la d�finition des ss-champs
 * @param mixed $insert_before // (option) champ avant lequel faire l'insertion ou "last" pour le mettre en dernier
 * @return void
 */
function add_champ ($tag, $definition, $insert_before) {
    if ($tag === "") {
        return("");
    }
    $champ=$this->notice->createElement("datafield");
    $champ->setAttribute("tag", $tag);
    foreach ($definition as $def_ss_champ) {
        $code=$def_ss_champ["code"];
        $valeur=$def_ss_champ["valeur"];
        $ss_champ_insert_before=$def_ss_champ["insert_before"];
        $this->add_ss_champ($champ, $code, $valeur, $ss_champ_insert_before);
    }
    
    if ($insert_before == "") {
        $insert_before=$this->get_pos_insere_champ($tag);
    }
    
    if ($insert_before == "last") {
        $this->record->appendChild($champ);
    } else {
        $this->record->insertBefore($champ, $insert_before);
    } 
    $this->bool_modif_index=1; // il faudra reg�n�rer l'index
    return ($champ);
}


/**
 * tvs_marcxml::add_ss_champ()
 * 
 * Cette m�thode ins�re un ss-champ dans un champ
 * Si l'emplacement n'est pas sp�cifi�, le ss-champ s'ins�re � sa place dans l'ordre des sous-champs
 * 
 * @param mixed $champ // le champ o� ins�er le ss-champ
 * @param mixed $code // code du ss-champ
 * @param mixed $valeur // valeur du ss-champ
 * @param mixed $insert_before // (option) ss-champ avant lequel ins�rer ou � la fin si "last"
 * @return void
 */
function add_ss_champ ($champ, $code, $valeur, $insert_before) {
    if ($code === "") {
        return ("");
    }
    if ($valeur === "0" OR $valeur === 0) {
        // on ne fait rien
    } elseif ($valeur == "" ) { // si sous-champ vide on ne le cr��e pas ???? (est-ce que c'est toujours pertinent ??)
        //return ("");
    }
    $valeur=(string)$valeur;
    $ss_champ=$this->notice->createElement("subfield", $valeur);
    $ss_champ->setAttribute("code", $code);
    
    if ($insert_before == "") {
        $insert_before=$this->get_pos_insere_ss_champ($champ, $code);
    }
    
    if ($insert_before == "last") {
        $champ->appendChild($ss_champ);
    } else {
        $champ->insertBefore($ss_champ, $insert_before);
    } 
}

/**
 * tvs_marcxml::empty_champ()
 * 
 * Supprime tous les sous-champs d'un champ
 * 
 * @param mixed $champ
 * @return void
 */
function empty_champ ($champ) {
    $ss_champs=$champ->childNodes;
    $nb_ss_champs=$ss_champs->length;
    for ($i=$nb_ss_champs-1 ; $i>=0 ; $i--) {
        $ss_champ=$ss_champs->item($i);
        $champ->removeChild($ss_champ);
    }
}

/**
 * tvs_marcxml::reset_champ()
 * 
 * R�initialise un champ.
 * Efface les anciens sous-champs et les remplace par les nouveaux fournis dans la d�finition
 * de la forme [0,1,...][code|valeur]
 * 
 * @param mixed $champ
 * @param mixed $definition
 * @return void
 */
function reset_champ ($champ, $definition) {
    $this->empty_champ($champ);
    foreach ($definition as $def_ss_champ) {
        $code=$def_ss_champ["code"];
        $valeur=$def_ss_champ["valeur"];
        $ss_champ=$this->notice->createElement("subfield", $valeur);
        $ss_champ->setAttribute("code", $code);
        $champ->appendChild($ss_champ);
    }
}


/**
 * tvs_marcxml::update_ss_champ()
 * 
 * modifie la valeur d'un ss-champ'
 * 
 * @param mixed $ss_champ
 * @param mixed $valeur
 * @return void
 */
function update_ss_champ ($ss_champ, $valeur) {
    $ss_champ->nodeValue=$valeur;
}


/**
 * tvs_marcxml::delete_ss_champ()
 * 
 * supprime un ss-champ
 * 
 * @param mixed $champ
 * @param mixed $ss_champ
 * @return void
 */
function delete_ss_champ ($champ, $ss_champ) {
    $champ->removeChild($ss_champ);
}

/**
 * tvs_marcxml::delete_champ()
 * 
 * supprime un champ
 * 
 * @param mixed $champ
 * @return void
 */
function delete_champ ($champ) {
    $this->record->removeChild($champ);
    $this->bool_modif_index=1; // il faudra reg�n�rer l'index
}

/**
 * tvs_marcxml::saveXML()
 * 
 * Retourne la notice XML sous forme de string (pour debugging et log)
 * 
 * @return string
 */
function saveXML () {
    $tmp=$this->notice->saveXML();
    return ($tmp);
}


/**
 * tvs_marcxml::add_champ_000()
 * 
 * rajoute le champ 000 � la notice (contient ID_notice ($a) et type_obj ($c))
 * 
 * @return void
 */
function add_champ_000 () {
    $champs_000=$this->get_champs("000", "");
    foreach ($champs_000 as $champ_000) {
        $this->delete_champ($champ_000);
    }
    $param=array(array("code"=>"a", "valeur"=>$this->ID), array("code"=>"b", "valeur"=>"0"), array("code"=>"c", "valeur"=>$this->type_obj));
    $this->add_champ("000", $param, "");
}


/**
 * tvs_marcxml::rename_champ()
 * 
 * renomme un champ (par ex. 606 => 61)
 * 
 * @param mixed $champ
 * @param mixed $tag
 * @return void
 */
function rename_champ ($champ, $tag) {
    $champ->attributes->getNamedItem("tag")->nodeValue=$tag;
    $this->bool_modif_index=1; // il faudra reg�n�rer l'index
}


/**
 * tvs_marcxml::rename_ss_champ()
 * 
 * renomme un ss-champ (par ex $a => $f)
 * 
 * @param mixed $ss_champ
 * @param mixed $code
 * @return void
 */
function rename_ss_champ ($ss_champ, $code) {
    $ss_champ->attributes->getNamedItem("code")->nodeValue=$code;
}

/**
 * tvs_marcxml::duplicate_ss_champ()
 * 
 * duplique un ss-champ en lui attribuant un code
 * 
 * @param mixed $ss_champ
 * @param mixed $code
 * @return void
 */
function duplicate_ss_champ ($champ, $ss_champ, $code) {
    $valeur=$this->get_valeur_ss_champ($ss_champ);
    $this->add_ss_champ($champ, $code, $valeur, "");
    
}

/**
 * tvs_marcxml::formate_ss_champ()
 * 
 * applique un plugin sur le contenu d'un ss-champ pour le modifier
 * la signature est [chaine]plugin_formate[chaine]
 * 
 * @param mixed $ss_champ
 * @param mixed $code
 * @return void
 */
function formate_ss_champ ($ss_champ, $plugin_formate) {
    $valeur=$this->get_valeur_ss_champ($ss_champ);
    $tmp=applique_plugin($plugin_formate, array("chaine"=>$valeur));
    if ($tmp["succes"]==1) {
        $valeur2=$tmp["resultat"]["chaine"];
        $this->update_ss_champ($ss_champ, $valeur2);
    }
  
}


/**
 * tvs_marcxml::nettoie_notice()
 * 
 * Cette fonction permet de nettoyer la notice � partir d'un filtre fourni.
 * On peut renommer des champs / sous-champs
 * supprimer des champs / sous-champs
 * le filtre doit avoir la forme suivante :
 * [200, 210, 700, ...][renommer] => 701 (renommera par ex. un champ 700 en 701)
 *                     [supprimer] => si vaut 1, le champ est supprim� 
 *                     [ss_champs][a,b,f...][renommer] => z (renommera un $a en $z)
 *                     [ss_champs][a,b,f...][supprimer] => si vaut 1, ss_champ supprim�
 * 
 * @param mixed $filtre
 * @return void
 */
function nettoie_notice ($filtre) {
    foreach ($filtre as $nom_champ_filtre => $infos_champ_filtre) {
        $champs=$this->get_champs($nom_champ_filtre, "");
        foreach ($champs as $champ) {
            if ($infos_champ_filtre["renommer"] != "") {
                $this->rename_champ($champ, $infos_champ_filtre["renommer"]);
            }
            if ($infos_champ_filtre["supprimer"] == 1) {
                $this->delete_champ($champ);
            } elseif (is_array($infos_champ_filtre["ss_champs"])) {
                foreach ($infos_champ_filtre["ss_champs"] as $nom_ss_champ_filtre => $infos_ss_champ_filtre) {
                    $ss_champs=$this->get_ss_champs($champ, $nom_ss_champ_filtre, "", "");
                    foreach ($ss_champs as $ss_champ) {
                        if ($infos_ss_champ_filtre["renommer"] != "") {
                            $this->rename_ss_champ($ss_champ, $infos_ss_champ_filtre["renommer"]);
                        }
                        if ($infos_ss_champ_filtre["supprimer"] != "") {
                            $this->delete_ss_champ($champ, $ss_champ);
                        }  
                        
                    }
                }
            }
        }
    }
}


    
} // fin de la classe
?>