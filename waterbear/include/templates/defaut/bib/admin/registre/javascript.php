<!-- Pour l'arbre --> 
<!-- <SCRIPT type="text/javascript" src="js/yui/treeview/treeview-min.js"></script>  -->
<script type="text/javascript" src="js/yui/treeview/treeview.js"></script> 




<script language="javascript">

var detail_noeuds = new Array();
var tree_todo= new Array();
var tree;
var ws_path="<?PHP print ($GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]);  ?>?module=admin/registre";
var acces_direct="<?PHP print($acces_direct);  ?>";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Fonction lancée quand la page est chargée
function init() {
  	affiche_arbre ();
    if (acces_direct != "") {
        focus_noeud_chemin(0, acces_direct);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher l'arbre au départ
function affiche_arbre () {
	tree = new YAHOO.widget.TreeView("arbre"); // On crée l'objet tree
	tree.setDynamicLoad(charge_node, 0); // Active la récupération dynamique des données
	//tree.setNodesProperty("propagateHighlightUp",true);
	var rootNode = tree.getRoot(); // on récupère la racine
	
	// On Crée le noeud de base (Registre)
	var tmpNode = new YAHOO.widget.TextNode("Registre", tree.getRoot(), false);
	tmpNode.labelElId="0";
	detail_noeuds[0]=new Object();
	detail_noeuds[0].ref=tmpNode;
	detail_noeuds[0].nom="Registre";
	detail_noeuds[0].valeur="";
	detail_noeuds[0].chemin="/";
	detail_noeuds[0].description="<?PHP print (get_intitule("bib/admin/registre", "description_racine", array()));  ?>";
	detail_noeuds[0].parent=0; // ??


	tree.subscribe("expandComplete", function(node) {
    	affiche_detail_noeud(node);
    	node.focus();
    	if (tree_todo.length > 0) {
            var tmp=tree_todo[0];
            tree_todo.shift();
    	  	eval (tmp);
		}
    });
    
    tree.subscribe("collapseComplete", function(node) {
    	affiche_detail_noeud(node);
    	if (tree_todo.length > 0) {
    	  	var tmp=tree_todo[0];
            tree_todo.shift();
    	  	eval (tmp);
		}
    });
    
    tree.subscribe("clickEvent", function(param) {
		param.node.focus();
    	affiche_detail_noeud(param.node);
    });
  
	tree.draw();

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Chargement d'un noeud'
// le 2e argument est une référence vers une fonction (fournie par yahoo) à appeler quand le noeud est chargé pour qu'il affiche
function charge_node (node, fnLoadComplete) {
  	var sUrl = ws_path+"&operation=afficher_branche&ID_noeud="+node.labelElId;
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (YAHOO.lang.isArray(oResults)) { // Si réponses
            	if (oResults.length == 0) {
				  	node.isLeaf=true;
				}
	            for (var i=0 ; i < oResults.length; i++) { // Pour chaque branche récupérée
	            	var tempData={label: oResults[i].nom};// on peut spécifier d'autres propriétés du noeud
				  	//var tempNode = new YAHOO.widget.TextNode(oResults[i].nom, node, false);
				  	var tempNode = new YAHOO.widget.TextNode(tempData, node);
				  	tempNode.labelElId=oResults[i].ID;
				  	detail_noeuds[oResults[i].ID]=new Object();
				  	detail_noeuds[oResults[i].ID].ref=tempNode; // objet noeud
				  	detail_noeuds[oResults[i].ID].nom = oResults[i].nom;
				  	detail_noeuds[oResults[i].ID].parent = oResults[i].parent;
				  	detail_noeuds[oResults[i].ID].description = oResults[i].description;
				  	detail_noeuds[oResults[i].ID].chemin = oResults[i].chemin;
				  	detail_noeuds[oResults[i].ID].valeur = oResults[i].valeur;
				}
			} else { // Si pas de réponses
				alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_recuperation", array()));  ?>");
			}
            oResponse.argument.fnLoadComplete(); // Pour dire qu'on a fini de recevoir les données
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
            "node": node,
            "fnLoadComplete": fnLoadComplete
        },
        
        // AUTRES INFOS
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche le détail d'un noeud dans le formulaire de droite
function affiche_detail_noeud (node) {
  	// On récupère les champs intéressants du formulaire
  	var champ_nom=document.getElementById("champ_nom");
  	var champ_chemin=document.getElementById("champ_chemin");
  	var champ_valeur=document.getElementById("champ_valeur");
  	var champ_description=document.getElementById("champ_description");
  	var champ_ID=document.getElementById("champ_ID");
  	var ID = node.labelElId;
  	
  	// Si noeud racine, on rend le formulaire non modifiable
  	if (ID==0) {
		champ_valeur.readOnly=true;
		champ_nom.readOnly=true;
		champ_description.readOnly=true;
	} else {
		champ_valeur.readOnly=false;
		champ_nom.readOnly=false;
		champ_description.readOnly=false;
	}
	
	// On remplit les champs du formulaire
	champ_nom.value=detail_noeuds[ID].nom;
	champ_chemin.value=detail_noeuds[ID].chemin;
	champ_valeur.value=detail_noeuds[ID].valeur;
	champ_ID.value=ID;
	champ_description.value=detail_noeuds[ID].description;
	
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Va à la déclaration d'un plugin ou à la définition si "??XXXX"

function goto_definition () {
    var nom=document.getElementById("champ_nom").value;
    var valeur=document.getElementById("champ_valeur").value;
    if (nom=="nom_plugin") {
        focus_noeud_chemin (0, "Registre/profiles/defaut/plugins/plugins/"+valeur);
    } else if (nom.substr(0,2)=="??") {
        var liste=valeur.split("/");
        var nb=liste.length;
        liste[nb-1]="_intitules/"+liste[nb-1];
        chemin=liste.join("/");
        focus_noeud_chemin (0, "Registre/profiles/defaut/langues/"+chemin);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Va aux PA d'un plugin'

function goto_utilisation () {
    var chemin=document.getElementById("champ_chemin").value;
    var tmp=chemin.split("/");
    var tmp2="";
    for (var i=4 ; i<tmp.length ; i++) {
        if (tmp2 != "") {
            tmp2+="/";
        }
        tmp2+=tmp[i];
    }
   
   var url="bib.php?module=admin/cherche_registre&nom_noeud=nom_plugin&valeur_noeud="+tmp2; 
   window.open(url, "", "");
    
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Se positionne sur un noeud enfant à partir de son label

function focus_noeud (parent, label) {
  	var enfants=parent.children;
  	for (var i=0 ; i<enfants.length ; i++) {
	    //alert ("compare "+enfants[i].label+" et "+label);
	    if (enfants[i].label == label) {
	      	enfants[i].expand();
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Se positionne sur un noeud en déployant l'arborescence donnée dans un chemin
// si index vaut 0 => parent = root

function focus_noeud_chemin (index, chemin) {

  
    var case_a_cocher=document.getElementById("bool_open_new");
    if (case_a_cocher.checked == true) {
        case_a_cocher.checked = false;
        url="bib.php?module=admin/registre&acces_direct="+chemin;
        window.open(url);
        return (true);
    }
    
    // 1) On récupère l'élément à ouvrir
    var parent;
    var enfant;
    var liste=chemin.split("/");
    var label = liste[0];
    var chemin2="";
    for (var i=1 ; i < liste.length ; i++) {
        if (chemin2 != "") {
            chemin2+="/";
        }
        chemin2+=liste[i];
    }
  
    
    // 2) si 1er appel, on par de root sinon on part de l'élément fourni
    if (index == 0) {
        tree.collapseAll();
        parent=tree.getRoot();
    } else {
        parent=tree.getNodeByIndex(index);
    }
    if (parent == null) {
        alert ("parent = null");
    }
    
    // 3) On récupère la bonne branche
    var enfants = parent.children;
    var idx=0;
    
    for (var i=0 ; i<enfants.length ; i++) {
	    //alert ("compare "+enfants[i].label+" et "+label);
	    if (enfants[i].label == label) {
	      	enfant=enfants[i];
            idx=enfant.index;
		}
	}
    if (idx == 0) {
        alert ("aucun noeud "+label+" dans chemin");
    }
    
    // 4) on ouvre l'élément et on propage
    if (chemin2 != "") {
        tree_todo.push("focus_noeud_chemin ("+idx+", \""+chemin2+"\");");
    }
    enfant.expand();

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Creer un nouveau noeud vierge
function creer_noeud () {
  	var ID_parent=document.getElementById("champ_ID").value;
  	if (ID_parent == "") {
		alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_crea_node_sans_parent", array()));  ?>");
		return false;
	}
  	var sUrl = ws_path+"&operation=creer_noeud_vierge&ID_parent="+ID_parent;
  	
	///////////////// !!! CALLBACK !!!
	var creer_noeud_callback = {
	  	// SUCCES
	  	success: function(oResponse) {
	  	  	var oResults = eval("(" + oResponse.responseText + ")");
	  	  	if (oResults.succes == 0) {
				alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_crea_node", array()));  ?> " + oResults.erreur);  
			} else {
			  	if (detail_noeuds[ID_parent].ref.children.length!=0) { // Si le noeud parent avait déjà des enfants, on recharge le noeud
				  	tree.removeChildren(detail_noeuds[ID_parent].ref); 
				  	detail_noeuds[ID_parent].ref.expand(); // on ouvre le noeud parent
				  	tree_todo.push("focus_noeud (detail_noeuds["+ID_parent+"].ref, \"nouveau noeud\")"); // on ouvre le nouveau noeud (une fois que l'action précédente est terminée)
			  	} else { // S'il n'en avait pas, on recharge le noeud parent
			  		var ID_grand_parent=detail_noeuds[ID_parent].parent;
			  		tree.removeChildren(detail_noeuds[ID_grand_parent].ref); 
				  	detail_noeuds[ID_grand_parent].ref.expand();
				  	tree_todo.push("focus_noeud (detail_noeuds["+ID_grand_parent+"].ref, \""+detail_noeuds[ID_parent].ref.label+"\")");
				  	tree_todo.push("focus_noeud (detail_noeuds["+ID_parent+"].ref, \"nouveau noeud\")");
			  	}
			  	
			}
	 	},
	 	
	 	// ECHEC
		failure: function(oResponse) {
			alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
		},
		
		// ARGUMENTS
		argument: {
		},
		
		// AUTRES INFOS
		timeout: 7000,
		cache: false
	} 
	///////////////// !!! FIN DU CALLBACK !!!
		
  	YAHOO.util.Connect.asyncRequest('GET', sUrl, creer_noeud_callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprimer un noeud et tous ses enfants
function supprimer_noeud() {
  	var ID=document.getElementById("champ_ID").value;
  	var ID_parent=detail_noeuds[ID].parent;
  	if (ID == "") {
  	  	alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_crea_node_sans_parent", array()));  ?>");
		return false;
	}
	if (ID == 0) {
  	  	alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_racine", array()));  ?>");
		return false;
	}
	var sUrl = ws_path+"&operation=supprimer_noeud&ID_noeud="+ID;
	
	///////////////// !!! CALLBACK !!!
  	var supprimer_noeud_callback = {
  	  	// SUCCES
	  	success: function(oResponse) {
	  	  	var oResults = eval("(" + oResponse.responseText + ")");
	  	  	if (oResults.succes != 1) {
				alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_suppr_node", array()));  ?>"+oResults.erreur)	  
			} else {
			  	tree.removeChildren(detail_noeuds[ID_parent].ref); 
				detail_noeuds[ID_parent].ref.expand();
			}
		},
		
		// ECHEC
		failure: function(oResponse) {
			alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
		},
		
		// ARGUMENTS
		argument: {
		},
		
		// AUTRES INFOS
		timeout: 7000,
		cache: false
	}
	///////////////// !!! FIN DU CALLBACK !!!
		
  	YAHOO.util.Connect.asyncRequest('GET', sUrl, supprimer_noeud_callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Confirmation de suppression d'un noeud

function confirm_supprimer_noeud () {
  	if (confirm("<?PHP print (get_intitule("bib/admin/registre", "confirm_suppr_node", array()));  ?>")) {
	    supprimer_noeud()
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ d'un noeud
function update_noeud () {
  	// On récupère les infos du formulaire
  	var ID=document.getElementById("champ_ID").value;
  	var nom=document.getElementById("champ_nom").value;
  	var valeur=document.getElementById("champ_valeur").value;
  	var description=document.getElementById("champ_description").value;
  	var ID_parent=detail_noeuds[ID].parent;
  	if (ID == "") {
  	  	alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_crea_node_sans_parent", array()));  ?>");
  	  	return false;
	}
	if (ID == 0) {
  	  	alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_racine", array()));  ?>");
  	  	return false;
	}
	nom=encodeURIComponent(nom);
	valeur=encodeURIComponent(valeur);
	description=encodeURIComponent(description);
	var sUrl = ws_path+"&operation=update_noeud&ID_noeud="+ID+"&nom="+nom+"&valeur="+valeur+"&description="+description;
	
	///////////////// !!! CALLBACK !!!
	var update_noeud_callback = {
	  	// SUCCES
	  	success: function(oResponse) {
	  	  	var oResults = eval("(" + oResponse.responseText + ")");
	  	  	if (oResults.succes != 1) {;
				alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_update_node", array()));  ?>"+oResults.erreur)	  
			} else {
			  	if (ID_parent == 0) {
				    window.location.reload();
				} else {
				  	tree.removeChildren(detail_noeuds[ID_parent].ref); 
				  	detail_noeuds[ID_parent].ref.expand();
				  	tree_todo.push("focus_noeud (detail_noeuds["+ID_parent+"].ref, \""+nom+"\")");
			  	}
			  	
			}
	 	},
	 	
	 	// ECHEC
		failure: function(oResponse) {
			alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
		},
		
		// ARGUMENTS
		argument: {
		},
		
		// AUTRES INFOS
		timeout: 7000,
		cache: false
	}
	///////////////// !!! CALLBACK !!!	
	
	YAHOO.util.Connect.asyncRequest('GET', sUrl, update_noeud_callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Copier un noeud

function tree_copy () {
	document.getElementById("tree_presse_papier").value=document.getElementById("champ_chemin").value;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Coller un noeud

function tree_paste (copie_contenu) {
  	var destination=document.getElementById("champ_chemin").value;
  	var modele=document.getElementById("tree_presse_papier").value;
  	var nom=document.getElementById("champ_nom").value;
  	var ID=document.getElementById("champ_ID").value;
  	var ID_parent=detail_noeuds[ID].parent;
  	var sUrl = ws_path+"&operation=copy_branche&modele="+modele+"&destination="+destination+"&copie_contenu="+copie_contenu;
  		///////////////// !!! CALLBACK !!!
	var tree_paste_callback = {
	  	// SUCCES
	  	success: function(oResponse) {
	  	  	var oResults = eval("(" + oResponse.responseText + ")");
	  	  	if (oResults.succes != 1) {;
				alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_paste", array()));  ?>"+oResults.erreur)	  
			} else {
			  	if (ID_parent == 0) {
				    window.location.reload();
				} else {
				  	tree.removeChildren(detail_noeuds[ID_parent].ref); 
				  	detail_noeuds[ID_parent].ref.expand();
				  	tree_todo.push("focus_noeud (detail_noeuds["+ID_parent+"].ref, \""+nom+"\")");
			  	}
			  	
			}
	 	},
	 	
	 	// ECHEC
		failure: function(oResponse) {
			alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
		},
		
		// ARGUMENTS
		argument: {
		},
		
		// AUTRES INFOS
		timeout: 7000,
		cache: false
	}
	///////////////// !!! CALLBACK !!!	
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, tree_paste_callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Reset registre

function reset_registre () {
  	
  	var sUrl = ws_path+"&operation=reset_registre";
  		///////////////// !!! CALLBACK !!!
	var reset_registre_callback = {
	  	// SUCCES
	  	success: function(oResponse) {
	  	  	var oResults = eval("(" + oResponse.responseText + ")");
	  	  	if (oResults.succes != 1) {;
				alert ("ERREUR");	  
			} else {
			  	alert ("OK");
			}
	 	},
	 	
	 	// ECHEC
		failure: function(oResponse) {
			alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
		},
		
		// ARGUMENTS
		argument: {
		},
		
		// AUTRES INFOS
		timeout: 7000,
		cache: false
	}
	///////////////// !!! CALLBACK !!!	
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, reset_registre_callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Exporter un noeud (visuel)
function exporter_branche () {
  	var chemin=document.getElementById("champ_chemin").value;
	window.open(ws_path+"&operation=exporter_branche_visuel&chemin="+chemin);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Exporter un noeud (maj)
function exporter_branche_maj () {
  	var chemin=document.getElementById("champ_chemin").value;
	window.open(ws_path+"&operation=exporter_branche_maj&chemin="+chemin);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Générer un  script PHP pour ce noeud
function exporter_branche_compilation (tout, reset) {
  	var chemin=document.getElementById("champ_chemin").value;
	window.open(ws_path+"&operation=exporter_branche_compilation&chemin="+chemin+"&compiler_tout="+tout+"&reset="+reset);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


</script>