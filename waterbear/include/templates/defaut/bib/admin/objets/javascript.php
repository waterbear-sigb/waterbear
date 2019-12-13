
<!--  Pour les onglets -->
<script type="text/javascript" src="js/yui/element/element-min.js"></script>
<script type="text/javascript" src="js/yui/tabview/tabview-min.js"></script> 


<script type="text/javascript">
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var ws_path="<?PHP print ($GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]);  ?>?module=admin/objets";

// INTITULES
// Formulaire acces
intitules["l_form_acces_acces_a_modifier"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_acces_a_modifier", array()));  ?>";
intitules["l_form_acces_nom_colonne"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_nom_colonne", array()));  ?>";
intitules["l_form_acces_nom_usage"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_nom_usage", array()));  ?>";
intitules["l_form_acces_description"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_description", array()));  ?>";
intitules["l_form_acces_type_colonne"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_type_colonne", array()));  ?>";
intitules["l_form_acces_type_index"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_type_index", array()));  ?>";
intitules["l_form_acces_multivaleurs"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_multivaleurs", array()));  ?>";
intitules["bt_valider_acces"]="<?PHP print (get_intitule("bib/admin/objets", "bt_valider_acces", array()));  ?>";
intitules["bt_supprimer_acces"]="<?PHP print (get_intitule("bib/admin/objets", "bt_supprimer_acces", array()));  ?>";
intitules["l_form_acces_indication"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_indication", array()));  ?>";
// formulaire tri
intitules["l_form_tri_tri_a_modifier"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_tri_a_modifier", array()));  ?>";
intitules["l_form_tri_nom_colonne"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_nom_colonne", array()));  ?>";
intitules["l_form_tri_nom_usage"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_nom_usage", array()));  ?>";
intitules["l_form_tri_description"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_description", array()));  ?>";
intitules["l_form_tri_type_colonne"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_type_colonne", array()));  ?>";
intitules["l_form_tri_indication"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_type_colonne", array()));  ?>";
intitules["bt_valider_tri"]="<?PHP print (get_intitule("bib/admin/objets", "bt_valider_tri", array()));  ?>";
intitules["bt_supprimer_tri"]="<?PHP print (get_intitule("bib/admin/objets", "bt_supprimer_tri", array()));  ?>";
intitules["l_form_tri_indication"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_tri_indication", array()));  ?>";
// Onglets et menu
intitules["l_onglet_acces"]="<?PHP print (get_intitule("bib/admin/objets", "l_onglet_acces", array()));  ?>";
intitules["l_onglet_tri"]="<?PHP print (get_intitule("bib/admin/objets", "l_onglet_tri", array()));  ?>";
intitules["l_nv_acces"]="<?PHP print (get_intitule("bib/admin/objets", "l_nv_acces", array()));  ?>";
intitules["l_nv_tri"]="<?PHP print (get_intitule("bib/admin/objets", "l_nv_tri", array()));  ?>";
//erreurs et confirmations
intitules["erreur_connexion"]="<?PHP print (get_intitule("bib/admin/objets", "erreur_connexion", array()));  ?>";
intitules["objet_cree"]="<?PHP print (get_intitule("bib/admin/objets", "objet_cree", array()));  ?>";
intitules["erreur_suppr_obj_sans_afficher"]="<?PHP print (get_intitule("bib/admin/objets", "erreur_suppr_obj_sans_afficher", array()));  ?>";
intitules["confirm_suppr_objet"]="<?PHP print (get_intitule("bib/admin/objets", "confirm_suppr_objet", array()));  ?>";
intitules["objet_supprime"]="<?PHP print (get_intitule("bib/admin/objets", "objet_supprime", array()));  ?>";
intitules["acces_maj"]="<?PHP print (get_intitule("bib/admin/objets", "acces_maj", array()));  ?>";
intitules["confirm_suppr_acces"]="<?PHP print (get_intitule("bib/admin/objets", "confirm_suppr_acces", array()));  ?>";
intitules["acces_supprime"]="<?PHP print (get_intitule("bib/admin/objets", "acces_supprime", array()));  ?>";
intitules["tri_maj"]="<?PHP print (get_intitule("bib/admin/objets", "tri_maj", array()));  ?>";
intitules["confirm_suppr_tri"]="<?PHP print (get_intitule("bib/admin/objets", "confirm_suppr_tri", array()));  ?>";
intitules["tri_supprime"]="<?PHP print (get_intitule("bib/admin/objets", "tri_supprime", array()));  ?>";


var tabView;
var tab_acces;
var tab_tri;
var tab_liens;
var tab_contenu;
var objets;
var objet_selectionne="";
var acces_form_html = "<b><i> <div id='nom_objet_acces'>"+objet_selectionne+"</div></i></b><br><br>\n";
acces_form_html+="<table><tr><td>"+intitules["l_form_acces_acces_a_modifier"]+" : </td><td><input type='text' id='field_acces_ancien_nom_colonne' readonly='readonly'/></td></tr>";
acces_form_html+="<tr><td>"+intitules["l_form_acces_nom_colonne"]+" : </td><td><input type='text' ID='field_acces_nom_colonne'/> "+intitules["l_form_acces_indication"]+"</td></tr>";
acces_form_html+="<tr><td>"+intitules["l_form_acces_nom_usage"]+" : </td><td><input type='text' ID='field_acces_nom'/></td></tr>";
acces_form_html+="<tr><td>"+intitules["l_form_acces_description"]+" : </td><td><textarea  ID='field_acces_description'></textarea></td></tr>";
acces_form_html+="<tr><td>"+intitules["l_form_acces_type_colonne"]+" : </td><td><select ID='field_acces_type_col'><option value='VARCHAR(250)'>Varchar</option><option value='INT'>Int</option><option value='FLOAT'>Float</option><option value='DATE'>Date</option><option value='TEXT'>Text</option><option value='LONGTEXT'>LongText</option><option value='BLOB'>Blob</option></select></td></tr>";
acces_form_html+="<tr><td>"+intitules["l_form_acces_type_index"]+" : </td><td><select ID='field_acces_type_index'><option value='FULLTEXT'>Fulltext</option><option value=''>Normal</option><option value='30'>Normal(30)</option><option value='none'>Aucun</option></select></td></tr>";
acces_form_html+="<tr><td>"+intitules["l_form_acces_multivaleurs"]+" : </td><td><select ID='field_acces_multivaleurs'><option value='1'>oui</option><option value='0'>Non</option></select></td></tr>";
acces_form_html+="<tr><td><img src='IMG/icones/accept.png'  alt='"+intitules["bt_valider_acces"]+"' title='"+intitules["bt_valider_acces"]+"' onClick='acces_valide_form()'/></td><td><img src='IMG/icones/delete.png'  alt='"+intitules["bt_supprimer_acces"]+"' title='"+intitules["bt_supprimer_acces"]+"'  onClick='confirm_supprimer_acces()'/></td></tr></table>";

var tri_form_html = "<b><i> <div id='nom_objet_tri'>"+objet_selectionne+"</div></i></b><br><br>\n";
tri_form_html+="<table><tr><td>"+intitules["l_form_tri_tri_a_modifier"]+" : </td><td><input type='text' id='field_tri_ancien_nom_colonne' readonly='readonly'/></td></tr>";
tri_form_html+="<tr><td>"+intitules["l_form_tri_nom_colonne"]+" : </td><td><input type='text' ID='field_tri_nom_colonne'/> "+intitules["l_form_tri_indication"]+"</td></tr>";
tri_form_html+="<tr><td>"+intitules["l_form_tri_nom_usage"]+" : </td><td><input type='text' ID='field_tri_nom'/></td></tr>";
tri_form_html+="<tr><td>"+intitules["l_form_tri_description"]+" : </td><td><textarea  ID='field_tri_description'></textarea></td></tr>";
tri_form_html+="<tr><td>"+intitules["l_form_tri_type_colonne"]+" : </td><td><select ID='field_tri_type_col'><option value='VARCHAR(250)'>Varchar</option><option value='INT'>Int</option><option value='FLOAT'>Float</option><option value='DATE'>Date</option><option value='TEXT'>Text</option><option value='LONGTEXT'>LongText</option></select></td></tr>";
tri_form_html+="<tr><td><img src='IMG/icones/accept.png'  alt='"+intitules["bt_valider_tri"]+"' title='"+intitules["bt_valider_tri"]+"' onClick='tri_valide_form()'/></td><td><img src='IMG/icones/delete.png'  alt='"+intitules["bt_supprimer_tri"]+"' title='"+intitules["bt_supprimer_tri"]+"'  onClick='confirm_supprimer_tri()'/></td></tr></table>";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Standardise le nom de colonne (minuscules) et rajoute si nécessaire a_ (acces) ou t_ (tri)
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function traite_nom_colonne(nom_colonne, type) {
  	nom_colonne=nom_colonne.toLowerCase();
  	if (nom_colonne.substring(0,2) != type+"_") {
	    nom_colonne=type+"_"+nom_colonne;
	}
	return(nom_colonne);
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RAZ des onglets
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init_onglets() {
	tabView = new YAHOO.widget.TabView();
	 
	//tabView.addTab();
	
    tab_acces=new YAHOO.widget.Tab({
        label: intitules["l_onglet_acces"],
        content: '<table><tr><td style="vertical-align:top ; width:40% ; border-right-style:dotted ; border-right-width:1" ><div id="tab_acces_liste"><i><div onClick="nouvel_acces()">-- '+intitules["l_nv_acces"]+' --</div></i></div></td><td valign="top"><div id="tab_acces_form" >'+acces_form_html+'</div></td></tr></table>',
        active: true
    });

    tab_tri=new YAHOO.widget.Tab({
        label: intitules["l_onglet_tri"],
        content: '<table><tr><td style="vertical-align:top ; width:40% ; border-right-style:dotted ; border-right-width:1" ><div id="tab_tri_liste"><i><div onClick="nouveau_tri()">-- '+intitules["l_nv_tri"]+' --</div></i></div></td><td valign="top"><div id="tab_tri_form" >'+tri_form_html+'</div></td></tr></table>'
    });

	tabView.addTab(tab_acces);
	tabView.addTab(tab_tri);
    tabView.appendTo('container');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Chargement de la liste des objets
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_liste_objets () {
  	//var sUrl = ws_path+"&operation=get_liste_objets&reset=1";
  	var sUrl = ws_path+"&operation=get_liste_objets";
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
				objets=oResults.liste_objets; // on maj la liste des objets
				maj_combo_objets (); // on maj le combo
				init_onglets(); // on regénère les onglets
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Création d'un nouveau type d'objet''
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function create_objet () {
  	var nom_objet=document.getElementById("field_new_objet").value;
  	var sUrl = ws_path+"&operation=create_objet&type_objet="+nom_objet;
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
				objets[nom_objet]=new Array("param_fichiers");
				objets[nom_objet]["param_fichiers"]=new Array();
				objets[nom_objet]["param_fichiers"]["acces"]=new Array();
				objets[nom_objet]["param_fichiers"]["acces"]["liste"]=new Array();
				objets[nom_objet]["param_fichiers"]["tri"]=new Array();
				objets[nom_objet]["param_fichiers"]["tri"]["liste"]=new Array();
				objet_selectionne=nom_objet;
				maj_combo_objets (); // on maj le combo
				affiche_objet();
				alert (intitules["objet_cree"]+" : "+nom_objet);
				//init_onglets(); // on regénère les onglets
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprimer un type d'objet
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function confirm_supprimer_objet() {
  	if (objet_selectionne != document.getElementById("combo_objets").value) {
	    alert (intitules["erreur_suppr_obj_sans_afficher"]);
	    return(0);
	}
	if (confirm(intitules["confirm_suppr_objet"])) {
		    var sUrl = ws_path+"&operation=delete_objet&type_objet="+objet_selectionne;

		  	///////////////// !!! CALLBACK !!!
		    var callback = {
		      	// SUCCES
		        success: function(oResponse) {
		            var oResults = eval("(" + oResponse.responseText + ")");
		            if (oResults.succes != 1) {
						alert (oResults.erreur);
					} else {
						delete(objets[objet_selectionne]); // on supprime la branche
						objet_selectionne="";
						maj_combo_objets ();
						document.getElementById("tab_acces_liste").innerHTML="";
						document.getElementById("nom_objet").innerHTML="";
						nouvel_acces();
						alert (intitules["objet_supprime"]);
					}
		        },
		        
		        // ECHEC
		        failure: function(oResponse) {
		            alert (intitules["erreur_connexion"]);
		            oResponse.argument.fnLoadComplete();
		        },
		        
		        // ARGUMENTS
		        argument: {
		        },
		        
		        // AUTRES INFOS
		        timeout: 7000,
		        cache:false
		    };
		    ///////////////// !!! FIN DU CALLBACK !!!
		    
		    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
		}
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ le combo ave la liste des objets
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function maj_combo_objets () {
  	var combo_objets=document.getElementById("combo_objets");
  	// On efface tout...
  	var combo_length=combo_objets.options.length
  	for (var i=0 ; i<1000 ; i++) {
	    combo_objets.options[0]=null;
	    if (combo_objets.options.length==0) {
		  	i=1000;
		}
	}
	// ... et on recommence !
	var compteur=0;
	for (obj in objets) {
	  	var selectionne = false;
	  	if (obj == objet_selectionne) {
		    selectionne = true;
		}
	  	var opt = new Option (obj, obj, false, selectionne);
	//alert ("on recrée l'élément "+compteur+" : "+opt.value);
	  	combo_objets.options[compteur]=opt;
	  	compteur++;
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche un objet
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_objet() {
  	objet_selectionne=document.getElementById("combo_objets").value;
    // empty_objet
    affiche_empty_objet(objet_selectionne);
    
  	// Accès
  	var liste_acces=objets[objet_selectionne].param_fichiers.acces.liste;
  	var html_acces="";
  	for (acces in liste_acces) {
	    html_acces += "<div onClick='affiche_acces(\""+acces+"\")'>"+acces+"</div><br>\n";
	}
	html_acces +='<i><div onClick="nouvel_acces()">-- '+intitules["l_nv_acces"]+' --</div></i>';
	document.getElementById("tab_acces_liste").innerHTML=html_acces;
	document.getElementById("nom_objet_acces").innerHTML=objet_selectionne;
	nouvel_acces();
	
	// Tri
	var liste_tri=objets[objet_selectionne].param_fichiers.tri.liste;
  	var html_tri="";
  	for (tri in liste_tri) {
	    html_tri += "<div onClick='affiche_tri(\""+tri+"\")'>"+tri+"</div><br>\n";
	}
	html_tri +='<i><div onClick="nouveau_tri()">-- '+intitules["l_nv_tri"]+' --</div></i>';
	document.getElementById("tab_tri_liste").innerHTML=html_tri;
	document.getElementById("nom_objet_tri").innerHTML=objet_selectionne;
	nouveau_tri();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche le lien pour vider l'objet
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function affiche_empty_objet (objet) {
    var chaine="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><a href='#' onClick=\"empty_objet('"+objet+"')\">Vider la table "+objet+"</a></b>";
    var zone=document.getElementById("empty_objet");
    zone.innerHTML=chaine;
    
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Vider l'objet'
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function empty_objet (objet) {
    if (confirm("Voulez-vous vraiment supprimer tous les objets de type "+objet+" ?")) {
	    var sUrl = ws_path+"&operation=empty_objet&type_objet="+objet;
  	
	  	///////////////// !!! CALLBACK !!!
	    var callback = {
	      	// SUCCES
	        success: function(oResponse) {
	            var oResults = eval("(" + oResponse.responseText + ")");
	            if (oResults.succes != 1) {
					alert (oResults.erreur);
				} else {
					
					alert ("OK");
				}
	        },
	        
	        // ECHEC
	        failure: function(oResponse) {
	            alert (intitules["erreur_connexion"]);
	            oResponse.argument.fnLoadComplete();
	        },
	        
	        // ARGUMENTS
	        argument: {
	        },
	        
	        // AUTRES INFOS
	        timeout: 7000,
	        cache:false
	    };
	    ///////////////// !!! FIN DU CALLBACK !!!
	    
	    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ACCES @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RAZ du formulaire Acces pour créer un nouvel accès
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function nouvel_acces() {
  	document.getElementById("field_acces_ancien_nom_colonne").value="";
  	document.getElementById("field_acces_nom_colonne").value="";
  	document.getElementById("field_acces_nom").value="";
  	document.getElementById("field_acces_description").value="";
  	document.getElementById("field_acces_type_col").value="";
  	document.getElementById("field_acces_type_index").value="";
  	document.getElementById("field_acces_multivaleurs").value="";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche le détail d'un accès dans le formulaire
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_acces(acces) {
  	document.getElementById("field_acces_ancien_nom_colonne").value=acces;
  	document.getElementById("field_acces_nom_colonne").value=acces;
  	document.getElementById("field_acces_nom").value=objets[objet_selectionne].param_fichiers.acces.liste[acces].nom;
  	document.getElementById("field_acces_description").value=objets[objet_selectionne].param_fichiers.acces.liste[acces].description_colonne;
  	document.getElementById("field_acces_type_col").value=objets[objet_selectionne].param_fichiers.acces.liste[acces].type_colonne;
  	document.getElementById("field_acces_type_index").value=objets[objet_selectionne].param_fichiers.acces.liste[acces].type_index;
  	document.getElementById("field_acces_multivaleurs").value=objets[objet_selectionne].param_fichiers.acces.liste[acces].multivaleurs;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Valide le formulaire Acces
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function acces_valide_form() {
  	var ancien_nom_colonne=document.getElementById("field_acces_ancien_nom_colonne").value;
  	var nom_colonne=document.getElementById("field_acces_nom_colonne").value;
  	nom_colonne = traite_nom_colonne(nom_colonne, "a");
  	var nom=document.getElementById("field_acces_nom").value;
  	var description_colonne=document.getElementById("field_acces_description").value;
  	var type_colonne=document.getElementById("field_acces_type_col").value;
  	var type_index=document.getElementById("field_acces_type_index").value;
  	var multivaleurs=document.getElementById("field_acces_multivaleurs").value;
  	var sUrl = ws_path+"&operation=acces_valide_form&type_objet="+objet_selectionne+"&ancien_nom_colonne="+ancien_nom_colonne+"&nom="+nom+"&nom_colonne="+nom_colonne+"&description_colonne="+description_colonne+"&type_colonne="+type_colonne+"&type_index="+type_index+"&multivaleurs="+multivaleurs;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
			  	if (ancien_nom_colonne != "") {
			  		delete(objets[objet_selectionne].param_fichiers.acces.liste[ancien_nom_colonne]); // on supprime la branche avec l'ancien nom (si existe)
			  	}
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne]=new Array (); // on recrée une branche avec le nouveau nom.. peut ête le mêm que l'ancien
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne].nom_colonne=nom_colonne;
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne].nom=nom;
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne].description_colonne=description_colonne;
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne].type_colonne=type_colonne;
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne].type_index=type_index;
			  	objets[objet_selectionne].param_fichiers.acces.liste[nom_colonne].multivaleurs=multivaleurs;
			  	affiche_objet();
			  	affiche_acces(nom_colonne);
				alert (intitules["acces_maj"]);
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprime un acces
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function confirm_supprimer_acces() {
  	if (confirm(intitules["confirm_suppr_acces"])) {
  	  	var ancien_nom_colonne=document.getElementById("field_acces_ancien_nom_colonne").value;
	    var sUrl = ws_path+"&operation=acces_delete&type_objet="+objet_selectionne+"&ancien_nom_colonne="+ancien_nom_colonne;
  	
	  	///////////////// !!! CALLBACK !!!
	    var callback = {
	      	// SUCCES
	        success: function(oResponse) {
	            var oResults = eval("(" + oResponse.responseText + ")");
	            if (oResults.succes != 1) {
					alert (oResults.erreur);
				} else {
					delete(objets[objet_selectionne].param_fichiers.acces.liste[ancien_nom_colonne]); // on supprime la branche avec l'ancien nom (si existe)
					affiche_objet();
					nouvel_acces()
					alert (intitules["acces_supprime"]);
				}
	        },
	        
	        // ECHEC
	        failure: function(oResponse) {
	            alert (intitules["erreur_connexion"]);
	            oResponse.argument.fnLoadComplete();
	        },
	        
	        // ARGUMENTS
	        argument: {
	        },
	        
	        // AUTRES INFOS
	        timeout: 7000,
	        cache:false
	    };
	    ///////////////// !!! FIN DU CALLBACK !!!
	    
	    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ TRI @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RAZ du formulaire tri pour créer un nouveau tri
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function nouveau_tri() {
  	document.getElementById("field_tri_ancien_nom_colonne").value="";
  	document.getElementById("field_tri_nom_colonne").value="";
  	document.getElementById("field_tri_nom").value="";
  	document.getElementById("field_tri_description").value="";
  	document.getElementById("field_tri_type_col").value="";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche le détail d'un accès dans le formulaire
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_tri(tri) {
  	document.getElementById("field_tri_ancien_nom_colonne").value=tri;
  	document.getElementById("field_tri_nom_colonne").value=tri;
  	document.getElementById("field_tri_nom").value=objets[objet_selectionne].param_fichiers.tri.liste[tri].nom;
  	document.getElementById("field_tri_description").value=objets[objet_selectionne].param_fichiers.tri.liste[tri].description_colonne;
  	document.getElementById("field_tri_type_col").value=objets[objet_selectionne].param_fichiers.tri.liste[tri].type_colonne;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Valide le formulaire tri
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function tri_valide_form() {
  	var ancien_nom_colonne=document.getElementById("field_tri_ancien_nom_colonne").value;
  	var nom_colonne=document.getElementById("field_tri_nom_colonne").value;
  	nom_colonne = traite_nom_colonne(nom_colonne, "t");
  	var nom=document.getElementById("field_tri_nom").value;
  	var description_colonne=document.getElementById("field_tri_description").value;
  	var type_colonne=document.getElementById("field_tri_type_col").value;
  	var sUrl = ws_path+"&operation=tri_valide_form&type_objet="+objet_selectionne+"&ancien_nom_colonne="+ancien_nom_colonne+"&nom="+nom+"&nom_colonne="+nom_colonne+"&description_colonne="+description_colonne+"&type_colonne="+type_colonne;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
			  	if (ancien_nom_colonne != "") {
			  		delete(objets[objet_selectionne].param_fichiers.tri.liste[ancien_nom_colonne]); // on supprime la branche avec l'ancien nom (si existe)
			  	}
			  	objets[objet_selectionne].param_fichiers.tri.liste[nom_colonne]=new Array (); // on recrée une branche avec le nouveau nom.. peut ête le mêm que l'ancien
			  	objets[objet_selectionne].param_fichiers.tri.liste[nom_colonne].nom_colonne=nom_colonne;
			  	objets[objet_selectionne].param_fichiers.tri.liste[nom_colonne].nom=nom;
			  	objets[objet_selectionne].param_fichiers.tri.liste[nom_colonne].description_colonne=description_colonne;
			  	objets[objet_selectionne].param_fichiers.tri.liste[nom_colonne].type_colonne=type_colonne;
			  	affiche_objet();
			  	affiche_tri(nom_colonne);
				alert (intitules["tri_maj"]);
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprime un tri
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function confirm_supprimer_tri() {
  	if (confirm(intitules["confirm_suppr_tri"])) {
  	  	var ancien_nom_colonne=document.getElementById("field_tri_ancien_nom_colonne").value;
	    var sUrl = ws_path+"&operation=tri_delete&type_objet="+objet_selectionne+"&ancien_nom_colonne="+ancien_nom_colonne;
  	
	  	///////////////// !!! CALLBACK !!!
	    var callback = {
	      	// SUCCES
	        success: function(oResponse) {
	            var oResults = eval("(" + oResponse.responseText + ")");
	            if (oResults.succes != 1) {
					alert (oResults.erreur);
				} else {
					delete(objets[objet_selectionne].param_fichiers.tri.liste[ancien_nom_colonne]); // on supprime la branche avec l'ancien nom (si existe)
					affiche_objet();
					nouveau_tri()
					alert (intitules["tri_supprime"]);
				}
	        },
	        
	        // ECHEC
	        failure: function(oResponse) {
	            alert (intitules["erreur_connexion"]);
	            oResponse.argument.fnLoadComplete();
	        },
	        
	        // ARGUMENTS
	        argument: {
	        },
	        
	        // AUTRES INFOS
	        timeout: 7000,
	        cache:false
	    };
	    ///////////////// !!! FIN DU CALLBACK !!!
	    
	    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// au démarrage...
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init() {
  	get_liste_objets ();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
</script>

