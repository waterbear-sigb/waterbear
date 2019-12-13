<?php

/**
 * plugin_transactions_resas_teste_quotas_reservataire()
 * 
 * Ce plugin permet de d�terminer si une r�servation est possible compte tenu du nombre de personnes (r�servataires) ayant d�j� r�serv� cette notice
 * 
 * Le teste s'effectue en partant des r�servations li�es � une notice bibliographique, mais elle se fait en se basant sur les infos
 * du 1er exemplaire de cette notice (o� on r�cup�re les crit�res section, type doc...)
 * L'arbre r�cup�r� est plus simple que pour les quotas habituels. Un seul arbre (pas besoin d'en fusionner plusieurs puisque ce test n'est pas li� aux
 * abonnements de l'usager)
 * 
 * Il comprend 2 parties : [arbre] (sans "s") et [criteres]
 * Pour [criteres] on a [0,1,2...][emplacement]
 * Pour [arbre] on a [adulte, jeunesse ...][livre, cd ...] => un nombre
 * on a �galement [arbre][_defaut] qui contient un nombre par d�dfaut 
 * 
 * @param mixed $parametres
 * @param [ID_notice] : ID de la notice biblio
 * @param [exemplaire] : exemplaire � tester (uniquement le 1er exemplaire) sous forme d'une ligne de DB
 * @param [plugin_get_resas] : plugin de recherche pour r�cup�rer les r�sas en cours li�es � cette notice biblio
 * @param [plugin_get_infos_quotas] : plugin pour r�cup�rer [criteres] et [arbre]
 * 
 * @return
 * @return [depassement] si vaut 1 : �chec 
 */
function plugin_transactions_resas_teste_quotas_reservataire ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
     $retour["resultat"]["depassement"]=0;
    
    $ID_notice=$parametres["ID_notice"]; // ID de la notice biblio
    $exemplaire=$parametres["exemplaire"];
    
    $plugin_get_resas=$parametres["plugin_get_resas"]; // retourne la liste des r�sas sous la forme de lignes de base de donn�es
    $plugin_get_infos_quotas=$parametres["plugin_get_infos_quotas"]; // retourne [arbres] et [criteres]
    
    // 1) On r�cup�re le nombre de r�sas en cours pour cette notice biblio
    $tmp=applique_plugin($plugin_get_resas, array("ID_notice"=>$ID_notice));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    
   
    // 2) On r�cup�re l'arbre et les crit�res
    $tmp=applique_plugin($plugin_get_infos_quotas, array());
    if ($tmp["succes"] != 1) {
        $retour["succes"]=0;
        $retour["erreur"]="les informations de quotas ne sont pas parametrees";
        return ($retour);
    }
    $infos_quotas=$tmp["resultat"];
    $arbre=$infos_quotas["arbre"]; // ATTENTIOn pas de "s" : ici un seul arbre qui ne d�pend pas de l'abonnement du lecteur
    $criteres=$infos_quotas["criteres"]; // les crit�res � rechercher dans l'arbre
    
    
    // 3) On r�cup�re les crit�res pertinents li�s � cet exemplaire
    $criteres_exe=array();
    foreach ($criteres as $critere_tmp) {
        $critere=$critere_tmp["emplacement"];
        array_push($criteres_exe, $exemplaire[$critere]);
    }
    
    // 4) On recherche le quota li� � ces crit�res. an a une valeur _defaut � la racine de l'arbre au cas o� on ne trouverait aps le param�tre
    $chemin=implode("/", $criteres_exe);
    $quota=get_parametres_by_chemin($arbre, $chemin);
    if ($quota === false) {
        $quota=$arbre["_defaut"];
    }
    
    
    // 5) On compare le nb r�sas et le quota
    if ($nb_resas >= $quota) {
         $retour["resultat"]["depassement"]=1;
         
    }

    
    return ($retour);
}

?>