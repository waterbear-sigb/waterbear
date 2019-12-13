<?php


/**
 * plugin_catalogue_marcxml_get_datafields()
 * 
 * Ce plugin retourne des champs et sous-champs formatés sous forme de string.
 * Le formatage (champs/sous champs à récupérer et la ponctuation, séparateurs...) est indiqué dans l'attribut [champs]
 * La notice peut être fournie soit sous forme d'objet DomXml [notice] soit directement tvs_marcxml [tvs_marcxml] soit via [ID_notice] et [type_doc]
 * 
 * Le formatage peut se faire soit dans l'ordre des champs/ss-champs fournis dans le registre, soit dans l'odre des champs/ss-champs catalogués
 * C'est par exemple utilse pour formater le champ 200 où l'ordre des ss-champs compte.
 * Dans ce cas, on met une clef [sequentiel] = 1 au niveau des champs ou des ss-champs (cf ci-dessous)
 * On spécifie les noms des champs / ss-champs sans fioriture (par exemple [a] et non pas [001 - a])
 * et pas besoin de spécifier [tag] ou [code] (selon le cas)
 * 
 * @param array $parametres
 * 
 * @param SOIT ["notice"] => notice en Domxml. (inutile si objet tvs_marcxml fourni)
 * @param SOIT ["tvs_marcxml"] => objet tvs_marcxml. Si non fourni, généré à partir de la notice (DomXml)
 * @param SOIT [ID_notice] et [type_doc]
 * 
 * @param ["sequentiel"] => si vaut 1, les champs sont pris dans l'ordre de catalogage (attention : mettre le nom des champs sans fioriture : "200" pas "001 - 200") et pas besoin de mettre [tag]
 * @param ["defaut"] => valeur par défaut pour l'ensemble du plugin
 * @param ["champs"] => liste des champs à extraire
 * @param ["avant|apres"] => chaines de caractères à placer avant ou après l'ensemble du contenu formaté
 * @param ["champs"][XXX]["plugin_inclus"]=> on peut insérer le contenu d'un plugin en lieu et place d'infos extraites de la notice [texte] plugin_inclus [notice][champ] ([champ] = définition du champ)
 * @param ["champs"][XXX]["tag"]=> 1 des champs
 * @param ["champs"][XXX]["idx"]=> position du champ dans la liste (commence à 1) ou last
 * @param ["champs"][XXX][avant|avant_verif|apres] => chaines de caractères à placer avant, avant (si déjà qqchse avant) ou après le contenu du champ
 * @param ["champs"][XXX][plugin_formate] => plugin pour formater le contenu du champ généré [texte]plugin_formate[texte]
 * @param ["champs"][XXX][defaut] => Valeur par défaut à retourner si aucun champ n'est trouvé
 * @param ["champs"][XXX][sequentiel] => si vaut 1, les ss-champs seront pris dans l'ordre de catalogage (pour le champ 200...) (attention : mettre le nom des ss-champs sans fioriture : "a" pas "001 - a") et pas besoin de mettre [code]
 * @param ["champs"][XXX]["sous-champs"]=> sous-champs à extraire pour ce champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["plugin_inclus"]=>  on peut insérer le contenu d'un plugin en lieu et place d'infos extraites de la notice [texte] plugin_inclus [notice][sous_champ] ([sous_champ] = définition du sous champ)
 * @param ["champs"][XXX]["sous-champs"][YYY]["code"]=> 1 des sous-champs
 * @param ["champs"][XXX]["sous-champs"][YYY]["idx"]=> position du ss-champ dans la liste (commecne à 1) ou last
 * @param ["champs"][XXX]["sous-champs"][YYY]["valeur"]=> Valeur requise pour un sous-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][avant|avant_verif|apres] => chaines de caractères à placer avant, avant (si déjà qqchse avant) ou après le contenu du ss-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][plugin_formate] => le contenu du ss-champ peut être formaté par un plugin [texte] plugin_formate [texte]
 * @param ["champs"][XXX]["sous-champs"][YYY][defaut] => valeur par défaut à retourner si aucun ss-champ trouvé
 * @param ["champs"][XXX]["sous-champs"][YYY][bool_affiche_vide] => si vaut 1, on pourra formater un ss-champ vide ou inexistant
 * 
 * @return $retour["resultat"]["texte"] => texte trouvé
 */
 
function plugin_catalogue_marcxml_get_datafields ($parametres) {
    $notice_xml=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $ID_notice=$parametres["ID_notice"];
    $type_doc=$parametres["type_doc"];
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    if ($tvs_marcxml == "") {
        if ($notice_xml=="") {
            if ($ID_notice == "" OR $type_doc == "") {
                $retour["succes"]=0;
                $retour["erreur"]="@& get_datafields : Vous n'avez fourni aucune notice";
                return($retour);
            }
            $notice_xml=get_objet_xml_by_id($type_doc, $ID_notice);
        }
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice_xml);
    }

    $retour["resultat"]["texte"]=$tvs_marcxml->get_champs_formate_string($parametres);
    return ($retour);
}
 
 



?>