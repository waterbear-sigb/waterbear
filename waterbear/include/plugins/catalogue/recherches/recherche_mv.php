<?php

/**
 * plugin_catalogue_recherches_recherche_mv()
 * 
 * Ce plugin est utilis� pour effectuer une recherche dans un champ multivaleurs (par exemple le champ des acc�s auteurs d'une notice biblio)
 * Il utilise un plugin pour la recherche (qui retournera juste UN champ par notice)
 * Puis un plugin pour mettre en forme ces champs, c'est � dire r�cup�rer les mots pertinents et les remettre dans leur contexte
 * Il se peut que le plugin trouve des doublons (le m�me auteur dans plusieurs notices). Dans ce cas, il relance la recherche pour les notices suivantes
 * Sauf si [nb_max] est d�pass� (pour �viter qu'on parcourts des centaines de notices ce qui ralentirait)
 * 
 * @param mixed $parametres
 * @param [query] => chaine � rechercher
 * @param [plugin_recherche] => le plugin utilis� pour faire la recherche
 * @param [plugin_formate] => Plugin pour formater chaque ligne (mettre en valeur les infos pertinentes)
 * @param [nb_resultats] => Nombre de notices � retourner
 * @param [nb_max] => nombre maximum de pages � parcourir avant d'abandonner si on n'a pas nb_resultats (cas o� on aurait beaucoup de doublons)
 * @return array
 */
function plugin_catalogue_recherches_recherche_mv ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // parametres
    $plugin_recherche=$parametres["plugin_recherche"];
    $plugin_formate=$parametres["plugin_formate"];
    $nb_resultats=$parametres["nb_resultats"];
    $nb_max=$parametres["nb_max"];
    $query=$parametres["query"];
    
    // on sp�cifie combien on veut retourner de notices (si pas de doublons, ne sera fait qu'une seule fois)
    $plugin_recherche["alias"]["nb_notices_par_page"]="param_recherche/nb_notices_par_page";
    $resultats=array();
    $resultats_mots=array();
    $bool_continue=true;
    $page=1;
    while ($bool_continue==true)  { // � faire jusqu'� ce qu'on ait tout trouv� ou qu'on abandonne
        
        // 1) �ventuellement, on modifie la pagination
        $plugin_recherche["alias"]["page"]="param_recherche/page";
        
        // 2) On effectue la recherche
        $tmp=applique_plugin ($plugin_recherche, array("query"=>$query, "page"=>$page, "nb_notices_par_page"=>$nb_resultats));
        if ($tmp["succes"] != 1) {
            $retour=$tmp;
            return ($tmp);
        }
        $nb_notices=$tmp["resultat"]["nb_notices"];
        $nb_pages=$tmp["resultat"]["nb_pages"];
        $notices=$tmp["resultat"]["notices"]; // sous la forme [0,1,2...][nom | id] mais on utilise pas id (puisqu'on d�doublonne)
        
        // 3) Dans chaque notice, on r�cup�re les infos pertinentes
        foreach ($notices as $notice) {
            $tmp=applique_plugin($plugin_formate, array("chaine"=>$notice["nom"], "motif"=>$query));
                if ($tmp["succes"] != 1) {
                $retour=$tmp;
                return ($tmp);
            }
            $chaine=strtolower($tmp["resultat"]["chaine"]);
            $mot=strtolower($tmp["resultat"]["mot"]);
            
            // 4) On d�doublonne
            if (! in_array($chaine, $resultats) AND $chaine != "") {
                array_push ($resultats, $chaine);
            }
            if (! in_array($mot, $resultats_mots) AND $mot != "") {
                array_push ($resultats_mots, $mot);
            }
        }
        
        
        
        // 5) On regarde si on a trouv� ce qu'il fallait ou s'il faut continuer
        //    Si on a trouv� ce qu'on voulait (nb_resultats) OU si on a parcouru toutes les pages (nb_pages) OU si on a atteint la limite (nb_max) 
        if (count($resultats) >= $nb_resultats OR $page >= $nb_pages OR $page >= $nb_max) {
            $bool_continue=false;
        }
        
        $page++;
    } // fin du while

    // on combine mots et segments
       
    foreach ($resultats as $elem) {
        if (! in_array($elem, $resultats_mots_min)) {
            array_push ($resultats_mots, $elem);
        }
    }

    $retour["resultat"]["notices"]=$resultats_mots;
    return ($retour);
}



?>