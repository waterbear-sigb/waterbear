<?php
/**
 * plugin_catalogue_marcxml_db_maj_lien_explicite()
 * 
 * Ce plugin met � jour un champ de lien donn� dans une notice. Il faut fournir la notice en XML et le champ de lien � remplacer sous forme de DomNode
 * (g�n�ralement fourni par le plugin get_datafiels_node)
 * On fournit �galement la liste des ss-champs � ne pas �craser
 * ATTENTION notice et champ doivent appartenir au m�me DOM
 * 
 * @param array $parametres
 * @param [notice] => la notice � modifier
 * @param [champ] => le champ � remplacer (DomNode)
 * @param [champ_remplace] => le nouveau champ (chaine de la forme a:xxx|b:yyy|b:zzz) retourn� par un get_datafield
 * @param [ss_champs_a_conserver] => les ss_champs � ne pas �craser dans le champ
 * @param [nom_champ] => le nom du champ (700, 464...)
 * @return array
 */
function plugin_catalogue_marcxml_db_maj_lien_explicite($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $xpath=new DOMXPath ($parametres["notice"]);
    
    // 1) on commence � g�n�rer le champ avec les ss-champs fournis sous forme de STR
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
    
    // 2) on r�cup�re les ss-champs � ne pas �craser et on les ajoue au champ STR
    if (is_array($parametres["ss_champs_a_conserver"])) {
        foreach ($parametres["ss_champs_a_conserver"] as $ss_champ_a_conserver) { // pour chaque type de ss-champ � conserver
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
    
    $retour["resultat"]["champ"]=$nouveau_champ2; // retourne le champ, mais juste pour tests, car la notice pass�e en param�tre est elle m�me modifi�e
    
    return ($retour);
}


?>