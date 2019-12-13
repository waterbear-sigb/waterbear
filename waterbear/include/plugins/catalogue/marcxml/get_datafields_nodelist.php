<?php


/**
 * plugin_catalogue_marcxml_get_datafields_nodelist()
 * 
 * 
 * Ce plugin retourne un tableau (!! PAS un nodelist) de tous les champs (DomNode) d'une notice présentant certains critères :
 * le nom du champ (200, 700, 464...)
 * la présence de certains sous-champs ($a, $b...)
 * La valeur des sous-champs
 * la position du champ ou du ss-champ dans la liste
 * NOTE : c'est une disjonction. Il suffit que la condition soit remplie pour UN sous sous-champ, et le champ sera validé // p-ê à modifier plus tard
 * La notice peut être fournie soit sous forme d'objet DomXml soit directement tvs_marcxml
 * 
 * @param array $parametres
 * 
 * @param ["notice"] => notice en marcxml. (inutile si objet tvs_marcxml fourni)
 * @param ["tvs_marcxml"] => objet tvs_marcxml. Si non fourni, généré à partir de la notice (DomXml)
 * 
 * @param ["champs"] => liste des champs à extraire
 * @param ["champs"][XXX]["tag"]=> 1 des champs
 * @param ["champs"][XXX]["idx"]=> position si plusieurs champs identiques : à partir de 1 ou "last()" pour le dernier
 * @param ["champs"][XXX]["plugin_formate"]=> Un plugin pour formater le champ. Le texte sera envoyé dans l'attribut [texte] et récupéré également dans [texte]
 * @param ["champs"][XXX]["sous-champs"]=> sous-champs devant être présents pour extraire ce champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["code"]=> 1 des sous-champs
 * @param ["champs"][XXX]["sous-champs"][YYY]["valeur"]=> valeur que doit prendre le sous-champ. Si vide, on accède n'importe quelle valeur du moement que le ss-champ est présent
 * @param ["champs"][XXX]["sous-champs"][YYY]["idx"]=> position si plusieurs ss-champs identiques : à partir de 1 ou "last()" pour le dernier
 * @param ["champs"][XXX]["sous-champs"][YYY]["plugin_formate"]=> Un plugin pour formater le ss-champ. Le texte sera envoyé dans l'attribut [texte] et récupéré également dans [texte]
 * 
 * @return $retour => liste (array) des champs (array)
 */

function plugin_catalogue_marcxml_get_datafields_nodelist ($parametres) {
    $notice_xml=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice_xml);
    }
    
    $retour["resultat"]["champs"]=$tvs_marcxml->get_champs_liste($parametres);
    return ($retour);
    
} 
 
 
 /**
function plugin_catalogue_marcxml_get_datafields_nodelist ($parametres) {
    $notice_xml=$parametres["notice"];
    $champs=$parametres["champs"];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["champs"]=array();
    $xpath=new DOMXPath ($notice_xml);
    foreach ($champs as $champ) { // pour chaque type de champ
        $tag=$champ["tag"];
        $expression1="/record/datafield[@tag='$tag']";
        $liste1=$xpath->query($expression1); // liste des champs de CE type
        $nb_champs=$liste1->length;
        for ($idx_champ=0 ; $idx_champ < $nb_champs ; $idx_champ++) { // Pour chaque champ de CE type
            $champ_node=$liste1->item($idx_champ);
            
            // est-ce que ce champ correspond aux critères pour être exporté
            // 1) si aucun critère de ss-champ => OK
            if (!is_array($champ["sous-champs"])) {
                array_push ($retour["resultat"]["champs"], $champ_node);
            } else {
                foreach ($champ["sous-champs"] as $sous_champ) { // pour chaque ss-champ dont on va tester la valeur
                    $code=$sous_champ["code"];
                    $valeur=$sous_champ["valeur"];
                    if ($valeur == "") { // si pas de critère d valeur
                        $expression2="subfield[@code='$code']";
                    } else { // si critère de valeur
                        $expression2="subfield[@code='$code' and . = '$valeur']";
                    }
                    $liste2=$xpath->query($expression2, $champ_node); // liste des sous-champs de CE type
                    $nb_ss_champs=$liste2->length;
                    if ($nb_ss_champs > 0) {
                        array_push($retour["resultat"]["champs"], $champ_node);
                        break;
                    }
                } // fin du pour chaque ss-champ dont on va tester la valeur
            }
        } // fin du pour chaque champ de CE type
    } // fin du pour chaque type de champ
    return ($retour);
} // fin du plugin

**/


?>