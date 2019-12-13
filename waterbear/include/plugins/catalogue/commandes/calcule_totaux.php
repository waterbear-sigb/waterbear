<?php
/**
 * plugin_catalogue_commandes_calcule_totaux()
 * 
 * Ce plugin permet de calculer des totaux pour une commande : somme command�e, engag�e, factur�e, nb docs command�s, re�us
 * reste � recevoir, tva, prix ht...
 * 
 * Le calcul peut soit s'effectuer pour la totalit� d'une commande, auquel cas, on passe [ID_commande] en param�tre
 * et on utilise [plugin_recherche] dynamique (avec param_recherche d�fini dans le registre). On ins�re ID_commande
 * dans param_recherche via un alias ou une variable incluse
 * 
 * Mais on peut aussi rechercher un total pour une s�lection de notices. Dans ce cas, on fournit [param_recherche]
 * en param�tre via le script, et [plugin_recherche] est statique.
 * 
 * Le plugin effectue plusieurs recherches (1 par total � calculer) mais c'est toujours la m�me recherche. Tout ce qui change, c'est
 * la colonne dont on veut faire la somme
 * 
 * Le r�sultat est format� par un plugin de type div/formate_variables � qui on fournit en param�tre [variables] et
 * qui retourne [texte] : les variables format�es
 * 
 * Le plugin retourne [texte] qui est le r�sultat format� et [variables] qui est une array exploitable
 * 
 * @param mixed $parametres
 * @param SOIT [param_recherche]
 * @param SOIT [ID_commande]
 * @param plugin_recherche (statique si [param_recherche], dynamique si [ID_commande])
 * @param [bool_pagination] => si vaut 0 (d�faut) on ne tient pas compte de la pagination => on calcule le total pour toutes les pages
 * @param [plugin_formate] => plugin qui va formater le r�sulter (g�n�ralement div/formate_variables) on envoie [variables] en param�tres
 * 
 * @return [texte] => le r�sultat format� pour affichage direct
 * @return [variables] => un tableau pour exploitation
 */
function plugin_catalogue_commandes_calcule_totaux ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $param_recherche=$parametres["param_recherche"]; // soit...
    $ID_commande=$parametres["ID_commande"]; // soit ...
    $bool_pagination=$parametres["bool_pagination"];
    $plugin_recherche=$parametres["plugin_recherche"]; 
    $plugin_formate=$parametres["plugin_formate"];
    
    if ($param_recherche == "") {
        $param_recherche = $plugin_recherche["parametres"]["param_recherche"];
    }

    // 1) si on veut d�sactiver la pagination (total de la commande) => d�faut
    if ($bool_pagination != 1) {
        unset($param_recherche["tris"]);
        unset($param_recherche["page"]);
    }
    
    $liste_sommes=array("prix_remise"=>"a_prix_remise_total", "tva"=>"a_tva_totale", "prix_ht"=>"a_prix_ht_total", "nb_exe"=>"a_nb_exe", "montant_engage"=>"a_montant_engage", "montant_facture"=>"a_montant_facture", "nb_a_recevoir"=>"a_nb_a_recevoir", "nb_recu"=>"a_nb_recu");
    $variables=array();
    foreach ($liste_sommes as $clef => $colonne) {
        $tmp=applique_plugin ($plugin_recherche, array("param_recherche"=>$param_recherche, "somme"=>$colonne, "somme_seulement"=>"1", "ID_commande"=>$ID_commande));
        if ($tmp["succes"] != 1) {
            return ($tmp);
        }
        $variables[$clef]=round($tmp["resultat"]["somme"],2);
    }
    
    $tmp=applique_plugin ($plugin_formate, array("variables"=>$variables));
    if ($tmp["succes"] != 1) {
       return ($tmp);
    }
    $retour["resultat"]["texte"]=$tmp["resultat"]["texte"];
    
    
    
    //$retour["resultat"]["texte"]="<br> Nombre d'exemplaires : ".$variables["nb_exe"]." <br> Prix total : ".$variables["prix_remise"]." <br> TVA : ".$variables["tva"]." <br> Prix HT : ".$variables["prix_ht"]." <br> Montant engage : ".$variables["montant_engage"]." <br> Montant facture : ".$variables["montant_facture"]." <br> Reste a recevoir : ".$variables["nb_a_recevoir"]." <br> Quantite recu : ".$variables["nb_recu"]." <br>";
    $retour["resultat"]["variables"]=$variables;
    
    
    
    
    return ($retour);
}



?>