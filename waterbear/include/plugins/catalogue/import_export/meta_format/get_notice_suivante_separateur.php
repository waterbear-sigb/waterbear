<?php


/**
 * plugin_catalogue_import_export_meta_format_get_notice_suivante_separateur()
 * 
 * @param mixed $parametres
 * @param ["separateur"] => séparateur
 * @param ["hex_separateur"] => séparateur fourni sous forme hexadécimale (pb des caractères spéciaux dans le registre)
 * @param ["taille_fichier"] => taille du fichier (si fichier)
 * @param ["handle"] => handle du fichier (si fichier)
 * @param ["chaine"] => chaine de caractères (si chaine)
 * @param ["taille_chaine"] => longueur de la chaine (si chaine)
 * @param ["last_car"] => dernier caractère
 * 
 * @return ["resultat"]["notice"]=>notice suivante ou chaine vide si rien trouvé
 * @return ["resultat"]["last_car"]=> dernier caractère ou 0 si fin de fichier
 * 
 * Retourne la notice suivante en se basant sur un séparateur
 * Peut être soit à partir d'un fichier, soit à partir d'une chaine de caractères
 * Prend également en paramètre la position du dernier caractère de la dernière notice
 */
function plugin_catalogue_import_export_meta_format_get_notice_suivante_separateur ($parametres) {
    extract ($parametres);
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    $retour["resultat"]["last_car"]=0;
    $retour["resultat"]["notice"]="";
    $buffer="";
    $longueur_buffer=0;
    $longueur_separateur=0;
    
    // Si dernier caractère n'est pas défini on le positionne à 0
    if (! isset($last_car)) {
        $last_car=0;
    }
    
    // On détermine le séparateur (éventuellement à partir de la valeur hexadécimale)
    if ($hex_separateur != "") {
        $separateur="";
        $tmp=explode("|",$hex_separateur);
        foreach ($tmp as $car) {
            $separateur.=chr($car);
        }
    }
    $longueur_separateur=strlen($separateur);
    
    if (isset($handle)) {// SI FICHIER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // On se positionne au bon emplacement
        fseek ($handle, $last_car);
        
        // On avance jusqu'à ce qu'on rencontre le séparateur ou la fin du fichier
        while (!feof($handle)) {
            $car = fread($handle, 1);
            $last_car++;
            $buffer.=$car;
            $longueur_buffer++;
            if (substr($buffer, $longueur_buffer - $longueur_separateur, $longueur_separateur) == $separateur) {
                $retour["resultat"]["notice"]=$buffer; // $separateur rencontré
                $retour["resultat"]["last_car"]=$last_car;
                return($retour);
            }
        }
        $retour["resultat"]["notice"]=$buffer; // FOF rencontré
        $retour["resultat"]["last_car"]=0;
        return($retour);
    } elseif (isset ($chaine)) {// SI CHAINE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        while ($car = substr ($chaine, $last_car, 1)) {
            $buffer.=$car;
            $longueur_buffer++;
            $last_car++;
            if (substr($buffer, $longueur_buffer - $longueur_separateur, $longueur_separateur) == $separateur) {
                $retour["resultat"]["notice"]=$buffer; // $separateur rencontré
                $retour["resultat"]["last_car"]=$last_car;
                return($retour);
            }
        }
        $retour["resultat"]["notice"]=$buffer; // FOF rencontré
        $retour["resultat"]["last_car"]=0;
        return($retour);
    } else {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "parametres_incorrects", array("fonction"=>"plugin_catalogue_import_export_meta_format_get_notice_suivante_separateur", "message"=>'ni $handle ni $chaine ne sont fournis'));
        return($retour);
    }
    
    
}



?>