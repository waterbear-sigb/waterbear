<?php
/**
 * plugin_catalogue_marcxml_formate_champ_vedette_matiere()
 * 
 * Ce plugin permet de générer une notice de vedette matière (sous forme de string) à partir d'un champ de lien d'une notice biblio (par exemple un champ 606)
 * Il va générer une chaine de la forme 250:3:123|a:Toto|x:tutu|$$400:3:Popo|a:Pupu|$$
 * 
 * Ce type de formatage est normalement assuré par un plugin de type get_datafields ou formate_plugins
 * Mais pour générer les vedettes matières, c'est trop complexe, car on a les $3 qui marquent la limite entre les autorités matières
 * et ensuite le 1er ss-champ qui suit le $3 qui va déterminer le nom du champ (subdivision chrono, géo...)
 * 
 * Dans tous les cas, la première autorité correspond à une tête de vedette dont le type est déterminé par [nom_champ_defaut] ==> NON
 * le 1er champ doit normalement correspondre à un $a
 * Ensuite, pour chaque autorité, on fera correspondre un nom de ss-champ à un nom de champ grâce au tableau [ss_champ_2_champs]
 * qui a la forme [x=>400, y=>410, z=>420]
 * 
 * @param mixed $parametres
 * @param [tvs_marcxml] => la notice
 * @param [champ] => le champ à analyser
 * @param [ss_champ_2_champ] => tableau associant le premier ss-champ après un $3 à un nom de champ dans la notice autorité
 * @param [nom_champ_defaut] => nom du champ à attribuer à la tête de vedette (ou a l'ensemble s'il n'y a pas de $3)
 * 
 * @return [texte]
 */
 
 
function plugin_catalogue_marcxml_formate_champ_vedette_matiere ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $champ=$parametres["champ"];
    $ss_champ_2_champ=$parametres["ss_champ_2_champ"];
    $nom_champ_defaut=$parametres["nom_champ_defaut"];
    
    $nom_ss_champ_no="3"; // p-ê à passer via le registre

    
    //$champs_retour=array();
    $last_no_champ="";
    $champ_courant="";
    $tous_les_champs="000:a:XXXXX|$$"; // on rajoute un champ 000$a (n° de notice) bidon
    $bool_ss_champs=0; // vaut 1 s'il y a au moins 1 $3
    
    
    $liste_ss_champs=$tvs_marcxml->get_ss_champs($champ, "", "", "");
    foreach ($liste_ss_champs as $ss_champ) { // pour chaque ss-champ
        $code=$tvs_marcxml->get_nom_ss_champ($ss_champ);
        $valeur=$tvs_marcxml->get_valeur_ss_champ($ss_champ);
        if ($code==$nom_ss_champ_no) { // si $3
            $bool_ss_champs=1;
            if ($champ_courant != "") {
                $tous_les_champs.=$champ_courant."$$";
                $champ_courant="";
            }
            $last_no_champ=$valeur;
        } elseif ($last_no_champ != "") { // si c'est le 1er ss-champ après le $3
            $nom_champ=$ss_champ_2_champ[$code];
            if ($nom_champ == "") {
                $nom_champ="XXX";
            }
            $champ_courant.=$nom_champ.":3:".$last_no_champ."|a:".$valeur."|";
            $last_no_champ="";
        } else { // pour les autres sous-champs
            $champ_courant.=$code.":".$valeur."|";
        }
    } // fin du pour chaque ss-champ
    
    if ($champ_courant != "") {
        $tous_les_champs.=$champ_courant."$$";
    }
    
    if ($bool_ss_champs == 0) {
        $tous_les_champs=$nom_champ_defaut.":".$tous_les_champs."$$";
    }
    
    $retour["resultat"]["texte"]=$tous_les_champs;
    
    return ($retour);
}


?>