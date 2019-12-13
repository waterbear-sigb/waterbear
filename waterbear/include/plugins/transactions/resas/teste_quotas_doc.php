<?php

/**
 * plugin_transactions_resas_teste_quotas_doc()
 * 
 * Ce plugin teste si la r�servation d'un exemplaire donn� est possible eu �gard aux quotas de r�servation du lecteur 
 * (nombre de r�sas effectu�es par le lecteur sur les diff�rents types de docs)
 * Ce test se base sur les infos de l'exemplaire (section, type doc...). Contrairement � la gestion des quotas de pr�t, on ne peut pas acc�der aux infos de la 
 * notice biblio, du lecteur... 
 * Par ailleurs, les infos de l'exemplaire sont dans un premier temps r�cup�r�s dans les notices de resa (pour g�n�rer l'arbre), puis dans la notice exe
 * (pour tester si un exemplaire donn� peut �tre r�serv�). Comme ces informations (identiques) peuvent correspondre � des acc�s diff�rents entre la notice 
 * de r�sa et la notice exe, on aura 2 param�tres pour les crit�res : [criteres_resa] et [criteres_exe]
 * 
 * Ce test n'est � effectuer que pour le premier exemplaire d'une notice bibliographique (on assume que les exemplaires suivants auront les m�mes crit�res : type doc, section...)
 * 
 * Le plugin commence par r�cup�rer les diff�rents quotas dans le registre, ainsi que les crit�res (par ex. type doc et section)
 * On r�cup�re ensuite les infos sur les abonnements du lecteur : si ID_famille != "" on r�cup�re les quotas du chef de famill, sinon ceux de l'individu. A partir de l�, on voit quels sont les quotas actifs du lecteur et on les fusionne
 * ce qui donne un arbre vierge
 * On r�cup�re ensuite la liste des r�sas en cours du lecteur, ce qui va permettre de g�n�rer l'arbre � jour du lecteur
 * Pour finir, on teste la r�sa de l'exemplaire � tester. �a retourne un arbre � jour (si OK), une dur�e de r�sa et �ventuellement des erreurs 
 * 
 * @param [$exemplaire] // infos d'exemplaire sous la forme d'une ligne de la base de donn�es
 * @param [$ID_lecteur]
 * @param [$ID_famille]
 * @param [$validation_message] // si "oui", forcer la r�sa
 * @param [$plugin_get_infos_quotas=] // retourne [arbres] et [criteres]
 * @param [$plugin_get_abos] // retourne la liste des abonnements sous la forme de lignes de base de donn�es
 * @param [$plugin_traite_abonnements]
 * @param [$plugin_get_resas] // retourne la liste des r�sas sous la forme de lignes de base de donn�es
 * @param [$plugin_resa_2_quota]
 * 
 * 
 * @return [arbre] => l'arbre maj
 * @return [duree] => dur�e de la r�sa
 * @return [depassement] => message d'erreur en cas de d�passement (indiquant le quota qui a bloqu�...)
 */
function plugin_transactions_resas_teste_quotas_doc ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    
    $exemplaire=$parametres["exemplaire"]; // infos d'exemplaire sous la forme d'une ligne de la base de donn�es
    $ID_lecteur=$parametres["ID_lecteur"];
    $ID_famille=$parametres["ID_famille"];
    $validation_message=$parametres["validation_message"];
    $plugin_get_infos_quotas=$parametres["plugin_get_infos_quotas"]; // retourne [arbres] et [criteres]
    $plugin_get_abos=$parametres["plugin_get_abos"]; // retourne la liste des abonnements sous la forme de lignes de base de donn�es
    $plugin_traite_abonnements=$parametres["plugin_traite_abonnements"];
    $plugin_get_resas=$parametres["plugin_get_resas"]; // retourne la liste des r�sas sous la forme de lignes de base de donn�es
    $plugin_resa_2_quota=$parametres["plugin_resa_2_quota"];
    
    // 1) On r�cup�re les infos li�es aux quotas et aux crit�res
    $tmp=applique_plugin($plugin_get_infos_quotas, array());
    if ($tmp["succes"] != 1) {
        $retour["succes"]=0;
        $retour["erreur"]="les informations de quotas ne sont pas parametrees";
        return ($retour);
    }
    $infos_quotas=$tmp["resultat"];
    //$arbres=$infos_quotas["arbres"];
    $criteres_resa=$infos_quotas["criteres_resa"]; // les crit�res � utiliser dans la notice de r�sa (pour g�n�rer l'arbre)')
    $criteres_exe=$infos_quotas["criteres_exe"]; // les crit�res � utiliser dans la notice d'exemplaire une fois que l'arbre est g�n�r� pour tester si l'exe est r�servable
    
    // 2) On r�cup�re les abonnements (et codes quotas) en cours
    if ($ID_famille == "" OR $ID_famille == "0") {
        $ID_famille=$ID_lecteur;
    }
    $tmp=applique_plugin($plugin_get_abos, array("ID_lecteur"=>$ID_famille));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nb_abos=$tmp["resultat"]["nb_notices"];
    $abos=$tmp["resultat"]["notices"];
    
    // 3) On g�n�re un arbre des quotas vierge
    $tmp=applique_plugin($plugin_traite_abonnements, array("infos_abos"=>$abos, "infos_quotas"=>$infos_quotas));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $messages=$tmp["resultat"]["messages"];
    $arbre=$tmp["resultat"]["arbre"];
    
    // 4) On r�cup�re la liste des r�servations de ce lecteur
    $tmp=applique_plugin($plugin_get_resas, array("ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    
    // 5) On r�cup�re l'arbre d�finitif en faisant une maj pour chaque r�sa
    foreach ($resas as $resa) {
        $tmp=applique_plugin($plugin_resa_2_quota, array("arbre"=>$arbre, "criteres"=>$criteres_resa, "validation_message"=>"oui", "bureau"=>$resa));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $arbre=$tmp["resultat"]["arbre"]; // arbre � jour
    }
    
    // 6) On teste pour voir si la r�sa de l'exemplaire est possible
    $tmp=applique_plugin($plugin_resa_2_quota, array("arbre"=>$arbre, "criteres"=>$criteres_exe, "validation_message"=>$validation_message, "bureau"=>$exemplaire));
    return($tmp);
}



?>