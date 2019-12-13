<?php


/**
 * plugin_catalogue_import_export_meta_format_get_notice_suivante_separateur()
 * 
 * @param mixed $parametres
 * @param ["separateur"] => s�parateur
 * @param ["hex_separateur"] => s�parateur fourni sous forme hexad�cimale (pb des caract�res sp�ciaux dans le registre)
 * @param ["taille_fichier"] => taille du fichier (si fichier)
 * @param ["handle"] => handle du fichier (si fichier)
 * @param ["chaine"] => chaine de caract�res (si chaine)
 * @param ["taille_chaine"] => longueur de la chaine (si chaine)
 * @param ["last_car"] => dernier caract�re
 * 
 * @return ["resultat"]["notice"]=>notice suivante ou chaine vide si rien trouv�
 * @return ["resultat"]["last_car"]=> dernier caract�re ou 0 si fin de fichier
 * 
 * Retourne la notice suivante en se basant sur un s�parateur
 * Peut �tre soit � partir d'un fichier, soit � partir d'une chaine de caract�res
 * Prend �galement en param�tre la position du dernier caract�re de la derni�re notice
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
    
    // Si dernier caract�re n'est pas d�fini on le positionne � 0
    if (! isset($last_car)) {
        $last_car=0;
    }
    
    // On d�termine le s�parateur (�ventuellement � partir de la valeur hexad�cimale)
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
        
        // On avance jusqu'� ce qu'on rencontre le s�parateur ou la fin du fichier
        while (!feof($handle)) {
            $car = fread($handle, 1);
            $last_car++;
            $buffer.=$car;
            $longueur_buffer++;
            if (substr($buffer, $longueur_buffer - $longueur_separateur, $longueur_separateur) == $separateur) {
                $retour["resultat"]["notice"]=$buffer; // $separateur rencontr�
                $retour["resultat"]["last_car"]=$last_car;
                return($retour);
            }
        }
        $retour["resultat"]["notice"]=$buffer; // FOF rencontr�
        $retour["resultat"]["last_car"]=0;
        return($retour);
    } elseif (isset ($chaine)) {// SI CHAINE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        while ($car = substr ($chaine, $last_car, 1)) {
            $buffer.=$car;
            $longueur_buffer++;
            $last_car++;
            if (substr($buffer, $longueur_buffer - $longueur_separateur, $longueur_separateur) == $separateur) {
                $retour["resultat"]["notice"]=$buffer; // $separateur rencontr�
                $retour["resultat"]["last_car"]=$last_car;
                return($retour);
            }
        }
        $retour["resultat"]["notice"]=$buffer; // FOF rencontr�
        $retour["resultat"]["last_car"]=0;
        return($retour);
    } else {
        $retour["succes"]=0;
        $retour["erreur"]=get_intitule("erreurs/messages_erreur", "parametres_incorrects", array("fonction"=>"plugin_catalogue_import_export_meta_format_get_notice_suivante_separateur", "message"=>'ni $handle ni $chaine ne sont fournis'));
        return($retour);
    }
    
    
}



?>