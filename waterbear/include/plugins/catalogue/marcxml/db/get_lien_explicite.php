<?php
 
/**
 * 
 * DEPLACE EN /MARCXML/DB/
 * 
 * plugin_catalogue_marcxml_db_get_lien_explicite()
 * 
 * Ce plugin g�n�re un champ de lien explicite � partir des r�f�rences d'une notice (type et ID)
 * OU � partir de la notice directement
 * Le retour est sous forme de tableau (qui pourra ensuite �tre converti en XML ou utilis� en JS)
 * Le plugin utilise un autre plugin pour r�cup�rer les infos et les formater
 * PAR CONTRE, il ne g�re pas les sous-champs � conserver. �a doit �tre fait par la fonction appelante
 * 
 * @param mixed $parametres
 * @param type => type de la notice (biblio, auteur...)
 * @param ID => ID de la notice 
 * @param OU notice => la notice elle m�me (objet DOMXml))
 * @param plugin_formate => le plugin qui va r�cup�rer et mettre en forme les bons champs dans la notice
 * @param OU [nouveau_champ_str] => on peut passer le nouveau champ directement (a d�j� �t� format� par la fonction appelante)
 * @return array
 * @return [resultat][champ][0,1,...][code]=>le code du sous champ
 * @return [resultat][champ][0,1,...][valeur]=>lvaleur du sous champ
 */
function plugin_catalogue_marcxml_db_get_lien_explicite($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["champ"]=array();
    $type=$parametres["type"];
    $ID=$parametres["ID"];
    $plugin_formate=$parametres["plugin_formate"];
    
    // 1) on r�cup�re l'objet li�
    if ($parametres["notice"] != "") {
        $notice_liee=$parametres["notice"];
    } else {
        /**
        $notice_liee_db=get_objet_by_id($type, $ID);
        $notice_liee_str=$notice_liee_db["contenu"];
        $notice_liee=new DOMDocument();
        $notice_liee->preserveWhiteSpace = false;
        $notice_liee->loadXML($notice_liee_str);
        **/
        $notice_liee=get_objet_xml_by_id($type, $ID);
    }
    
    // 2) On extrait les infos de l'objet li� gr�ce au plugin de formatage d�clar�
    //    r�cup�r� sous forme de chaine : a:xxx|b:yyy|b:zzz ...    
    if ($parametres["nouveau_champ_str"] == "") {
        $tmp=applique_plugin($plugin_formate, array("notice"=>$notice_liee));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $nouveau_champ_str=$tmp["resultat"]["texte"];
    } else {
        $nouveau_champ_str=$parametres["nouveau_champ_str"];
    }
    
    // 3) On convertit la chaine en array
    $liste=explode ("|", $nouveau_champ_str);
    foreach ($liste as $element) {
        if (strpos($element, ":") != 0) { // si pr�sence de ":" et pas en 1ere position
            $tmp=explode(":", $element, 2);
            $code=$tmp[0];
            $valeur=$tmp[1]; // peut �tre vide
            array_push($retour["resultat"]["champ"], array("code"=>$code, "valeur"=>$valeur));
        }
    }
    return ($retour);
    
}



?>