

<!--  Template "javascript" de la page -->
<script language="javascript">

var bool_stat="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["bool_stat"]);  ?>";

var recherchator; // l'objet global qui va gérer tout
var panierator; // l'objet global qui va gérer les paniers

var type_2_grille;
var page_geolocalisation;
var html_icones_liste;
var html_icones_notice;
var tabView;

var icones_recherche = eval("(<?PHP print ($icones_recherche);  ?>)");  
var icones_liste = eval("(<?PHP print ($icones_liste);  ?>)"); 
var icones_notice = eval("(<?PHP print ($icones_notice);  ?>)");  
var icones_paniers = eval("(<?PHP print ($icones_paniers);  ?>)");  

var nom_tab_recherche="<?PHP print (get_intitule('bib/catalogue/recherches/formulaires', 'nom_tab_recherche', array()));  ?>"; 
var nom_tab_liste="<?PHP print (get_intitule('bib/catalogue/recherches/formulaires', 'nom_tab_liste', array()));  ?>"; 
var nom_tab_notice="<?PHP print (get_intitule('bib/catalogue/recherches/formulaires', 'nom_tab_notice', array()));  ?>"; 
var nom_tab_paniers="<?PHP print (get_intitule('bib/catalogue/recherches/formulaires', 'nom_tab_paniers', array()));  ?>"; 

var prefixe_recherche="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["prefixe_recherche"]);  ?>";

// SI Stats
if (bool_stat == 1) {
    var statator; // l'objet global qui va gérer les stats
    var manage_stats; // objet qui gère les stats
    var icones_stats = eval("(<?PHP print ($icones_stats);  ?>)");  
    var icones_resultats_stats = eval("(<?PHP print ($icones_resultats_stats);  ?>)");  
    var nom_tab_stats="<?PHP print (get_intitule('bib/catalogue/recherches/formulaires', 'nom_tab_stats', array()));  ?>"; 
    var nom_tab_stats_resultat="<?PHP print (get_intitule('bib/catalogue/recherches/formulaires', 'nom_tab_stats_resultat', array()));  ?>"; 
    var prefixe_stats="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["prefixe_stats"]);  ?>";
} 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init () {

    var erreurs_chargement = "<?PHP print ($erreurs);  ?>";
    if (erreurs_chargement != "") {
        alert (erreurs_chargement);
    }
    
    
    init_onglets();
    
    // RECHERCHE
    recherchator=new <?PHP print ($GLOBALS["affiche_page"]["parametres"]["nom_recherchator"]);  ?>();
    recherchator.init_variable("nom_js", "recherchator");
    recherchator.init_variable("prefixe_div", prefixe_recherche);
    recherchator.tabView=tabView;
    
    recherchator.init_variable("classe_css", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["classe_css"]);  ?>");
    recherchator.init_variable("ws_url", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url"]);  ?>");
    recherchator.init_variable("ws_url_total", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url_total"]);  ?>");
    recherchator.init_variable("id_operation", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["id_operation"]);  ?>");
    recherchator.init_variable("nom_div_total", "total"); // à rendre paramétrable à l'avenir ??
    
    recherchator.init_variable("tri_defaut", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["tri_defaut"]);  ?>");
    recherchator.init_variable("format_liste_defaut", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["format_liste_defaut"]);  ?>");
    recherchator.init_variable("format_notice_defaut", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["format_notice_defaut"]);  ?>");
    recherchator.init_variable("type_objet", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["type_objet"]);  ?>");
    recherchator.init_variable("validation_auto", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["validation_auto"]);  ?>");
    recherchator.init_variable("id_appel", "<?PHP print ($_REQUEST["id_appel"]);  ?>");
    recherchator.init_variable("url_geolocalisation", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_geolocalisation"]);  ?>");
    recherchator.init_variable("url_exporter", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_exporter"]);  ?>");
    recherchator.init_variable("url_telecharger", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_telecharger"]);  ?>");
    recherchator.html_icones_liste=html_icones_liste;
    recherchator.html_icones_notice=html_icones_notice;

    recherchator.init_variable_json("liste_criteres_ajout", "<?PHP print ($liste_criteres_ajout);  ?>");
    recherchator.init_variable_json("formulaire_defaut", "<?PHP print ($formulaire_defaut);  ?>");
    recherchator.init_variable_json("liste_tris", "<?PHP print ($liste_tris);  ?>");
    recherchator.init_variable_json("liste_formats_liste", "<?PHP print ($liste_formats_liste);  ?>");
    recherchator.init_variable_json("liste_formats_notice", "<?PHP print ($liste_formats_notice);  ?>");
    recherchator.init_variable_json("plugin_get_id", "<?PHP print ($plugin_get_id);  ?>");
    recherchator.init_variable_json("type_2_grille", "<?PHP print ($type_2_grille);  ?>");
    
    recherchator.init_formulaire();
    
    // PANIERS
    panierator=new <?PHP print ($GLOBALS["affiche_page"]["parametres"]["nom_panierator"]);  ?>();
    panierator.init_variable("type_objet", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["type_objet"]);  ?>");
    panierator.init_variable("ws_url", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["panierator_ws_url"]);  ?>");
    panierator.init_variable("id_appel", "<?PHP print ($_REQUEST["id_appel"]);  ?>");
    panierator.init_variable("l_retour", "<?PHP print (get_intitule('bib_ws/catalogue/paniers', 'retour', array()));  ?>");
    panierator.init_variable("l_selectionnez_panier", "<?PHP print (get_intitule('bib_ws/catalogue/paniers', 'selectionnez_panier', array()));  ?>");
    panierator.init_variable("l_pas_repertoire", "<?PHP print (get_intitule('bib_ws/catalogue/paniers', 'pas_repertoire', array()));  ?>");
    panierator.init_variable("l_impossible_non_statique", "<?PHP print (get_intitule('bib_ws/catalogue/paniers', 'impossible_non_statique', array()));  ?>");
    panierator.init_variable("l_selectionnez_notice", "<?PHP print (get_intitule('bib_ws/catalogue/paniers', 'selectionnez_notice', array()));  ?>");
    panierator.init_variable("l_confirm_delete", "<?PHP print (get_intitule('bib_ws/catalogue/paniers', 'confirm_delete', array()));  ?>");
    
    panierator.get_liste();
    
    // On attribue un n° d'onglet aux différents objets
    recherchator.no_onglet_liste=1;
    recherchator.no_onglet_notice=2;
    
    // STATS
    if (bool_stat == 1) {
        // ces critères sont spécifiques à statator
        statator=new <?PHP print ($GLOBALS["affiche_page"]["parametres"]["nom_recherchator"]);  ?>();
        statator.init_variable("nom_js", "statator");
        statator.init_variable("prefixe_div", prefixe_stats);
        statator.init_variable("format_liste_defaut", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["format_liste_defaut_stats"]);  ?>");
        statator.init_variable_json("liste_criteres_ajout", "<?PHP print ($liste_criteres_ajout_stats);  ?>");
        statator.init_variable_json("formulaire_defaut", "<?PHP print ($formulaire_defaut_stats);  ?>");
        statator.init_variable_json("liste_formats_liste", "<?PHP print ($liste_formats_liste_stats);  ?>");
        
        // ces critères sont les mêmes que recherchator  
        statator.tabView=tabView;
        statator.init_variable("classe_css", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["classe_css"]);  ?>");
        statator.init_variable("ws_url", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url"]);  ?>");
        statator.init_variable("id_operation", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["id_operation"]);  ?>");
        statator.init_variable("tri_defaut", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["tri_defaut"]);  ?>");
        statator.init_variable("format_notice_defaut", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["format_notice_defaut"]);  ?>");
        statator.init_variable("type_objet", "<?PHP print ($GLOBALS["affiche_page"]["parametres"]["type_objet"]);  ?>");
        statator.html_icones_liste=html_icones_liste;
        statator.init_variable_json("liste_tris", "<?PHP print ($liste_tris);  ?>");
        statator.init_variable_json("liste_formats_notice", "<?PHP print ($liste_formats_notice);  ?>");
        statator.init_variable_json("plugin_get_id", "<?PHP print ($plugin_get_id);  ?>");
        statator.init_variable_json("type_2_grille", "<?PHP print ($type_2_grille);  ?>");
    
        statator.init_formulaire();
        
        // MANAGE STATS
        manage_stats=new manage_stats();
        manage_stats.recherchator=recherchator;
        manage_stats.statator=statator;
        manage_stats.tabView=tabView;
        manage_stats.ws_url="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url"]);  ?>";
        manage_stats.lien_rebond="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["lien_rebond"]);  ?>";
        manage_stats.id_operation="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["id_operation"]);  ?>";
        manage_stats.nom_div_tab="div_tab_liste_"+prefixe_stats;
        
        // On attribue un n° d'onglet aux différents objets
        statator.no_onglet_liste=5;
        manage_stats.no_onglet_affichage=2;
    }
 
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_notice (obj, num, idx) {
    recherchator.affiche_notice(obj, num, idx);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

function rebondir (obj, num, idx) {
    recherchator.rebondir();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

function catalogue_notice () {
    recherchator.catalogue_notice();
}

function mc_voir (a, b, param) {
    var type_obj=param.type_obj;
    var ID=param.ID;
    var idx=param.idx;
    recherchator.affiche_notice(type_obj, ID, idx);
}

function mc_cataloguer (a, b, param) {
    var type_obj=param.type_obj;
    var ID=param.ID;
    var grille=recherchator.type_2_grille[type_obj]["grille"];
    var url="bib.php?module="+grille+"&ID_notice="+ID;
    window.open(url);
}

function mc_selectionner (a, b, param) {
    var type_obj=param.type_obj;
    var ID=param.ID;
    var idx=param.idx;
    recherchator.repond_appel(type_obj, ID, idx);
}


function affiche_panier_en_cours () {
    //var ID_panier=panierator.ID_panier_en_cours;
    var ID_panier=panierator.ID_panier_modif;
    var sUrl = recherchator.ws_url+"&operation=affiche_panier&ID_panier="+ID_panier;
        //var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                //alert (oResponse.responseText);
                if (statator != undefined && oResults["resultat"]["formulaire_stat"] != undefined) {
                    statator.formulaire_defaut=oResults["resultat"]["formulaire_stat"];
                    statator.empty_formulaire();
                    statator.init_formulaire_defaut();
                }
                recherchator.formulaire_defaut=oResults["resultat"]["formulaire"];
                recherchator.empty_formulaire();
                recherchator.init_formulaire_defaut();
                
                if (statator != undefined && oResults["resultat"]["formulaire_stat"] != undefined) {
                    manage_stats.get_infos();
                } else {
                    recherchator.valide_formulaire(true);
                }
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// géolocalisation : retourne une liste de coordonnées
function get_points (page) {
    page_geolocalisation=page;
    recherchator.valide_formulaire_geolocalisation();
}

function affiche_points (points) {
    page_geolocalisation.affiche_points (points);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init_onglets () {
    // 0) On génère le HTML pour les icones des onglets :
    var html_icones_recherche=genere_icones(icones_recherche);
    html_icones_liste=genere_icones(icones_liste); // variable globale car doit être passé à recherchator
    html_icones_notice=genere_icones(icones_notice);
    var html_icones_paniers=genere_icones(icones_paniers);
    var html_lancer_la_recherche="";
    if (bool_stat != 1) {
        html_lancer_la_recherche="<input type='submit' value='Lancer la recherche'/>";
    }
      
    // 1) On créee les onglets
    tabView = new YAHOO.widget.TabView();
    var tab_recherche=new YAHOO.widget.Tab({
        label: nom_tab_recherche,
        content: "<div id='div_barre_recherche_"+prefixe_recherche+"'> "+html_icones_recherche+" </div><form onsubmit='return valide_recherche()' action='bidon.php'><div id='div_tab_recherche_"+prefixe_recherche+"'></div><br/>"+html_lancer_la_recherche+"</form>"
    });
    tabView.addTab(tab_recherche);
    
    if (bool_stat == 1) {
        var html_icones_stats=genere_icones(icones_stats);
        var html_icones_resultats_stats=genere_icones(icones_resultats_stats);
        var tab_stats=new YAHOO.widget.Tab({
            label: nom_tab_stats,
            content: "<div id='div_barre_recherche_"+prefixe_stats+"'> "+html_icones_stats+" </div><form onsubmit='return valide_recherche_stats()' action='bidon.php'><div id='div_tab_recherche_"+prefixe_stats+"'></div><br/><input type='submit' value='Afficher les statistiques'/></form>"
        });
        tabView.addTab(tab_stats);
        
        var tab_stats_resultat=new YAHOO.widget.Tab({
            label: nom_tab_stats_resultat,
            content: "<div id='div_barre_liste_"+prefixe_stats+"'> "+html_icones_resultats_stats+" </div><div id='div_tab_liste_"+prefixe_stats+"'></div><br /><div id='div_tab_icones_chart_"+prefixe_stats+"'><table><tr><td><img src='<?PHP print(add_skin ("IMG/icones/chart_bar.png")); ?>' onClick='manage_stats.chart_change_type(\"serial\")'/></td><td>&nbsp;</td><td><img src='<?PHP print(add_skin ("IMG/icones/chart_pie_title.png")); ?>' onClick='manage_stats.chart_change_type(\"pie\")'/></td></tr></table></div><br /><div id='div_tab_chart_"+prefixe_stats+"'></div><br />"
        });
        tabView.addTab(tab_stats_resultat);    
    } else {

    
        var tab_liste=new YAHOO.widget.Tab({
            label: nom_tab_liste,
            content: "<div id='div_barre_liste_"+prefixe_recherche+"'> </div><div id='div_tab_liste_"+prefixe_recherche+"'></div>"
        });
        tabView.addTab(tab_liste);
        
        var tab_notice=new YAHOO.widget.Tab({
            label: nom_tab_notice,
            content: "<div id='div_barre_notice_"+prefixe_recherche+"'> </div><div id='div_tab_notice_"+prefixe_recherche+"'></div>"
        });
        tabView.addTab(tab_notice);
    }
    
    var tab_paniers=new YAHOO.widget.Tab({
        label: nom_tab_paniers,
        content: "<div id='div_barre_paniers'> <table><tr><td>"+html_icones_paniers+" </td><td><input id='input_panier_en_cours' readonly='readonly'/></td></tr></table></div><div id='div_tab_paniers'><table><tr><td> <div id='div_navigation_paniers'> navigation dans les paniers</div> </td><td> <div id='div_formulaire_paniers'><br> Emplacement : <br> <input id='input_chemin_parent' readonly='readonly'> <br><br> Nom : <br> <input id='input_nom_panier'>  <br><br> Description : <br><textarea id='textarea_description_panier'></textarea></div> </td></tr></table></div>"
    });
    tabView.addTab(tab_paniers);
    
    /**
    if (bool_stat == 1) {
        var html_icones_stats=genere_icones(icones_stats);
        var html_icones_resultats_stats=genere_icones(icones_resultats_stats);
        var tab_stats=new YAHOO.widget.Tab({
            label: nom_tab_stats,
            content: "<div id='div_barre_recherche_"+prefixe_stats+"'> "+html_icones_stats+" </div><form onsubmit='return valide_recherche_stats()' action='bidon.php'><div id='div_tab_recherche_"+prefixe_stats+"'></div><br/><input type='submit' value='Afficher les statistiques'/></form>"
        });
        tabView.addTab(tab_stats);
        
        var tab_stats_resultat=new YAHOO.widget.Tab({
            label: nom_tab_stats_resultat,
            content: "<div id='div_barre_liste_"+prefixe_stats+"'> "+html_icones_resultats_stats+" </div><div id='div_tab_liste_"+prefixe_stats+"'></div>"
        });
        tabView.addTab(tab_stats_resultat);    
    }
    **/


    
    tabView.appendTo(document.getElementById("container"));
    tabView.set('activeIndex', 0);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

function genere_icones (icones_recherche) {
    try {
        var html_icones_recherche="<table style='height:100%;'><tr>"
        for (idx in icones_recherche) {
            var url=icones_recherche[idx]["url"];
            var src=icones_recherche[idx]["src"];
            var alt=icones_recherche[idx]["alt"];
            src=add_skin(src);
            html_icones_recherche+="<td class='recherchator_icone'><a href='#' class='recherchator_icone' title='"+alt+"' alt='"+alt+"'><img src='"+src+"' class='recherchator_icone'   onClick=\""+url+"\"/></a></td>";
        }
        html_icones_recherche+="</tr></table>"
        return (html_icones_recherche);
    } catch (e) {
        return ("");
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!! SURCHARGE la fonction de base dans tmpl_js_div car rajoute la maj du total
function catalogage_rapide (obj) {
    var valeur=obj.value;
    var ID_notice=obj.getAttribute("ID_notice");
    var type_obj=obj.getAttribute("type_obj");
    var ws_url_traitement=obj.getAttribute("ws_url");
    var sUrl = ws_url_traitement+"&ID_notice="+ID_notice+"&valeur="+valeur+"&type_obj="+type_obj;
        //var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                // maj total (si présent)
                if (recherchator.ws_url_total != "") {
                    recherchator.calcule_total();
                }
                
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur lors de la connexion");
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

function set_panier_appel (id_appel, valeur) {
    recherchator.set_panier_appel (id_appel, valeur);
}

function valide_recherche () {
    recherchator.valide_formulaire(true)
    return(false);
}

function valide_recherche_stats () {
    manage_stats.get_infos();
    return(false);
}

</script>

<!--  Fin du template "javascript" de la page -->