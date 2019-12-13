<?php


/**
 * plugin_catalogue_marcxml_get_datafields_array()
 * 
 * Ce plugin retourne une liste de champs (datafields) comme get_data_fields, mais il le fait sous forme de tableau :
 * exemple :
 * [600]
 *     [0] => "toto : tutu : titi"
 *     [1] => "popo : pupu : pipi"
 * [606]
 *     [0] => ...
 * 
 * @param array $parametres
 * @param ["notice"] => notice en marcxml
 * @param ["tvs_marcxml"] => objet tvs_marcxml. Si non fourni, généré à partir de la notice (DomXml)
 * 
 * @param ["champs"] => liste des champs à extraire
 * @param ["champs"][XXX]["tag"]=> 1 des champs
 * @param ["champs"][XXX]["idx"]=> position si plusieurs champs identiques : à partir de 1 ou "last()" pour le dernier
 * @param ["champs"][XXX][avant|avant_verif|apres] => chaines de caractères à placer avant, avant (si déjà qqchse avant) ou après le contenu du champ
 * @param ["champs"][XXX]["sous-champs"]=> sous-champs à extraire pour ce champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["code"]=> 1 des sous-champs
 * @param ["champs"][XXX]["sous-champs"][YYY]["idx"]=> position du ss-champ dans la liste ou last()
 * @param ["champs"][XXX]["sous-champs"][YYY]["valeur"]=> Valeur requise pour un sous-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][avant|avant_verif|apres] => chaines de caractères à placer avant, avant (si déjà qqchse avant) ou après le contenu du ss-champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["plugin_formate"]=> Un plugin pour formater le ss-champ. Le texte sera envoyé dans l'attribut [texte] et récupéré également dans [texte]
 * 
 * @return $retour["resultat"]["texte"] => texte trouvé
 */
 
function plugin_catalogue_marcxml_get_datafields_array ($parametres) {
    $notice_xml=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice_xml);
    }
    
    $retour["resultat"]["texte"]=$tvs_marcxml->get_champs_formate_array($parametres);
    return ($retour);
}
 
 /**
function plugin_catalogue_marcxml_get_datafields_array ($parametres) {
    $notice_xml=$parametres["notice"];
    $champs=$parametres["champs"];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["texte"]=array();
    $xpath=new DOMXPath ($notice_xml);
    $tmp_type_champ=array(); // tableau contenant tous les types de champ (600,606...)
    foreach ($champs as $champ) { // pour chaque type de champ
        $tag=$champ["tag"];
        $expression1="/record/datafield[@tag='$tag']";
        $liste1=$xpath->query($expression1); // liste des champs de CE type
        $nb_champs=$liste1->length;
        $tmp_champ=array(); // tableau contenant tous les champs d'un même type [0,1,2...]
        for ($idx_champ=0 ; $idx_champ < $nb_champs ; $idx_champ++) { // Pour chaque champ de CE type
            $texte_champ="";
            foreach ($champ["sous-champs"] as $sous_champ) { // Pour chaque type de sous-champ
                $code=$sous_champ["code"];
                $expression2="subfield[@code='$code']";
                $liste2=$xpath->query($expression2, $liste1->item($idx_champ)); // liste des sous-champs de CE type
                $nb_ss_champs=$liste2->length;
                for ($idx_ss_champ=0 ; $idx_ss_champ < $nb_ss_champs ; $idx_ss_champ++) { // Pour chaque sous-champ de CE type
                    $tmp=$liste2->item($idx_ss_champ)->textContent;
                    if ($texte_champ != "") {
                        $texte_champ .= $sous_champ["avant_verif"];
                    }
                    $texte_champ.=$sous_champ["avant"].$tmp.$sous_champ["apres"];
                }
            } 
            if ($texte_champ != "") {
                array_push ($tmp_champ, $texte_champ);
            }
        }
        if (count ($tmp_champ) > 0) {
            $tmp_type_champ[$tag]=$tmp_champ;
        }
    }
    $retour["resultat"]["texte"]=$tmp_type_champ;
    return ($retour);
}
**/

?>