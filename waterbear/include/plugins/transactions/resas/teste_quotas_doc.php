<?php

/**
 * plugin_transactions_resas_teste_quotas_doc()
 * 
 * Ce plugin teste si la réservation d'un exemplaire donné est possible eu égard aux quotas de réservation du lecteur 
 * (nombre de résas effectuées par le lecteur sur les différents types de docs)
 * Ce test se base sur les infos de l'exemplaire (section, type doc...). Contrairement à la gestion des quotas de prêt, on ne peut pas accéder aux infos de la 
 * notice biblio, du lecteur... 
 * Par ailleurs, les infos de l'exemplaire sont dans un premier temps récupérés dans les notices de resa (pour générer l'arbre), puis dans la notice exe
 * (pour tester si un exemplaire donné peut être réservé). Comme ces informations (identiques) peuvent correspondre à des accès différents entre la notice 
 * de résa et la notice exe, on aura 2 paramètres pour les critères : [criteres_resa] et [criteres_exe]
 * 
 * Ce test n'est à effectuer que pour le premier exemplaire d'une notice bibliographique (on assume que les exemplaires suivants auront les mêmes critères : type doc, section...)
 * 
 * Le plugin commence par récupérer les différents quotas dans le registre, ainsi que les critères (par ex. type doc et section)
 * On récupère ensuite les infos sur les abonnements du lecteur : si ID_famille != "" on récupère les quotas du chef de famill, sinon ceux de l'individu. A partir de là, on voit quels sont les quotas actifs du lecteur et on les fusionne
 * ce qui donne un arbre vierge
 * On récupère ensuite la liste des résas en cours du lecteur, ce qui va permettre de générer l'arbre à jour du lecteur
 * Pour finir, on teste la résa de l'exemplaire à tester. ça retourne un arbre à jour (si OK), une durée de résa et éventuellement des erreurs 
 * 
 * @param [$exemplaire] // infos d'exemplaire sous la forme d'une ligne de la base de données
 * @param [$ID_lecteur]
 * @param [$ID_famille]
 * @param [$validation_message] // si "oui", forcer la résa
 * @param [$plugin_get_infos_quotas=] // retourne [arbres] et [criteres]
 * @param [$plugin_get_abos] // retourne la liste des abonnements sous la forme de lignes de base de données
 * @param [$plugin_traite_abonnements]
 * @param [$plugin_get_resas] // retourne la liste des résas sous la forme de lignes de base de données
 * @param [$plugin_resa_2_quota]
 * 
 * 
 * @return [arbre] => l'arbre maj
 * @return [duree] => durée de la résa
 * @return [depassement] => message d'erreur en cas de dépassement (indiquant le quota qui a bloqué...)
 */
function plugin_transactions_resas_teste_quotas_doc ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    
    $exemplaire=$parametres["exemplaire"]; // infos d'exemplaire sous la forme d'une ligne de la base de données
    $ID_lecteur=$parametres["ID_lecteur"];
    $ID_famille=$parametres["ID_famille"];
    $validation_message=$parametres["validation_message"];
    $plugin_get_infos_quotas=$parametres["plugin_get_infos_quotas"]; // retourne [arbres] et [criteres]
    $plugin_get_abos=$parametres["plugin_get_abos"]; // retourne la liste des abonnements sous la forme de lignes de base de données
    $plugin_traite_abonnements=$parametres["plugin_traite_abonnements"];
    $plugin_get_resas=$parametres["plugin_get_resas"]; // retourne la liste des résas sous la forme de lignes de base de données
    $plugin_resa_2_quota=$parametres["plugin_resa_2_quota"];
    
    // 1) On récupère les infos liées aux quotas et aux critères
    $tmp=applique_plugin($plugin_get_infos_quotas, array());
    if ($tmp["succes"] != 1) {
        $retour["succes"]=0;
        $retour["erreur"]="les informations de quotas ne sont pas parametrees";
        return ($retour);
    }
    $infos_quotas=$tmp["resultat"];
    //$arbres=$infos_quotas["arbres"];
    $criteres_resa=$infos_quotas["criteres_resa"]; // les critères à utiliser dans la notice de résa (pour générer l'arbre)')
    $criteres_exe=$infos_quotas["criteres_exe"]; // les critères à utiliser dans la notice d'exemplaire une fois que l'arbre est généré pour tester si l'exe est réservable
    
    // 2) On récupère les abonnements (et codes quotas) en cours
    if ($ID_famille == "" OR $ID_famille == "0") {
        $ID_famille=$ID_lecteur;
    }
    $tmp=applique_plugin($plugin_get_abos, array("ID_lecteur"=>$ID_famille));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nb_abos=$tmp["resultat"]["nb_notices"];
    $abos=$tmp["resultat"]["notices"];
    
    // 3) On génère un arbre des quotas vierge
    $tmp=applique_plugin($plugin_traite_abonnements, array("infos_abos"=>$abos, "infos_quotas"=>$infos_quotas));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $messages=$tmp["resultat"]["messages"];
    $arbre=$tmp["resultat"]["arbre"];
    
    // 4) On récupère la liste des réservations de ce lecteur
    $tmp=applique_plugin($plugin_get_resas, array("ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    
    // 5) On récupère l'arbre définitif en faisant une maj pour chaque résa
    foreach ($resas as $resa) {
        $tmp=applique_plugin($plugin_resa_2_quota, array("arbre"=>$arbre, "criteres"=>$criteres_resa, "validation_message"=>"oui", "bureau"=>$resa));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $arbre=$tmp["resultat"]["arbre"]; // arbre à jour
    }
    
    // 6) On teste pour voir si la résa de l'exemplaire est possible
    $tmp=applique_plugin($plugin_resa_2_quota, array("arbre"=>$arbre, "criteres"=>$criteres_exe, "validation_message"=>$validation_message, "bureau"=>$exemplaire));
    return($tmp);
}



?>