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
 * @param SOIT [notice] => la notice � modifier
 * @param SOIT [tvs_marcxml] => la notice � modifier
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
    
    $notice=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $champ=$parametres["champ"];
    $champ_remplace=$parametres["champ_remplace"];
    $ss_champ_a_conserver=$parametres["ss_champs_a_conserver"];
    $nom_champ=$parametres["nom_champ"];
    
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    if (!is_array($ss_champ_a_conserver)) {
        $ss_champ_a_conserver=array();
    }
    
    // 1) on vide les ss-champs du champ SAUF les ss-champs � conserver
    $liste_ss_champs=$tvs_marcxml->get_ss_champs($champ, "", "", "");
    foreach ($liste_ss_champs as $ss_champ) { // pour chaque ss-champ du champ
        $code_ss_champ=$tvs_marcxml->get_nom_ss_champ($ss_champ);
        if (in_array($code_ss_champ, $ss_champ_a_conserver)) {
            // on ne fait rien
        } else {
            $tvs_marcxml->delete_ss_champ($champ, $ss_champ);
        }
    }
    
    //2)  On rajoute les nouveaux ss-champs
    $liste=explode ("|", $champ_remplace);
    foreach ($liste as $element) {
        if (strpos($element, ":") != 0) { // si pr�sence de ":" et pas en 1ere position
            $tmp=explode(":", $element, 2);
            $code=$tmp[0];
            $valeur=$tmp[1]; // peut �tre vide
            $tvs_marcxml->add_ss_champ($champ, $code, $valeur, "last");
        }
        
    }
    
    $retour["resultat"]["champ"]=$champ;
    return ($retour);

   
}


?>