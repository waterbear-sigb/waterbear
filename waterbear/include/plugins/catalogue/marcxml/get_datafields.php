<?php


/**
 * plugin_catalogue_marcxml_get_datafields()
 * 
 * Ce plugin retourne des champs et sous-champs format�s sous forme de string.
 * Le formatage (champs/sous champs � r�cup�rer et la ponctuation, s�parateurs...) est indiqu� dans l'attribut [champs]
 * La notice peut �tre fournie soit sous forme d'objet DomXml [notice] soit directement tvs_marcxml [tvs_marcxml] soit via [ID_notice] et [type_doc]
 * 
 * Le formatage peut se faire soit dans l'ordre des champs/ss-champs fournis dans le registre, soit dans l'odre des champs/ss-champs catalogu�s
 * C'est par exemple utilse pour formater le champ 200 o� l'ordre des ss-champs compte.
 * Dans ce cas, on met une clef [sequentiel] = 1 au niveau des champs ou des ss-champs (cf ci-dessous)
 * On sp�cifie les noms des champs / ss-champs sans fioriture (par exemple [a] et non pas [001 - a])
 * et pas besoin de sp�cifier [tag] ou [code] (selon le cas)
 * 
 * @param array $parametres
 * 
 * @param SOIT ["notice"] => notice en Domxml. (inutile si objet tvs_marcxml fourni)
 * @param SOIT ["tvs_marcxml"] => objet tvs_marcxml. Si non fourni, g�n�r� � partir de la notice (DomXml)
 * @param SOIT [ID_notice] et [type_doc]
 * 
 * @param ["sequentiel"] => si vaut 1, les champs sont pris dans l'ordre de catalogage (attention : mettre le nom des champs sans fioriture : "200" pas "001 - 200") et pas besoin de mettre [tag]
 * @param ["defaut"] => valeur par d�faut pour l'ensemble du plugin
 * @param ["champs"] => liste des champs � extraire
 * @param ["avant|apres"] => chaines de caract�res � placer avant ou apr�s l'ensemble du contenu format�
 * @param ["champs"][XXX]["plugin_inclus"]=> on peut ins�rer le contenu d'un plugin en lieu et place d'infos extraites de la notice [texte] plugin_inclus [notice][champ] ([champ] = d�finition du champ)
 * @param ["champs"][XXX]["tag"]=> 1 des champs
 * @param ["champs"][XXX]["idx"]=> position du champ dans la liste (commence � 1) ou last
 * @param ["champs"][XXX][avant|avant_verif|apres] => chaines de caract�res � placer avant, avant (si d�j� qqchse avant) ou apr�s le contenu du champ
 * @param ["champs"][XXX][plugin_formate] => plugin pour formater le contenu du champ g�n�r� [texte]plugin_formate[texte]
 * @param ["champs"][XXX][defaut] => Valeur par d�faut � retourner si aucun champ n'est trouv�
 * @param ["champs"][XXX][sequentiel] => si vaut 1, les ss-champs seront pris dans l'ordre de catalogage (pour le champ 200...) (attention : mettre le nom des ss-champs sans fioriture : "a" pas "001 - a") et pas besoin de mettre [code]
 * @param ["champs"][XXX]["sous-champs"]=> sous-champs � extraire pour ce champ
 * @param ["champs"][XXX]["sous-champs"][YYY]["plugin_inclus"]=>  on peut ins�rer le contenu d'un plugin en lieu et place d'infos extraites de la notice [texte] plugin_inclus [notice][sous_champ] ([sous_champ] = d�finition du sous champ)
 * @param ["champs"][XXX]["sous-champs"][YYY]["code"]=> 1 des sous-champs
 * @param ["champs"][XXX]["sous-champs"][YYY]["idx"]=> position du ss-champ dans la liste (commecne � 1) ou last
 * @param ["champs"][XXX]["sous-champs"][YYY]["valeur"]=> Valeur requise pour un sous-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][avant|avant_verif|apres] => chaines de caract�res � placer avant, avant (si d�j� qqchse avant) ou apr�s le contenu du ss-champ
 * @param ["champs"][XXX]["sous-champs"][YYY][plugin_formate] => le contenu du ss-champ peut �tre format� par un plugin [texte] plugin_formate [texte]
 * @param ["champs"][XXX]["sous-champs"][YYY][defaut] => valeur par d�faut � retourner si aucun ss-champ trouv�
 * @param ["champs"][XXX]["sous-champs"][YYY][bool_affiche_vide] => si vaut 1, on pourra formater un ss-champ vide ou inexistant
 * 
 * @return $retour["resultat"]["texte"] => texte trouv�
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