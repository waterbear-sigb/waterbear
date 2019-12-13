<?php

/**
 * plugin_transactions_resas_teste_quotas_reservataire()
 * 
 * Ce plugin permet de déterminer si une réservation est possible compte tenu du nombre de personnes (réservataires) ayant déjà réservé cette notice
 * 
 * Le teste s'effectue en partant des réservations liées à une notice bibliographique, mais elle se fait en se basant sur les infos
 * du 1er exemplaire de cette notice (où on récupère les critères section, type doc...)
 * L'arbre récupéré est plus simple que pour les quotas habituels. Un seul arbre (pas besoin d'en fusionner plusieurs puisque ce test n'est pas lié aux
 * abonnements de l'usager)
 * 
 * Il comprend 2 parties : [arbre] (sans "s") et [criteres]
 * Pour [criteres] on a [0,1,2...][emplacement]
 * Pour [arbre] on a [adulte, jeunesse ...][livre, cd ...] => un nombre
 * on a également [arbre][_defaut] qui contient un nombre par dédfaut 
 * 
 * @param mixed $parametres
 * @param [ID_notice] : ID de la notice biblio
 * @param [exemplaire] : exemplaire à tester (uniquement le 1er exemplaire) sous forme d'une ligne de DB
 * @param [plugin_get_resas] : plugin de recherche pour récupérer les résas en cours liées à cette notice biblio
 * @param [plugin_get_infos_quotas] : plugin pour récupérer [criteres] et [arbre]
 * 
 * @return
 * @return [depassement] si vaut 1 : échec 
 */
function plugin_transactions_resas_teste_quotas_reservataire ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
     $retour["resultat"]["depassement"]=0;
    
    $ID_notice=$parametres["ID_notice"]; // ID de la notice biblio
    $exemplaire=$parametres["exemplaire"];
    
    $plugin_get_resas=$parametres["plugin_get_resas"]; // retourne la liste des résas sous la forme de lignes de base de données
    $plugin_get_infos_quotas=$parametres["plugin_get_infos_quotas"]; // retourne [arbres] et [criteres]
    
    // 1) On récupère le nombre de résas en cours pour cette notice biblio
    $tmp=applique_plugin($plugin_get_resas, array("ID_notice"=>$ID_notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    
   
    // 2) On récupère l'arbre et les critères
    $tmp=applique_plugin($plugin_get_infos_quotas, array());
    if ($tmp["succes"] != 1) {
        $retour["succes"]=0;
        $retour["erreur"]="les informations de quotas ne sont pas parametrees";
        return ($retour);
    }
    $infos_quotas=$tmp["resultat"];
    $arbre=$infos_quotas["arbre"]; // ATTENTIOn pas de "s" : ici un seul arbre qui ne dépend pas de l'abonnement du lecteur
    $criteres=$infos_quotas["criteres"]; // les critères à rechercher dans l'arbre
    
    
    // 3) On récupère les critères pertinents liés à cet exemplaire
    $criteres_exe=array();
    foreach ($criteres as $critere_tmp) {
        $critere=$critere_tmp["emplacement"];
        array_push($criteres_exe, $exemplaire[$critere]);
    }
    
    // 4) On recherche le quota lié à ces critères. an a une valeur _defaut à la racine de l'arbre au cas où on ne trouverait aps le paramètre
    $chemin=implode("/", $criteres_exe);
    $quota=get_parametres_by_chemin($arbre, $chemin);
    if ($quota === false) {
        $quota=$arbre["_defaut"];
    }
    
    
    // 5) On compare le nb résas et le quota
    if ($nb_resas >= $quota) {
         $retour["resultat"]["depassement"]=1;
         
    }

    
    return ($retour);
}

?>