<?php

/**
 * plugin_catalogue_import_export_traite_autorite()
 * 
 * Ce plugin va g�rer les champs li�s � autorit� quand on importe une autorit� (par exemple le champ 700 d'une notice bibliographique)
 * Il va cr�er la notice autorit� (ou la d�doublonner si elle existe d�j�) � partir des infos contenues dans le champ
 * puis il va mettre � jour le champ lui-m�me � partir de la notice autorit� cr��e
 * 
 * Il re�oit en param�tre une notice (tvs_marcxml) et le champ � traiter (sous forme de DomNode qui doit provenir du m�me DOM)
 * Diff�rents plugins vont g�rer les diff�rentes �tapes du processus.
 * 
 * 1) On g�n�re la notice autorit� � partir des infos contenues dans le champ (plugin_formate_champ). La notice est g�n�r�e sous forme de chaine de carat�re avec le
 *    format suivant : 200:a:toto|b:tutu|$$210:a:popo|b:pupu|$$
 * 
 * 2) A partir de cette chaine de carat�res on va cr�er la d�finition (Array) de la notice autorit�, puis on va g�n�rer cete notice en XML (plugin_crea_objet)
 * 
 * 3) On importe cette notice autorit� (plugin_importe_objet). C'est ce plugin qui sera charg� de d�doublonner la notice autorit�. Nous on r�cup�re la notice une fois import�e 
 *    (qui peut �tre celle qu'on a g�n�r� ou bien celle pr�sente dans la base ou une fusion des 2)
 * 
 * 4) Enfin on maj le champ de la notice elle-m�me. C'est un champ de type lien explicite. Donc on r�cup�re d'abord les infos sur le lien explicite (plugin_param_lien explicite)
 *    Entre autres choses, �a fournit le plugin qui va servir � formater la notice autorit� pour g�n�rer le champ de lien explicite (c'est en fait l'op�ration inverse qu'en 1))
 *    Puis on maj la notice (et le champ) : plugin_maj_lien_explicite
 * 
 * Le plugin ne retourne rien, car la notice est directement maj (par r�f�rence). ATTENTION le plugin n'enregistre pas la notice dans la DB. C'est � la charge
 * du plugin appelant
 *  
 * @param mixed $parametres
 * @param [type_obj] -> type d'objet de la notice autorit� li�e
 * @param [tvs_marcxml] -> notice en tvs_marcxml
 * @param [champ] -> champ de lien � traiter sous forme de DomNode (appartenant au m�me DOM que la notice)
 * @param [plugin_formate_champ] -> plugin qui va g�n�rer une notice autorit� (sous forme de string) � partir du champ de lien
 * @param [plugin_crea_objet] -> plugin qui va g�n�rer la notice autorit� en XML
 * @param [plugin_importe_objet] -> plugin qui va importer la notice autorit� dans la base (et le cas �ch�ant la d�doublonner)
 * @param [plugin_param_lien_explicite] -> Infos sur le lien explicite (du champ li� � l'autorit�)
 * @param [plugin_rajoute_ID_notice] => plugin de type modif_marcxml pour rajouter l'ID de la notice autorit� nouvellement cr�e en 000$a
 * !!!! deprecated !!!! => info r�cup�r�e via plugin_param_lien_explicite @param [plugin_formate_autorite] => plugin qui va g�n�rer le champ de lien (sous forme de string) � partir de la notice autorit� (op�ration inverse de plugin_formate_champ mais entre temps le champ devra �tre chang�, ne serait-ce que pour inclure les $3, $9a...)
 * @param [plugin_maj_lien_explicite] -> plugin qui va mettre � jour le champ de lien une fois la notice autorit� import�e
 * @param [nom_champ] -> nom du champ de lien
 * @param [import_options] => un tableau contenant divers options qui peuvent �tre saisies dans le formulaire d'import (ex. bib pour rec 995)
 *                           Ces options sont pass�es aux diff�rents plugins qui pourront les int�grer
 
 * 
 * @return void
 * 
 */
function plugin_catalogue_import_export_traite_autorite($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $type_obj=$parametres["type_obj"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $champ=$parametres["champ"];
    
    $plugin_formate_champ=$parametres["plugin_formate_champ"];
    $plugin_crea_objet=$parametres["plugin_crea_objet"];
    $plugin_importe_objet=$parametres["plugin_importe_objet"];
    $plugin_param_lien_explicite=$parametres["plugin_param_lien_explicite"];
    $plugin_maj_lien_explicite=$parametres["plugin_maj_lien_explicite"];
    //$plugin_formate_autorite=$parametres["plugin_formate_autorite"];
    $plugin_rajoute_ID_notice=$parametres["plugin_rajoute_ID_notice"];
    $nom_champ=$parametres["nom_champ"];
    $import_options=$parametres["import_options"];
    
    // 1) On r�cup�re la notice autorit� sous forme de string de type
    // 200:a:toto|b:tutu$$210:c:popo|d:pupu|$$
    $tmp=applique_plugin ($plugin_formate_champ, array("champ"=>$champ, "tvs_marcxml" => $tvs_marcxml, "import_options"=>$import_options));
    if ($tmp["succes"]!=1) {
        return ($tmp);
    }
    $def_str=$tmp["resultat"]["texte"];
    
    // 2) On cr�e la d�finition (array) de la notice autorit�
    $def_array=array();
    $def_array["definition"]=array();
    $tmp_liste_champs=explode("$$", $def_str);
    foreach ($tmp_liste_champs as $tmp_champ) { // pour chaque champ
        $tmp_elem_champ=explode(":", $tmp_champ, 2);
        if (count($tmp_elem_champ)==2) {
            $def_champ_array=array();
            $tag=$tmp_elem_champ[0];
            $def_champ_array["tag"]=$tag;
            $def_champ_array["definition"]=array();
            $ss_champs_str=$tmp_elem_champ[1];
            $tmp_liste_ss_champs=explode("|", $ss_champs_str);
            foreach ($tmp_liste_ss_champs as $tmp_ss_champ) { // pour chaque ss_champ
                $tmp_elem_ss_champ=explode(":", $tmp_ss_champ, 2);
                if (count($tmp_elem_ss_champ)==2) {
                     $code=$tmp_elem_ss_champ[0];
                     $valeur=$tmp_elem_ss_champ[1];
                     array_push($def_champ_array["definition"], array("code"=>$code, "valeur"=>$valeur));
                }
            } // fin de pour chaque ss_champ...
            array_push ($def_array["definition"], $def_champ_array);
        }
    } // fin de pour chaque champ
    
    // 3) On g�n�re la notice autorit� XML
    $tmp=applique_plugin($plugin_crea_objet, $def_array);
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    $notice_autorite=$tmp["resultat"]["notice"];
    
    // 4) On importe cette notice autorit�
    $tmp=applique_plugin($plugin_importe_objet, array("notice"=>$notice_autorite, "import_options"=>$import_options));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    if ($tmp["resultat"]["bool_erreur"]==1) {
        return($retour);
    }
    $tvs_marcxml_autorite=$tmp["resultat"]["tvs_marcxml"];
    $ID_notice_autorite=$tmp["resultat"]["ID_notice"]; 
    
    // 5) on r�cup�re les infos sur le lien explicite
    $tmp=applique_plugin($plugin_param_lien_explicite, array());
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    $infos_lien_explicite=$tmp["resultat"];
    $ss_champs_a_conserver=$infos_lien_explicite["ss_champs_a_conserver"];
    $plugin_formate_autorite=$infos_lien_explicite["plugin_formate"];
    
    // 5bis) On rajoute ID_notice dans le champ 000$a
    $tmp=applique_plugin($plugin_rajoute_ID_notice, array("tvs_marcxml"=>$tvs_marcxml_autorite, "ID_notice"=>$ID_notice_autorite));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    
    // 6) on g�n�re la chaine de carat�res qui va servir � maj le lien explicite
    $tmp=applique_plugin($plugin_formate_autorite, array("tvs_marcxml"=>$tvs_marcxml_autorite));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    }
    $str_lien_explicite=$tmp["resultat"]["texte"];
    
    // 7) on maj la notice de base (lien explicite)
    $tmp=applique_plugin($plugin_maj_lien_explicite, array("tvs_marcxml"=>$tvs_marcxml, "champ"=>$champ, "champ_remplace"=>$str_lien_explicite, "ss_champs_a_conserver"=>$ss_champs_a_conserver, "nom_champ"=>$nom_champ));
    if ($tmp["succes"]!=1) {
       return ($tmp);
    } 
 
    
    return ($retour);
}


?>