// modif dans manage_stats.js
// ATTENTION on crée une méthode JSON.stringify car la librairie javacsript JSON que j'utilisais a le même nom que la classe standars JS incluse désormais dans tous les navigateurs.
// Or la librairie amCharts (graphiques) l'utilise. On crée juste un alias mais c'est moche. Il faudarit le faire au niveau de la libriairie JSON ou bien utiliser la nouvelle classe JS'
JSON.stringify = function(chaine) {
    return (this.encode(chaine));
}


function manage_stats () {
this.recherchator;
this.statator;
this.nom_div_tab; // le div qui va recevoir les tableaux de stats
this.nom_div_barre; // le div qui va recevoir les icones
this.tabView; // l'objet gestion des onglets
this.no_onglet_affichage; // le n° d'onglet pour l'affichage dans tabView
this.ws_url;
this.lien_rebond; // lien pour rebondir vers recherche experte
this.id_operation;
this.resultat_structure=new Object();
this.type_chart="serial";
this.last_idx_chart="total"; 
this.last_sens_chart=1;
    
this.infos = new Object();
this.infos_str = "";
    
this.get_infos = function () {
    this.infos["recherchator"]=this.recherchator.formulaire_2_array();
    this.infos["statator"]=this.statator.formulaire_2_array();
    this.infos_str=this.recherchator.formulaire_2_json(this.infos);
    this.recupere_stats();
    //alert (infos_str);
}

this.recupere_stats = function() {
    affiche_waiting(true);
    var sUrl = this.ws_url;
    var sPost="&operation=statistiques&ID_operation="+this.id_operation+"&param="+this.infos_str;
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
                this_recherchator.resultat_structure=oResults.resultat.resultat_structure;
                var tableau = this_recherchator.formate_tableau (oResults.resultat.resultat_structure);
                document.getElementById(this_recherchator.nom_div_tab).innerHTML=tableau;
                this_recherchator.tabView.selectTab(this_recherchator.no_onglet_affichage);
                this_recherchator.click_chart(1, "total");
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

this.formate_tableau = function(resultat_structure) {
    var html="<br/><table class='stat_table'><tr class='stat_tr_titre'><td class='stat_td_titre'> </td>";
    var intitules_col=resultat_structure["intitules_col"];
    var rows=resultat_structure["rows"];
    var totaux_col=resultat_structure["totaux_col"];
    var total=resultat_structure["total"];
    
    // 1) intitulés colonnes
    for (idx_intitule_col in intitules_col) {
        // on raccourcit les intitulés trop longs comme les noms de paniers (a/b/c/d...) en ne gardant que le dernier élément
        var intitule=intitules_col[idx_intitule_col];
        var tmp=String(intitule).split("/");
        intitule=tmp.pop();
        
        html+="<td class='stat_td_titre'>"+intitule+" <img class='icone_stat' title='afficher le graphique' id='icone_stat_col_"+idx_intitule_col+"' src='../IMG/icones/chart_bar_add.png' onClick='manage_stats.click_chart(0,"+idx_intitule_col+")'/></td>";
    }
    html+="<td  class='stat_td_titre'>TOTAL <img class='icone_stat' title='afficher le graphique' id='icone_stat_col_total' src='../IMG/icones/chart_bar_add.png' onClick='manage_stats.click_chart(0, \"total\")'/></td></tr>";
    
    // 2) lignes et cellules
    for (idx_row in rows) { // pour chaque ligne
        // on raccourcit les intitulés trop longs comme les noms de paniers (a/b/c/d...) en ne gardant que le dernier élément
        var row=rows[idx_row];
        var intitule=row["intitule_row"];
        var tmp=String(intitule).split("/");
        intitule=tmp.pop(); 
        html+="<tr  class='stat_tr_ligne'><td  class='stat_td_ligne_intitule'>"+intitule+" <img class='icone_stat' title='afficher le graphique' id='icone_stat_row_"+idx_row+"' src='../IMG/icones/chart_bar_add.png'  onClick='manage_stats.click_chart(1,"+idx_row+")'/></td>";
        for (idx_cell in row["cells"]) { // pour chaque cellule
            html+="<td  class='stat_td_ligne_cell'><a target='_blank' href='"+this.lien_rebond+"&expert="+row["liens"][idx_cell]+"'>"+row["cells"][idx_cell]+"<a></td>";
        }
        html+="<td  class='stat_td_ligne_total'>"+row["total_row"]+"</td></tr>";
    } // fin du pour chaque ligne
    
    // 3) totaux de colonnes
    html+="<tr  class='stat_tr_total'><td class='stat_td_titre'>TOTAL <img class='icone_stat' title='afficher le graphique' id='icone_stat_row_total' src='../IMG/icones/chart_bar_add.png'  onClick='manage_stats.click_chart(1, \"total\")'/></td>";
    for (idx_totaux_col in totaux_col) {
        html+="<td class='stat_td_total'>"+totaux_col[idx_totaux_col]+"</td>";
    }
    html+="<td class='stat_td_total'>"+total+"</td></tr></table><br/>";
    return (html);
}

/**
// si row vaut 1, c'est une stat sur un row sinon stat sur colonne
**/
this.click_chart = function (row, idx) {

this.last_idx_chart=idx;
this.last_sens_chart=row;
var tmp;

if (this.type_chart=="serial") {
    p={"type":"serial","theme":"light","dataProvider":[],"gridAboveGraphs":true,"startDuration":1,"graphs":[{"balloonText":"[[category]]: <b>[[value]]<\/b>","fillAlphas":0.8,"lineAlpha":0.2,"type":"column","valueField":"y"}],"chartCursor":{"categoryBalloonEnabled":false,"cursorAlpha":0,"zoomable":false},"categoryField":"x","categoryAxis":{"gridPosition":"start","gridAlpha":0,"tickPosition":"start","tickLength":10,"labelRotation":0,"labelsEnabled":true},"export":{"enabled":true}};
} else if (this.type_chart=="pie") {
    p={"type":"pie","theme":"light","dataProvider": [],"valueField": "y","titleField": "x","balloon":{"fixedPosition":true},"export": {"enabled": true}};    
}
    if (row==0) {
        for (idx_row in this.resultat_structure["rows"]) {
            var row = this.resultat_structure["rows"][idx_row];
            p["dataProvider"][idx_row]=new Object();
            tmp=String(row["intitule_row"]).split("/");
            p["dataProvider"][idx_row]["x"]=tmp.pop();
            if (idx=="total") {
                p["dataProvider"][idx_row]["y"]=row["total_row"];
            } else {
                p["dataProvider"][idx_row]["y"]=row["cells"][idx];
            }
        }
    } else {
        if (idx=="total") {
            for (idx_cell in this.resultat_structure["totaux_col"]) {
                p["dataProvider"][idx_cell]=new Object();
                tmp=String(this.resultat_structure["intitules_col"][idx_cell]).split("/");
                p["dataProvider"][idx_cell]["x"]=tmp.pop();
                p["dataProvider"][idx_cell]["y"]=this.resultat_structure["totaux_col"][idx_cell];
            }
        } else {
            var row=this.resultat_structure["rows"][idx];
            for (idx_cell in row["cells"]) {
                p["dataProvider"][idx_cell]=new Object();
                tmp=String(this.resultat_structure["intitules_col"][idx_cell]).split("/");
                p["dataProvider"][idx_cell]["x"]=tmp.pop();
                p["dataProvider"][idx_cell]["y"]=row["cells"][idx_cell];
            }
        }
        
    }

    
    
   
    
    
    //var chaine=JSON.encode(p);
    var chart = AmCharts.makeChart( "div_tab_chart_statator",p);
    //alert (chaine);
}

this.chart_change_type = function (type) {
    this.type_chart=type;
    this.click_chart(this.last_sens_chart, this.last_idx_chart);
    
}
    
} // fin de la classe