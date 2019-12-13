<?php
/**
 * plugin_catalogue_import_export_meta_format_controlfield_2_datafield()
 * 
 * Ce plugin permet de convertir un controlfield, c'est à dire un champ ne possédant pas de ss-champs en datafield classique avec des ss_champs
 * Généralement un controlfield est composé d'une chaîne de caractères dans laquelle les informations sont réparties en fonction de leur position dans la chaine
 * A partir du paramètre [champs] qui contient le détail de la structure du controlfield, le plugin va découper la chaine et générer des sous champs
 * 
 * Si jamais le champ n'était pas paramétré, on crée un seul ss-champ $a avec la totalité du texte
 * 
 * Le retour se fait sous forme d'une array [ss_champs]. A charge pour la fonction appelant de générer une notice XML à partir de cette array
 *  
 * @param mixed $parametres
 * @param [nom_champ] => nom du champ à formater (label, 100...)
 * @param [texte] => texte du champ à formater
 * @param [champs] => défintion de la manière dont il faut formater le champ à savoir :
 * @param [champs][label, 100, ...][ss_champs][0,1,2...][code | debut | longueur]
 *  
 * @return [ss_champs][0,1,2...][code | valeur]
 * 
 */
function plugin_catalogue_import_export_meta_format_controlfield_2_datafield ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    $retour["resultat"]["ss_champs"]=array();
    $retour["resultat"]["bool_code"]=1;
    
    $champs=$parametres["champs"];
    $nom_champ=$parametres["nom_champ"];
    $texte=$parametres["texte"];
    
    // 1) si ce champ n'est pas paramétré dans [champs] on retourne tout le texte dans un $a
    if (! isset ($champs[$nom_champ])) {
        array_push($retour["resultat"]["ss_champs"], array("code"=>"a", "valeur"=>$texte));
        $retour["resultat"]["bool_code"]=0;
        return ($retour);
    }
    
    // 2) sinon on génère les ss-champs
    foreach ($champs[$nom_champ]["ss_champs"] as $infos_ss_champ) {
        $code=$infos_ss_champ["code"];
        $debut=$infos_ss_champ["debut"];
        $longueur=$infos_ss_champ["longueur"];
        $str_ss_champ=substr($texte, $debut, $longueur);
        array_push($retour["resultat"]["ss_champs"], array("code"=>$code, "valeur"=>$str_ss_champ));
    }
    
    
    
    return ($retour);
}


?>