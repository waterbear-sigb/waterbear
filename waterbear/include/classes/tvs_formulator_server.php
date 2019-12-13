<?php
/**
 * tvs_formulator_server
 * 
 * @package WaterBear
 * Cette classe gère des formulator sur 3 niveaux : onglet/champ/sous-champ. 
 * Les sous-champs pouvant contenir plusieurs éléments 
 * 
 * *** $onglets ***
 * la variable $onglets contient toute la définition du formulaire de manière hierérchique (avec au total tous les paramètres de champs et ss-champs définis dans le registre)
 * $onglets [0,1,2...][id|intitule]
 *                    [champs][0,1,2...][id|nom|intitule + autres infos registre (autoplugin, icones...)] 
 *                                      [ss_champs][0,1,2...][id|nom|intitule|valeur + autres infos registre (type, autoplugin, icones, evenements...)]
 * 
 * *** $elements ***
 * la variable $elements recense tous les éléments du formulaires (onglets, champs sous-champs). Elle permet de retouver facilement la position d'un élément dans l'arborescence ($onglets) à partir de son id
 * $elements[0,1,2...]
 *                    [type] => onglet | champ | ss_champ
 *                    [parent] => Id de l'onglet pour un champ ou du champ pour un ss_champ ("" pour un onglet)
 *                    [idx] => position de l'élément dans la liste des enfants de son parent (dans le tableau $onglets) 
 * 
 * Par exemple je cherche id = 123
 * Dans $elements je trouve (type => ss_champ, parent => 120, idx=>3)
 * je cherche donc le champ parent qui a id=120 je trouve (type => champ, parent => 100, idx => 4)
 * Enfin je cherche l'onglet qui a id=100 et je trouve (type => onglet, parent => "", idx => 2)
 * 
 * je sais que mon sous-champ est le 3e d'un champ qui est le 4e du 2e onglet.
 * J'obtiendrai donc toutes les infos sur ce ss-champ dans $onglets[2][champs][4][ss_champs][3]
 * 
 * 
 * 
 * **/
 
class tvs_formulator_server {
    var $onglets;
    var $elements;
    var $last_id;
    //var $ID_operation;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    function __construct () {
        //$this->ID_operation=$parametres["ID_operation"];
        $this->onglets=array();
        $this->elements=array();
        $this->last_id=0;
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function init_formulator ($parametres) {
//tvs_log ("dbg", "DBG!!", var_export($parametres, true));
        $this->genere_onglets($parametres["onglets"]);
        $this->last_id=$parametres["last_ID"];
        $this->genere_elements ($this->onglets);
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction remplace les idx définis dans le registre du type "02 - 200" en idx numérique : 2
    function genere_onglets ($onglets) {
        $onglets2=array();
        $idx_onglet=0;
        foreach ($onglets as $onglet) {
            $onglet2=$onglet;
            $onglet2["champs"]=array(); // on réinitialise les champs
            $idx_champ=0;
            if (is_array($onglet["champs"])) {
                foreach ($onglet["champs"] as $champ) {
                    $champ2=$champ;
                    $champ2["ss_champs"]=array(); // on réinitialise les ss-champs
                    $idx_ss_champ=0;
                    foreach ($champ["ss_champs"] as $ss_champ) {
                        // S'il y a une valeur par défaut (select) on la met en valeur (si celle-ci non définie)
                        if ($ss_champ["valeur"]=="" AND $ss_champ["valeur_defaut"] != "") {
                            $ss_champ["valeur"]=$ss_champ["valeur_defaut"];
                        }
                        $champ2["ss_champs"][$idx_ss_champ] = $ss_champ;
                        $idx_ss_champ++;
                    }
                    $onglet2["champs"][$idx_champ] = $champ2;
                    $idx_champ++;
                }
            }
            $onglets2[$idx_onglet]=$onglet2;
            $idx_onglet++;
        }
        $this->onglets=$onglets2;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function genere_elements ($onglets) {
        foreach ($onglets as $idx_onglet => $onglet) {
            $tmp=array();
            $tmp["type_element"]="onglet";
            $tmp["parent"]="";
            $tmp["idx"]=$idx_onglet;
            $this->elements[$onglet["id"]]=$tmp;
            foreach ($onglet["champs"] as $idx_champ => $champ) {
                $tmp=array();
                $tmp["type_element"]="champ";
                $tmp["parent"]=$onglet["id"];
                $tmp["idx"]=$idx_champ;
                $this->elements[$champ["id"]]=$tmp;
                foreach ($champ["ss_champs"] as $idx_ss_champ => $ss_champ) {
                    $tmp=array();
                    $tmp["type_element"]="ss_champ";
                    $tmp["parent"]=$champ["id"];
                    $tmp["idx"]=$idx_ss_champ;
                    //$tmp["valeur"]=$ss_champ["valeur"];
                    $this->elements[$ss_champ["id"]]=$tmp;
                }
            }
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_last_id () {
    $this->last_id++;
    return ($this->last_id);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne des infos supplémentaires à partir de l'ID d'un élément :
// type_element => onglet | champ | ss_champ
// nom_champ (ex. 700) : pour les champs et les ss_champs
// nom_ss_champ (ex. a) : pour les ss-champs
// idx_ss_champ : positionnement du ss-champ dans $onglets (par rapport au champ parent)
// idx_champ : positionnement du champ dans $onglets (par rapport à l'onglet parent)
// idx_onglet : positionnement de l'onglet dans $onglets (par rapport à la racine)
// ID_parent : id de l'élément parent ATTENTION : ID en majuscules :/
// valeur : valeur (pour les ss-champs)

    function get_infos_element ($ID_element) {
        $retour=array();
        $retour["type_element"]="";
        $retour["nom_champ"]="";
        $retour["nom_ss_champ"]="";
        $retour["idx_ss_champ"]="";
        $retour["idx_champ"]="";
        $retour["idx_onglet"]="";
        $retour["ID_parent"]="";
        if (! isset($this->elements[$ID_element])) {
            return (0);
        }
        $element=$this->elements[$ID_element];
        $idx_element=$element["idx"];
        $retour["type_element"]=$element["type_element"];
        
        if ($element["type_element"] == "ss_champ") {
            $retour["ID_parent"]=$element["parent"];
            $retour["idx_ss_champ"]=$idx_element;
            $champ=$this->elements[$element["parent"]];
            $idx_champ=$champ["idx"];
            $retour["idx_champ"]=$idx_champ;
            $onglet=$this->elements[$champ["parent"]];
            $idx_onglet=$onglet["idx"];
            $retour["idx_onglet"]=$idx_onglet;
            $retour["nom_champ"]=$this->onglets[$idx_onglet]["champs"][$idx_champ]["nom"];
            $retour["nom_ss_champ"]=$this->onglets[$idx_onglet]["champs"][$idx_champ]["ss_champs"][$idx_element]["nom"];
            $retour["valeur"]=$this->onglets[$idx_onglet]["champs"][$idx_champ]["ss_champs"][$idx_element]["valeur"];
        } elseif ($element["type_element"] == "champ") {
            $retour["ID_parent"]=$element["parent"];
            $retour["idx_champ"]=$idx_element;
            $champ=$element;
            $idx_champ=$idx_element;
            $onglet=$this->elements[$champ["parent"]];
            $idx_onglet=$onglet["idx"];
            $retour["idx_onglet"]=$idx_onglet;
            $retour["nom_champ"]=$this->onglets[$idx_onglet]["champs"][$idx_champ]["nom"];
        } elseif ($element["type_element"] == "onglet") {
            $retour["idx_onglet"]=$idx_element;
        }
        
        return ($retour);
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    function update_element ($ID_element, $update) {
        $infos=$this->get_infos_element($ID_element);
        // SS_CHAMP
        if ($infos["type_element"]=="ss_champ") {
            foreach ($update as $amodifier => $valeur) {
                $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$infos["idx_ss_champ"]][$amodifier]=$valeur;
            }
        }
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    function delete_element ($ID_element) {
        $infos=$this->get_infos_element($ID_element);
        if ($infos["type_element"]=="ss_champ") {
            $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"]=tvs_unset_array ($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"], $infos["idx_ss_champ"]);
            unset ($this->elements[$ID_element]);
            foreach ($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"] as $idx => $ss_champ) {
                $this->elements[$ss_champ["id"]]["idx"]=$idx;
            }
        } elseif ($infos["type_element"]=="champ") {
            // on supprime les sous-champs de manière récursive
            foreach ($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"] as $ss_champ) {
                $this->delete_element($ss_champ["id"]);
            }
            // puis on supprime le champ
            $this->onglets[$infos["idx_onglet"]]["champs"]=tvs_unset_array ($this->onglets[$infos["idx_onglet"]]["champs"], $infos["idx_champ"]);
            unset ($this->elements[$ID_element]);
            foreach ($this->onglets[$infos["idx_onglet"]]["champs"] as $idx => $champ) {
                $this->elements[$champ["id"]]["idx"]=$idx;
            }
        }
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    function monte_descend_ss_champ ($ID_element, $sens) {
        $infos=$this->get_infos_element($ID_element);
        if ($sens == "descendre") {
            $idx_rempl=$infos["idx_ss_champ"]+1;
        } else {
            $idx_rempl=$infos["idx_ss_champ"]-1;
        }
        if (! isset ($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$idx_rempl])) {
            return (false);
        }
        $ID_rempl=$this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$idx_rempl]["id"];
        $tmp=$this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$idx_rempl];
        $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$idx_rempl]=$this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$infos["idx_ss_champ"]];
        $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$infos["idx_ss_champ"]]=$tmp;
        $this->elements[$ID_element]["idx"]=$idx_rempl;
        $this->elements[$ID_rempl]["idx"]=$infos["idx_ss_champ"];
        return ($ID_rempl);
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    function monte_descend_champ ($ID_element, $sens) {
        $infos=$this->get_infos_element($ID_element);
        if ($sens == "descendre") {
            $idx_rempl=$infos["idx_champ"]+1;
        } else {
            $idx_rempl=$infos["idx_champ"]-1;
        }
        if (! isset ($this->onglets[$infos["idx_onglet"]]["champs"][$idx_rempl])) {
            return (false);
        }
        $ID_rempl=$this->onglets[$infos["idx_onglet"]]["champs"][$idx_rempl]["id"];
        $tmp=$this->onglets[$infos["idx_onglet"]]["champs"][$idx_rempl];
        $this->onglets[$infos["idx_onglet"]]["champs"][$idx_rempl]=$this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]];
        $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]=$tmp;
        $this->elements[$ID_element]["idx"]=$idx_rempl;
        $this->elements[$ID_rempl]["idx"]=$infos["idx_champ"];
        return ($ID_rempl);
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// si on fournit $ordonner = 0 les ss-champs seront ajoutés à la fin sans tri
// ce paramètre est optionnel
    function insere_ss_champ ($ID_champ, $ss_champ, $ordonner = 1) {
        $infos=$this->get_infos_element($ID_champ);
        $ss_champ["id"]=$this->get_last_id(); // on attribue une ID
        
        // on regarde où l'insérer (on insère AVANT si déjà sous-champ identique) 
        if ($ordonner == 1) {
            $idx=0;
            if (count($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"]) > 0) {
                $id_elem_remplace=$this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][0]["id"]; // Valeur par défaut = ID du premier élément
            }
            for ($i = count($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"])-1; $i>=0 ; $i--) {
                if ($ss_champ["nom"] >= $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$i]["nom"]) {
                    $idx=$i+1;
                    if ($i == count($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"])) {
                        $id_elem_remplace=""; // Si dernier élémenr, mettre en dernier
                    } else {
                        $id_elem_remplace=$this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"][$i+1]["id"]; // sinon, mettre avant l'élément précédent
                    }
                    break;
                }
            }
        } else { // si pas de tri on met en dernier
            $idx=count($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"]);
            $id_elem_remplace=""; 
        }
        
        // on insère l'élément
        $this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"]=tvs_insert_array($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"], $ss_champ, $idx);
   
        // on regénère $this->elements
        foreach ($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"] as $nv_idx => $nv_ss_champ) {
            if (isset($this->elements[$nv_ss_champ["id"]])) {
                $this->elements[$nv_ss_champ["id"]]["idx"]=$nv_idx;
            } else {
                $tmp=array();
                $tmp["type_element"]="ss_champ";
                $tmp["parent"]=$ID_champ;
                $tmp["idx"]=$nv_idx;
                $this->elements[$nv_ss_champ["id"]]=$tmp;
            }
        }
        return (array("idx"=>$idx, "ID"=>$ss_champ["id"], "ID_champ"=>$ID_champ , "ID_rempl"=>$id_elem_remplace, "ss_champ"=>$ss_champ));
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function insere_champ ($idx_onglet, $champ) {
        $ID_onglet=$this->onglets[$idx_onglet]["id"];
        $infos=$this->get_infos_element($ID_onglet);
     
        // on attribue une ID au champ et aux sous-champs
        $champ["id"]=$this->get_last_id(); 
        $tmp_idx=0;
        $tmp_array=array();
        foreach ($champ["ss_champs"] as $idx_ss_champ => $onsenfout) {
            $champ["ss_champs"][$idx_ss_champ]["id"]=$this->get_last_id();
            $tmp_array[$tmp_idx]=$champ["ss_champs"][$idx_ss_champ]; // pour remplacer les clefs de type "02 - f" en 2
            if ($tmp_array[$tmp_idx]["valeur"]=="") {
                $tmp_array[$tmp_idx]["valeur"]=$tmp_array[$tmp_idx]["valeur_defaut"];
            }
            $tmp_idx++;
        }
        $champ["ss_champs"]=$tmp_array;
        
        
        // on regarde où l'insérer (on insère AVANT si déjà sous-champ identique)
        $idx=0;
        if (count($this->onglets[$idx_onglet]["champs"]) > 0) {
            $id_elem_remplace=$this->onglets[$idx_onglet]["champs"][0]["id"]; // Valeur par défaut = ID du premier élément
        }
        for ($i = count($this->onglets[$idx_onglet]["champs"])-1; $i>=0 ; $i--) {
            if ($champ["nom"] >= $this->onglets[$idx_onglet]["champs"][$i]["nom"]) {
                $idx=$i+1;
                if ($i == count($this->onglets[$idx_onglet]["champs"])) {
                    $id_elem_remplace=""; // Si dernier élémenr, mettre en dernier
                } else {
                    $id_elem_remplace=$this->onglets[$idx_onglet]["champs"][$i+1]["id"]; // sinon, mettre avant l'élément précédent
                }
                break;
            }
        }
        
        // on insère l'élément
        $this->onglets[$idx_onglet]["champs"]=tvs_insert_array($this->onglets[$idx_onglet]["champs"], $champ, $idx);

        // on regénère $this->elements
        // Pour les champs
        foreach ($this->onglets[$idx_onglet]["champs"] as $nv_idx => $nv_champ) {
            if (isset($this->elements[$nv_champ["id"]])) {
                $this->elements[$nv_champ["id"]]["idx"]=$nv_idx;
            } else {
                $tmp=array();
                $tmp["type_element"]="champ";
                $tmp["parent"]=$ID_onglet;
                $tmp["idx"]=$nv_idx;
                $this->elements[$nv_champ["id"]]=$tmp;
            }
        }
        // Pour les nouveaux sous-champs
        foreach ($champ["ss_champs"] as $idx_ss_champ => $ss_champ) {
            $tmp=array();
            $tmp["type_element"]="ss_champ";
            $tmp["parent"]=$champ["id"];
            $tmp["idx"]=$idx_ss_champ;
            $this->elements[$ss_champ["id"]]=$tmp;
        }
        
        return (array("idx"=>$idx, "ID"=>$champ["id"], "ID_rempl"=>$id_elem_remplace, "ID_onglet"=>$ID_onglet, "champ"=>$champ));
        
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function vide_champ ($id_champ, $ss_champs_a_conserver) {
        $infos=$this->get_infos_element($id_champ);
        $retour=array();
        foreach ($this->onglets[$infos["idx_onglet"]]["champs"][$infos["idx_champ"]]["ss_champs"] as $idx_ss_champ => $ss_champ) {
            $id_ss_champ=$ss_champ["id"];
            $nom_ss_champ=$ss_champ["nom"];
            $bool=0;
            foreach ($ss_champs_a_conserver as $ss_champ_a_conserver) {
                if ($ss_champ_a_conserver == $nom_ss_champ) {
                    $bool=1;
                }
            }
            if ($bool == 0) {
                $this->delete_element($id_ss_champ);
                array_push($retour, $id_ss_champ);
            }
        }
        return ($retour);
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// retourne une liste de sous-champs d'un champ donné
// on indique l'ID du champ et le nom ($a, $b...) des ss-champs à trouver
// si pas de nom de sous-champ fourni, retourne TOUS les sous-champs
// pour chaque ss-champ, on a les éléments fournis dans $onglet (cf haut de page)
    function get_ss_champs_by_nom ($id_champ, $nom_ss_champ) {
        $liste_ss_champs=array();
        $infos=$this->get_infos_element($id_champ);
        $idx_onglet=$infos["idx_onglet"];
        $idx_champ=$infos["idx_champ"];
        foreach ($this->onglets[$idx_onglet]["champs"][$idx_champ]["ss_champs"] as $ss_champ) {
            if ($ss_champ["nom"] == $nom_ss_champ OR $nom_ss_champ == "") {
                array_push ($liste_ss_champs, $ss_champ);
            }
        }
        return ($liste_ss_champs);   
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Retourne la liste des champs portant un nom (200) 
// pour chaque champ on a les éléments fournis dans $onglet (cf haut de page)  
    function get_champs_by_nom ($nom_champ) {
        $liste_champs=array();
        foreach ($this->onglets as $onglet) {
            foreach ($onglet["champs"] as $champ) {
                if ($champ["nom"]==$nom_champ) {
                    array_push ($liste_champs, $champ);
                }
            }
        }
        return ($liste_champs);
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// cas particulier où on veut récupérer un ss-champ non répétable d'un champ non répétable   
// le ss-champ fourni contient les infos contenues dans $onglets (cf haut de page)
    function get_ss_champ_simple ($nom_champ, $nom_ss_champ) {
        $champs=$this->get_champs_by_nom($nom_champ);
        foreach ($champs as $champ) {
            $id_champ=$champ["id"];
            $ss_champs=$this->get_ss_champs_by_nom($id_champ, $nom_ss_champ);
            foreach ($ss_champs as $ss_champ) {
                return ($ss_champ); // on ne retourne que le premier ss-champ du 1er champ
            }
        }
        return (array());
    }

    
} // fin de la classe

?>