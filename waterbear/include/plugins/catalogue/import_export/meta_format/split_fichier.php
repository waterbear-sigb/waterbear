<?PHP

/**
 * catalogue_import_export_meta_format_split_fichier()
 * 
 * @param mixed $parametres
 * @param ["meta_format"] => méta-format 
 * @param ["taille_fichier"] => taille du fichier (si fichier)
 * @param ["handle"] => handle du fichier (si fichier)
 * @param ["chaine"] => chaine de caractères (si chaine)
 * @param ["taille_chaine"] => longueur de la chaine (si chaine)
 * @param ["lien_format_plugin"] (ARRAY) => tableau associatif méta-format / PA pour le traier (paramètre stoché dans le registre)
 * @return array
 * @return ["resultat"] => (INT) nombre de notices
 * 
 * Ce plugin retourne le nombre de notices contenues dans un fichier en fonction d'un méta-format donné (MARC, TXT, XML...)
 * Utilise des plugins spécifiques à chaque format pour ce faire. L'association format => plugin est paramétrée dans le registre (paramètres du plugin)
 * Peut utiliser soit un fichier soit une chaine de caractères en entrée
 */
function plugin_catalogue_import_export_meta_format_split_fichier ($parametres) {
    extract ($parametres);
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=0;
    if (! isset($lien_format_plugin[$meta_format])) {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "parametres_incorrects", array("fonction"=>"plugin_catalogue_import_export_meta_format_split_fichier", "message"=>"meta-format $meta_format non parametre"));
        return ($retour);
    }
    
    $PA=$lien_format_plugin[$meta_format]; // on récupère le PA
    $last_car=0;
    $compteur=0;
    do {
        $tmp=applique_plugin($PA, array("handle"=>$handle, "taille_fichier"=>$taille_fichier, "last_car"=>$last_car));
        if ($tmp["succes"]==0) {
            return ($tmp);
        }
        $notice=$tmp["resultat"]["notice"];
        $last_car=$tmp["resultat"]["last_car"];
        if (strlen($notice)>0) {
            $compteur++;
        }
    } while ($last_car != 0);
    
    $retour["resultat"]=$compteur;
    return ($retour);
}

?>