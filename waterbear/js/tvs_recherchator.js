function tvs_recherchator () {


this.tabView;
this.tab_recherche;
this.tab_liste;
this.tab_notice;
this.tab_paniers;
this.no_onglet_liste=0;
this.no_onglet_notice=0;
this.nom_div_total;

this.conteneur_recherche;
this.nom_js; // "recherchator" ou "statator"

//this.nom_tab_stats_resultat;
//this.nom_div_tab_recherche;

this.prefixe_div;
this.classe_css;
this.ws_url;
this.ws_url_total;
this.id_operation;
this.tri_defaut;

this.html_icones_liste="";
this.html_icones_notice="";

this.ID=0; // id des éléments dans le conteneur
this.liste_criteres_ajout;
this.liste_tris;
this.liste_formats_liste;
this.liste_formats_notice;
this.formulaire_defaut;
this.format_liste_defaut;
this.format_notice_defaut;
this.message; // objet YUI pour l'affichage de messages
this.liste_champs = new Array(); // liste des objets champs de recherches (pour extraire les infos lorsque on valide le formulaire)
this.nb_notices=0;
this.nb_pages=0;
this.idx_notice_en_cours=0;
this.ID_notice_en_cours=0;
this.plugin_get_id; // plugin de formatage pour récupérer juste l'id d'une notice
this.type_2_grille;
this.page=1; // page en cours
this.id_appel=0; // si page de recherche ouverte par une autre => id de l'appel
this.liste_appels = new Array(); // dictionnaire des pages ouvertes

////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
this.init_formulaire = function () {
    // 1) on détermine le nom des différents <div> en fonction du préfixe
    this.nom_div_tab_recherche="div_tab_recherche_"+this.prefixe_div;
    this.nom_div_tab_liste="div_tab_liste_"+this.prefixe_div;
    this.nom_div_tab_notice="div_tab_notice_"+this.prefixe_div;
    this.nom_div_barre_recherche="div_barre_recherche_"+this.prefixe_div;
    this.nom_div_barre_liste="div_barre_liste_"+this.prefixe_div;
    this.nom_div_barre_notice="div_barre_notice_"+this.prefixe_div;
    this.nom_div_tri="div_tri_"+this.prefixe_div;
    this.nom_div_format_liste="div_format_liste_"+this.prefixe_div;
    this.nom_div_format_notice="div_format_notice_"+this.prefixe_div;
    this.nom_div_page_liste="div_page_liste_"+this.prefixe_div;
    this.nom_div_notice_en_cours="div_notice_en_cours_"+this.prefixe_div;
    
    
    // 2) Le conteneur qui contiendra les champs de recherche
    this.conteneur_recherche=new tvs_conteneur (document.getElementById(this.nom_div_tab_recherche), this.classe_css+"_recherche", this.classe_css+"_champ");
    
    // 3) Init le formulaire défaut
    this.init_formulaire_defaut();
    
    // 4) éventuellement on lance la recherche si vlaidation_auto == 1
    if (this.validation_auto == 1) {
        this.valide_formulaire(true);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

this.init_variable = function(nom, valeur) {
    var str="this."+nom+" = '"+valeur+"';";
    eval (str);
}   


////////////////////////////////////////////////////////////////////////////////////////////////////////////

this.init_variable_json = function(nom, valeur) {
    var tmp=eval("(" + valeur + ")");
    var str="this."+nom+" = tmp;";
    eval (str);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////

this.add_champ_recherche = function (param) {
    if (param["classe_css"]==undefined) {
        param["classe_css"]=this.classe_css;
    }
    var champ;
    var id=this.get_id();
    param.id=id;
    if (param["type_champ"] == "textbox") {  
        champ = new tvs_champ_textbox (param);
    } else if (param["type_champ"] == "autocomplete") {  
        champ = new tvs_champ_autocomplete (param);
    } else if (param["type_champ"] == "textarea") {
        champ = new tvs_champ_textarea (param);
    } else if (param["type_champ"] == "panier_lien") {  
        champ = new tvs_champ_panier_lien (param);
    } else if (param["type_champ"] == "select") {  
        champ = new tvs_champ_select (param);
    } else if (param["type_champ"] == "comptage") {  
        champ = new tvs_champ_comptage (param);
    } else {
        //alert ("type_champ "+param["type_champ"]+" inconnu");
        return (false);
    }
    
    var toto=champ.genere_champ();
    this.conteneur_recherche.add_element(toto, id);
    champ.post_genere_champ(); // à appeler une fois que le DOM a été maj
    this.liste_champs[id]=champ;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

this.add_champ = function (idx_critere) {
    this.add_champ_recherche(this.liste_criteres_ajout[idx_critere]);
    this.message.destroy();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

this.init_formulaire_defaut = function () {
    for (idx_critere in this.formulaire_defaut) {
        this.add_champ_recherche(this.formulaire_defaut[idx_critere]);
    } 
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

this.get_id = function() {
    this.ID++;
    return (this.ID);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher la liste des champs à insérer

this.affiche_liste_criteres = function () {
    var str = "";
    for (idx in this.liste_criteres_ajout) {
        str+="<a href='javascript:"+this.nom_js+".add_champ(\""+idx+"\");'>"+this.liste_criteres_ajout[idx]["critere_intitule"]+"</a><br>"
    }
    this.message = new YAHOO.widget.Panel("panel2", { width:"320px", visible:false, draggable:true, close:true, modal:true, fixedcenter:true } ); 
    this.message.setHeader("Ajouter un critere"); 
    this.message.setBody(str); 
    //var verif = this.message.render("container"); 
    var verif = this.message.render(document.body); 
    this.message.center();
    this.message.show();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Efface tous les champs du formulaires
this.empty_formulaire = function() {
    var liste=this.conteneur_recherche.get_liste_elements_ordered();
    for (idx in liste) {
        id=liste[idx];
        this.delete_champ(id);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Génère une array à partir du formulaire

this.formulaire_2_array = function () {
    var tableau=new Object();
    tableau.criteres=new Array();
    tableau.tris=new Array();
    // Les critères
    var liste=this.conteneur_recherche.get_liste_elements_ordered();
    //alert (print_r(liste, 0, "nl"));
    for (idx in liste) {
        id=liste[idx];
        var champ=this.liste_champs[id];
        var element=champ.valide_champ();
        tableau.criteres.push(element);
    }
    
    // les Tris
    try {
        tableau.tris[0]=document.getElementById(this.nom_div_tri).value;
    } catch (e) {
        tableau.tris[0]=this.tri_defaut;
    }
    
    // pagination
    tableau.page=this.page;
    
    // Format liste
    try {
        var format=document.getElementById(this.nom_div_format_liste).value;
    } catch (e) {
        var format=this.format_liste_defaut;
    }
    

    tableau.plugin_formate_notice=this.liste_formats_liste[format]["plugin_formate_notice"];
    tableau.plugin_formate_liste=this.liste_formats_liste[format]["plugin_formate_liste"];

    
    // TMP
    tableau.type_objet=this.type_objet; 
    tableau.format_resultat="formate";
    tableau.bool_parse_contenu="1"; 
    return (tableau);
    
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Génère une chaîne json à partir du formulaire

this.formulaire_2_json = function (tableau) {
    var chaine=JSON.encode(tableau);
    return (chaine);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Exporte la liste dans une nouvelle fenêtre
this.exporter_liste = function (mode) {
    if (mode == 1) {
        var fenetre=window.open(this.url_exporter, "", "");
    } else {
        var fenetre=window.open(this.url_telecharger, "", "");
    }
}





///////////////////////////////////////////////////////////////////////////////////////////////////////
// Valide le formulaire pour ajouter les notices trouvées au panier en cours

this.valide_formulaire_panier = function(action_panier, ID_panier) {
    affiche_waiting(true);
    var tableau=this.formulaire_2_array();
    var chaine=this.formulaire_2_json(tableau);
    //this.last_recherche=chaine; // ???
    var sUrl = this.ws_url;
    var sPost="&operation=valide_formulaire&ID_operation="+this.id_operation+"&param_recherche="+chaine+"&action_panier="+action_panier+"&ID_panier="+ID_panier;
    var this_recherchator = this;
     ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                alert ("OK");
            }
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 70000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
// Valide le formulaire

this.valide_formulaire = function(bool_maj_barres) {
    affiche_waiting(true);
    var tableau=this.formulaire_2_array();
    var chaine=this.formulaire_2_json(tableau);
    //this.last_recherche=chaine; // ???
    var sUrl = this.ws_url;
    var sPost="&operation=valide_formulaire&ID_operation="+this.id_operation+"&param_recherche="+chaine;
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                this_recherchator.affiche_liste(oResults.resultat.notices);
                if (bool_maj_barres == true) { // si recherche directe (par opposé à tri ou format...) on maj les barres
                    this_recherchator.nb_page=oResults.resultat.nb_pages;
                    this_recherchator.nb_notices=oResults.resultat.nb_notices;
                    this_recherchator.affiche_pagination (oResults.resultat.nb_notices, oResults.resultat.nb_pages);
                    this_recherchator.affiche_pagination_notice (oResults.resultat.nb_notices);
                    if (this_recherchator.nb_notices=="1") {
                        this_recherchator.affiche_notice_idx(0);
                    }
                }
                
                if (this_recherchator.ws_url_total != "") {
                    this_recherchator.calcule_total();
                }
                //this_recherchator.maj_total(oResults.resultat.total);
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 0,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// lance requête pour affichage du total

this.calcule_total = function() {
    var tableau=this.formulaire_2_array();
    var chaine=this.formulaire_2_json(tableau);
    //this.last_recherche=chaine; // ???
    var sUrl = this.ws_url_total;
    var sPost="&operation=calcule_total&ID_operation="+this.id_operation+"&param_recherche="+chaine;
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                this_recherchator.maj_total(oResults.resultat.texte);
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
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Valide le formulaire pour la géolocalisation

this.valide_formulaire_geolocalisation = function() {
    var tableau=this.formulaire_2_array();
    var chaine=this.formulaire_2_json(tableau);
    //this.last_recherche=chaine; // ???
    var sUrl = this.ws_url;
    var sPost="&operation=valide_formulaire&ID_operation="+this.id_operation+"&param_recherche="+chaine+"&geolocalisation=1";
    
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
			     affiche_points (oResults.resultat.notices);
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
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// géolocalisation

this.geolocalisation = function () {
    window.open(this.url_geolocalisation);
    
}


///////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche une liste de résultats

this.affiche_liste = function (liste) {
    chaine=liste;
    document.getElementById(this.nom_div_tab_liste).innerHTML=chaine;
    this.tabView.selectTab(this.no_onglet_liste);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche une notice

this.affiche_notice = function (type, ID, idx) {
    affiche_waiting(true);
    var tmp=this.get_format_notice();
    var plugin_formate_notice=this.formulaire_2_json(tmp);
    var sUrl = this.ws_url+"&operation=affiche_notice&ID_operation="+this.id_operation+"&type_objet="+type+"&ID="+ID+"&plugin_formate_notice="+plugin_formate_notice;
    this.idx_notice_en_cours=idx;
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                //alert(oResults.resultat.notice);
                document.getElementById(this_recherchator.nom_div_tab_notice).innerHTML=oResults.resultat.notice;
                document.getElementById(this_recherchator.nom_div_notice_en_cours).innerHTML=this_recherchator.idx_notice_en_cours+1;
                this_recherchator.tabView.selectTab(this_recherchator.no_onglet_notice);
                this_recherchator.ID_notice_en_cours=ID;
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 70000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche une notice par son idx (c'est à dire sa position dans la liste des résultats)

this.affiche_notice_idx = function(idx) {
    affiche_waiting(true);
    this.idx_notice_en_cours=idx;
    var tableau = this.formulaire_2_array();
    tableau["nb_notices_par_page"]=1;
    tableau["page"]=idx+1;
    tableau["plugin_formate_liste"]="";
    tableau["plugin_formate_notice"]=this.get_format_notice();
    var chaine=this.formulaire_2_json(tableau);
    var sUrl = this.ws_url;
    var sPost="&operation=affiche_notice_idx&ID_operation="+this.id_operation+"&param_recherche="+chaine+"&idx="+idx;
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                document.getElementById(this_recherchator.nom_div_tab_notice).innerHTML=oResults.resultat.notices[0];
                document.getElementById(this_recherchator.nom_div_notice_en_cours).innerHTML=this_recherchator.idx_notice_en_cours+1;
                this_recherchator.tabView.selectTab(this_recherchator.no_onglet_notice);
                this_recherchator.get_ID_by_idx(idx); // pour mettre à jour this.ID_notice_en_cours
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 70000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupère l'ID d'une notice à partir de son idx et met à jour this.ID_notice_en_cours

this.get_ID_by_idx = function(idx) {
    //alert ("get_ID_by_idx : "+idx);
    var tableau = this.formulaire_2_array();
    tableau["nb_notices_par_page"]=1;
    tableau["page"]=idx+1;
    tableau["format_resultat"]="liste";
    var chaine=this.formulaire_2_json(tableau);
    var sUrl = this.ws_url;
    var sPost="&operation=affiche_notice_idx&ID_operation="+this.id_operation+"&param_recherche="+chaine+"&idx="+idx;
    //alert (sUrl);
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var ID=oResults.resultat.notices;
                this_recherchator.ID_notice_en_cours=ID;
                
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
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche une notice par son idx (c'est à dire sa position dans la liste des résultats)

this.catalogue_notice = function(idx) {
    if (idx == undefined) {
        idx=this.idx_notice_en_cours;
    }
    var tableau = this.formulaire_2_array();
    tableau["nb_notices_par_page"]=1;
    tableau["page"]=idx+1;
    tableau["plugin_formate_liste"]="";
    tableau["plugin_formate_notice"]=this.plugin_get_id["plugin_formate_notice"];
    var chaine=this.formulaire_2_json(tableau);
    var sUrl = this.ws_url+"&operation=affiche_notice_idx&ID_operation="+this.id_operation+"&param_recherche="+chaine+"&idx="+idx;
    var this_recherchator = this;
  
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var ID=oResults.resultat.notices[0]["id"];
                var type_objet=this_recherchator.type_objet;
                var plugin=this_recherchator.type_2_grille[type_objet]["grille"];
    
                var url="bib.php?module="+plugin+"&ID_notice="+ID;
                window.open(url, "", "");
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



///////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche la pagination de la liste

this.affiche_pagination = function (nb_notices, nb_pages) {
    this.nb_notices=nb_notices;
    this.nb_pages=nb_pages;
    var img_debut=add_skin("IMG/icones/control_start_blue.png");
    var img_precedent=add_skin("IMG/icones/control_rewind_blue.png");
    var img_suivant=add_skin("IMG/icones/control_fastforward_blue.png");
    var img_fin=add_skin("IMG/icones/control_end_blue.png");
    var html="<table id='table_barre_liste'><tr>"; // début du tableau
    html+="<td id='col_nb_notices_liste'><span id='nb_notices_liste'>"+nb_notices+" notices</span></td>"; // nb de notices
    html+="<td id='col_pagination_liste'><table width='100%'><tr>"; // début cellule pagination
        html+="<td><img src='"+img_debut+"' onclick='"+this.nom_js+".go_page(1)'/></td>"; // icone début
        html+="<td><img src='"+img_precedent+"' onclick='"+this.nom_js+".go_page(2)'/></td>"; // icone notice prec
        html+="<td><span id='"+this.nom_div_page_liste+"'>"+this.page+"</span>/<span>"+nb_pages+"</span></td>"; // page
        html+="<td><img src='"+img_suivant+"' onclick='"+this.nom_js+".go_page(3)'/></td>"; // icone notice suiv
        html+="<td><img src='"+img_fin+"' onclick='"+this.nom_js+".go_page(4)'/></td>"; // icone fin
    html+="</tr></table>"; // fin cellule pagination
    html+="<td with='35%'>trier par : <select onChange='"+this.nom_js+".valide_formulaire(false)' id='"+this.nom_div_tri+"'>"; // début tri
    var selected="";
    for (idx_tri in this.liste_tris) {
        var tri=this.liste_tris[idx_tri];
        selected="";
        if (tri.valeur == this.tri_defaut) {
            selected=" selected ";
        }
        html+="<option value='"+tri.valeur+"' "+selected+">"+tri.intitule+"</option>";
    }
    html+="</select></td>"; // fin tri
    
    html+="<td with='30%'>Format : <select onChange='"+this.nom_js+".valide_formulaire(false)' id='"+this.nom_div_format_liste+"'>"; // début Format
    for (idx_format in this.liste_formats_liste) {
        var format=this.liste_formats_liste[idx_format];
        selected="";
        if (idx_format == this.format_liste_defaut) {
            selected=" selected ";
        }
        html+="<option value='"+idx_format+"' "+selected+">"+format.intitule+"</option>";
    }
    html+="</select></td>"; // fin Format
    html+="<td>"+this.html_icones_liste+"</td>";
    html+="</tr></table>"
    document.getElementById(this.nom_div_barre_liste).innerHTML=html;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Affiche la pagination de la notice

this.affiche_pagination_notice = function (nb_notices) {
    var img_debut=add_skin("IMG/icones/control_start_blue.png");
    var img_precedent=add_skin("IMG/icones/control_rewind_blue.png");
    var img_suivant=add_skin("IMG/icones/control_fastforward_blue.png");
    var img_fin=add_skin("IMG/icones/control_end_blue.png");
    var html="<table id='table_barre_notice'><tr>"; // début du tableau
    html+="<td id='col_pagination_notice'><table width='50%'><tr>"; // début cellule pagination
        html+="<td><img src='"+img_debut+"' onclick='"+this.nom_js+".go_notice(1)'/></td>"; // icone début
        html+="<td><img src='"+img_precedent+"' onclick='"+this.nom_js+".go_notice(2)'/></td>"; // icone notice prec
        html+="<td><span id='"+this.nom_div_notice_en_cours+"'>"+this.idx_notice_en_cours+"</span>/<span>"+nb_notices+"</span></td>"; // page
        html+="<td><img src='"+img_suivant+"' onclick='"+this.nom_js+".go_notice(3)'/></td>"; // icone notice suiv
        html+="<td><img src='"+img_fin+"' onclick='"+this.nom_js+".go_notice(4)'/></td>"; // icone fin
    html+="</tr></table>"; // fin cellule pagination
    
    html+="<td with='50%'>Format : <select onChange='"+this.nom_js+".affiche_notice_idx("+this.nom_js+".idx_notice_en_cours)' id='"+this.nom_div_format_notice+"'>"; // début Format
    var selected="";
    for (idx_format in this.liste_formats_notice) {
        var format=this.liste_formats_notice[idx_format];
        selected="";
        if (idx_format == this.format_notice_defaut) {
            selected=" selected ";
        }
        html+="<option value='"+idx_format+"' "+selected+">"+format.intitule+"</option>";
    }
    html+="</select></td>"; // fin Format
    html+="<td>"+this.html_icones_notice+"</td>";
    
    html+="</tr></table>"
    document.getElementById(this.nom_div_barre_notice).innerHTML=html;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Changer de page

this.go_page = function (indicateur) {
    if (indicateur==1) { // début
        this.page=1;
    } else if (indicateur == 2) { // précédent
        if (this.page<=1) {
            return (0);
        }
        this.page--;
    } else if (indicateur == 3) { // suivant
        if (this.page>=this.nb_pages) {
            return (0);
        }
        this.page++;
    } else if (indicateur == 4) { // dernier
        this.page=this.nb_pages;
    }
    this.valide_formulaire(false);
    document.getElementById(this.nom_div_page_liste).innerHTML=this.page;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Changer de notice

this.go_notice = function (indicateur) {
    if (indicateur == 1) {
        this.idx_notice_en_cours=0;
        this.affiche_notice_idx(this.idx_notice_en_cours);
    } else if (indicateur == 2) {
        if (this.idx_notice_en_cours > 0) {
            this.idx_notice_en_cours--;
            this.affiche_notice_idx(this.idx_notice_en_cours);
        }
    } else if (indicateur == 3) {
        if (this.idx_notice_en_cours < this.nb_notices-1) {
            this.idx_notice_en_cours++;
            this.affiche_notice_idx(this.idx_notice_en_cours);
        }
    } else if (indicateur == 4) {
        this.idx_notice_en_cours = this.nb_notices-1;
        this.affiche_notice_idx(this.idx_notice_en_cours);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupérer le format pour affichage de la notice détaillée

this.get_format_notice = function () {
    try {
        var format=document.getElementById(this.nom_div_format_notice).value;
    } catch (e) {
        var format=this.format_notice_defaut;
    }
    var retour=this.liste_formats_notice[format]["plugin_formate_notice"];
    return (retour);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Répond à un appel d'une autre page

this.repond_appel = function(type_obj, ID, idx) {
    if (this.id_appel == 0) {
        this.catalogue_notice(idx);
    } else {
        window.opener.callback_appel (this.id_appel, ID);
        window.close(); // faut-il toujours fermer ???
    }
    
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprime un champ de recherche

this.delete_champ = function(id) {
    this.conteneur_recherche.delete_element(id);
    // todo ?? supprimer de this.liste_champs ?
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Génère une barre d'icones 

this.ouvrir_lien = function(id_element, url) {
    this.liste_appels[id_element]=new Object(); // a voir + tard ce qu'on met dedans
    url=url+"&id_appel="+id_element;
    window.open(url);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// maj le nom d'un panier quand on a ouvert une nouvelle fenêtre
this.set_panier_appel = function (id_appel, panier) {
    this.liste_champs[id_appel].set_valeur(panier);
}

this.maj_total = function (chaine) {
    var div = document.getElementById(this.nom_div_total);
    div.innerHTML=chaine;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// Rebondir à partir de la notice courante (idem menu contextuel)

this.rebondir = function () {
    return (fn_mc("contextmenu", this.type_objet, this.ID_notice_en_cours, this.idx_notice_en_cours));
}
    
} // fin de la classe