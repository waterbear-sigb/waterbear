<?php
/**
 * plugin_catalogue_import_export_meta_format_controlfield_2_datafield()
 * 
 * Ce plugin permet de convertir un controlfield, c'est � dire un champ ne poss�dant pas de ss-champs en datafield classique avec des ss_champs
 * G�n�ralement un controlfield est compos� d'une cha�ne de caract�res dans laquelle les informations sont r�parties en fonction de leur position dans la chaine
 * A partir du param�tre [champs] qui contient le d�tail de la structure du controlfield, le plugin va d�couper la chaine et g�n�rer des sous champs
 * 
 * Si jamais le champ n'�tait pas param�tr�, on cr�e un seul ss-champ $a avec la totalit� du texte
 * 
 * Le retour se fait sous forme d'une array [ss_champs]. A charge pour la fonction appelant de g�n�rer une notice XML � partir de cette array
 *  
 * @param mixed $parametres
 * @param [nom_champ] => nom du champ � formater (label, 100...)
 * @param [texte] => texte du champ � formater
 * @param [champs] => d�fintion de la mani�re dont il faut formater le champ � savoir :
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
    
    // 1) si ce champ n'est pas param�tr� dans [champs] on retourne tout le texte dans un $a
    if (! isset ($champs[$nom_champ])) {
        array_push($retour["resultat"]["ss_champs"], array("code"=>"a", "valeur"=>$texte));
        $retour["resultat"]["bool_code"]=0;
        return ($retour);
    }
    
    // 2) sinon on g�n�re les ss-champs
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