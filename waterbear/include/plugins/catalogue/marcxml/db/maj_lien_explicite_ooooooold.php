<?php
/**
 * plugin_catalogue_marcxml_db_maj_lien_explicite()
 * 
 * Ce plugin met à jour un champ de lien donné dans une notice. Il faut fournir la notice en XML et le champ de lien à remplacer sous forme de DomNode
 * (généralement fourni par le plugin get_datafiels_node)
 * On fournit également la liste des ss-champs à ne pas écraser
 * ATTENTION notice et champ doivent appartenir au même DOM
 * 
 * @param array $parametres
 * @param [notice] => la notice à modifier
 * @param [champ] => le champ à remplacer (DomNode)
 * @param [champ_remplace] => le nouveau champ (chaine de la forme a:xxx|b:yyy|b:zzz) retourné par un get_datafield
 * @param [ss_champs_a_conserver] => les ss_champs à ne pas écraser dans le champ
 * @param [nom_champ] => le nom du champ (700, 464...)
 * @return array
 */
function plugin_catalogue_marcxml_db_maj_lien_explicite($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $xpath=new DOMXPath ($parametres["notice"]);
    
    // 1) on commence à générer le champ avec les ss-champs fournis sous forme de STR
    $champ_str="";
    $liste=explode ("|", $parametres["champ_remplace"]);
    foreach ($liste as $element) {
        $tmp=explode(":", $element, 2);
        if (count($tmp)==2) {
            $code=$tmp[0];
            $valeur=$tmp[1];
            $champ_str.="<subfield code='$code'>$valeur</subfield>";
        }
    }
    
    // 2) on récupère les ss-champs à ne pas écraser et on les ajoue au champ STR
    if (is_array($parametres["ss_champs_a_conserver"])) {
        foreach ($parametres["ss_champs_a_conserver"] as $ss_champ_a_conserver) { // pour chaque type de ss-champ à conserver
            $expression="subfield[@code='$ss_champ_a_conserver']";
            $liste_a_conserver=$xpath->query($expression, $parametres["champ"]); // liste des sous-champs de CE type
            for ($j=0 ; $j < $liste_a_conserver->length ; $j++) { // pour chaque ss-champ de CE type
                $valeur_ss_champ=$liste_a_conserver->item($j)->textContent;
                if ($valeur_ss_champ != "") {
                    $champ_str.="<subfield code='$ss_champ_a_conserver'>$valeur_ss_champ</subfield>";
                }
            }
        }
    }
    $champ_str="<datafield tag='".$parametres["nom_champ"]."'>$champ_str</datafield>";
    
    // 3) on remplace le nouveau champ par l'ancien
    $nouveau_champ=new DOMDocument();
    $nouveau_champ->loadXML($champ_str);
    $nouveau_champ2=$nouveau_champ->getElementsByTagName("datafield")->item(0);
    $nouveau_champ2=$parametres["notice"]->importNode($nouveau_champ2, true);
    $parametres["champ"]->parentNode->replaceChild($nouveau_champ2, $parametres["champ"]);
    
    $retour["resultat"]["champ"]=$nouveau_champ2; // retourne le champ, mais juste pour tests, car la notice passée en paramètre est elle même modifiée
    
    return ($retour);
}


?>