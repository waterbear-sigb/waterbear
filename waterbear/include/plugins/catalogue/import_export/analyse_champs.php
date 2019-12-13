<?php

/**
 * plugin_catalogue_import_export_analyse_champs()
 * 
 * Ce plugin permet de modifier une notice XML en appliquant des plugins sur les champs et les ss-champs les composant.
 * Par exemple, pour les champs de lien, on appliquera un plugin permettant de créer les autorités correspondantes et de maj le champ de lien
 * 
 * Ce plugin ne retourne rien, car la notice est modifiée par référence
 * 
 * @param mixed $parametres
 * @param [notice] OU [tvs_marcxml] => la notice en DomXml ou en tvs_marcxml
 * @param [champs] => définition des plugins à appliquer sur les champs / ss-champs
 * @param        [200, 210, 700...][plugins][0,1,2...] => liste des plugins à appliquer sur chaque champ (on peut appliquer plusieurs plugins sur un même champ)
 * @param                          [ss_champs][a,c,j...][0,1,2...] => liste des plugins à appliquer sur chaque ss-champ (on peut appliquer plusieurs plugins sur un même ss-champ)
 * @param [import_options] => un tableau contenant divers options qui peuvent être saisies dans le formulaire d'import (ex. bib pour rec 995)
 *                           Ces options sont passées aux différents plugins qui pourront les intégrer
 * @return void
 */
function plugin_catalogue_import_export_analyse_champs($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $def_champs=$parametres["champs"];
    $notice=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $import_options=$parametres["import_options"];
    
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    foreach ($def_champs as $nom_champ => $infos_champ) { // pour chaque tag
        $liste_champs=$tvs_marcxml->get_champs($nom_champ, "");
        foreach ($liste_champs as $champ) { // pour chaque champ ayant ce tag
            
            if (is_array($infos_champ["ss_champs"])) { // si on veut appliquer des plugins au niveau des ss_champs
                foreach ($infos_champ["ss_champs"] as $nom_ss_champ => $infos_ss_champ) { // pour chaque code
                    $liste_ss_champs=$tvs_marcxml->get_ss_champs($champ, $nom_ss_champ, "", "");
                    foreach ($liste_ss_champs as $ss_champ) { // pour chaque ss-champ ayant ce code
                        foreach ($infos_ss_champ as $plugin_ss_champ) {
                            $tmp=applique_plugin ($plugin_ss_champ, array("champ"=>$champ, "tvs_marcxml"=>$tvs_marcxml, "notice"=>$tvs_marcxml->notice, "ss_champ"=>$ss_champ, "nom_champ"=>$nom_champ, "nom_ss_champ"=>$nom_ss_champ, "import_options"=>$import_options));
                            if ($tmp["succes"] != 1) {
                                return ($tmp);
                            }
                        }
                    } // fin du pour chaque ss-champ ayant ce code
                } // fin du pour chaque code
            }
            
            if (is_array($infos_champ["plugins"])) { // si on veut appliquer des plugins au niveau du champ
                foreach ($infos_champ["plugins"] as $plugin_champ) { // pour chaque plugin de modif de champ
                    $tmp=applique_plugin ($plugin_champ, array("champ"=>$champ, "tvs_marcxml"=>$tvs_marcxml, "notice"=>$tvs_marcxml->notice, "nom_champ"=>$nom_champ, "import_options"=>$import_options));
                    if ($tmp["succes"] != 1) {
                        return ($tmp);
                    }
                } // fin du pour chaque plugin de modif de champ
            }
            
        } // fin du pour chaque champ ayant ce tag
    } // fin du pour chaque tag
    
    
    
    
    return ($retour);
}


?>