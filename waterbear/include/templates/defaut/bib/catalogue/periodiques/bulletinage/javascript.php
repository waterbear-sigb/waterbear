<script language="javascript">


var ws_cab="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_cab"]); ?>";
var ws_url="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_url"]); ?>";
var ws_get_cab_incrementiel="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_get_cab_incrementiel"]); ?>";
var ID_operation="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ID_operation"]); ?>";
var oDS;
var oAC;
var tabView;
var onglet_serie;
var onglet_hs;
var onglet_manquants;
var ID_revue;

function init () {
   
    init_autocomplete();
    set_focus_cab ();
    init_onglets ();
    
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette méthode génère un cab incrémentiel
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_cab_incrementiel() {
    var sUrl=ws_get_cab_incrementiel;
    ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
                affiche_waiting(false);
				alert (oResults.erreur);
			} else {
                var cab = oResults.resultat.cab;
				document.getElementById("input_cab").value=cab;
                submit_cab();
                set_focus_cab();
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert ("Echec lors de la recuperation des donnees");
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 20000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function submit_cab () {
    var active_tab = tabView.get('activeIndex');
    document.getElementById("aide_saisie").innerHTML="<?PHP print($GLOBALS["affiche_page"]["parametres"]["aide_saisie"]); ?>";
    var input = document.getElementById("input_cab");
    var cab=input.value;
    if (active_tab == 1) { // HS
        var no_hs=get_no_hs();
        var date_hs=get_date_hs();
        var id_abo=get_id_abo_hs();
        var sUrl = ws_url+"&ID_operation="+ID_operation+"&cab="+cab+"&action=submit_cab&id_abo="+id_abo+"&bool_hs=true&bool_afficher_tous_abos=true&no_hs="+no_hs+"&date_hs="+date_hs;

    } else { // sinon
        
        var id_abo=get_coche_abo();
        var bool_afficher_tous_abos=get_bool_afficher_tous_abos();
        var sUrl = ws_url+"&ID_operation="+ID_operation+"&cab="+cab+"&action=submit_cab&id_abo="+id_abo+"&bool_hs=false&bool_afficher_tous_abos="+bool_afficher_tous_abos;

    }
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var texte = oResults.resultat.texte;
                var message = oResults.resultat.message;
                var abos = oResults.resultat.abos;
                var fascicules = oResults.resultat.fascicules;
                var exemplaires = oResults.resultat.exemplaires;
                var bool_raz_fascicules = oResults.resultat.bool_raz_fascicules;
                var select_abos = oResults.resultat.select_abos;
                var date_jour = oResults.resultat.date_jour;
                ID_revue=oResults.resultat.ID_revue;
                if (message != "" && message != null) {
                    alert(message);
                    return (false);
                }
                if (bool_raz_fascicules == 1) {
                    maj_texte ("", "zone_fascicules");
                }
                if (fascicules == undefined) {
                    fascicules="";
                }
                if (exemplaires == undefined) {
                    exemplaires="";
                }
                if (select_abos == undefined) {
                    select_abos="";
                }
                if (fascicules != "") {
                    var txt_fascicules="<table class='bulletinage_fascicule_exemplaire'><tr><td><table class='bulletinage_fascicule'>"+fascicules+"</table><table class='bulletinage_exemplaire'>"+exemplaires+"</table></td></tr></table><br/>";
                    add_texte (txt_fascicules, "zone_fascicules");
                }
                
                if (select_abos != "") {
                    var txt_hs="<table><tr><td><b>Abonnement</b></td><td><b>Titre ou num.</b></td><td><b>Date</b></td></tr><tr><td>"+select_abos+"</td><td><input id='no_hs' value='HS'></td><td><input value='"+date_jour+"' id='date_hs'></td></tr></table>";
                    maj_texte (txt_hs, "zone_hs");
                }
                maj_texte (texte, "zone_affichage");
                maj_texte (abos, "zone_abos");
                
                //maj_texte (fascicules, "zone_fascicules");
                //maj_texte (exemplaires, "zone_exemplaires");
                document.getElementById("input_cab").value="";
                set_focus_cab();
                set_coche_abo();
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
        timeout: 700000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    
    
    
    
    return (false);
}

function init_autocomplete(){

    oDS = new YAHOO.util.XHRDataSource(ws_cab);
    oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    oDS.maxCacheEntries = 5;
    oAC = new YAHOO.widget.AutoComplete("input_cab", "autocomplete_input_cab", oDS);
    oAC.queryQuestionMark=false;
    oAC.itemSelectEvent.subscribe(set_id_biblio, "bidon");
    
}

function set_focus_cab () {
    var cab = document.getElementById ("input_cab");
    cab.focus();
}

function maj_texte (texte, zone) {
    if (zone == undefined || zone == "") {
        zone="zone_affichage"
    }
    if (texte == undefined) {
        texte="";
    }
    var div_texte=document.getElementById(zone);
    div_texte.innerHTML=texte;
}

function add_texte (texte, zone) {
    if (zone == undefined || zone == "") {
        zone="zone_affichage"
    }
    if (texte == undefined) {
        texte="";
    }
    var div_texte=document.getElementById(zone);
    div_texte.innerHTML+=texte;
}

function set_id_biblio (sType, aArgs, bidon) {
    var myAC = aArgs[0]; // reference back to the AC instance
    var elLI = aArgs[1]; // reference to the selected LI element
    var oData = aArgs[2]; // object literal of selected item's result data
    var valeur=oData[1];
    var intitule=oData[0];
    document.getElementById("input_cab").value="ID:"+valeur;
    submit_cab();
}

function set_coche_abo () {
    var liste=document.getElementsByName("coche_abo");
    if (liste.length > 0) {
        liste[0].checked=true;
    }
}

function get_coche_abo () {
    var liste=document.getElementsByName("coche_abo");
    for (idx in liste) {
        if (liste[idx].checked == true) {
            var id_abo = liste[idx].getAttribute("id_abo");
            return (id_abo);
        }
    }
    return("");
}

function get_no_hs () {
    var tmp=document.getElementById("no_hs").value;
    return (tmp);
}

function get_date_hs () {
    var tmp=document.getElementById("date_hs").value;
    return (tmp);
}

function get_id_abo_hs () {
    var tmp=document.getElementById("id_abo_hs").value;
    return (tmp);
}

function coche_abo (elem) {
    decoche_tout_abo();
    elem.checked=true;
}


function get_bool_afficher_tous_abos () {
    //var chk=document.getElementById("bool_afficher_tous_abos");
    //return (chk.checked);
    return (false);
}

function decoche_tout_abo() {
  var liste=document.getElementsByName("coche_abo");
    for (idx in liste) {
        liste[idx].checked=false;
    } 
}

function clic_afficher_tous_abos () {
    var bool_afficher_tous_abos=get_bool_afficher_tous_abos ();
    alert (bool_afficher_tous_abos);
}

function init_onglets() {
	tabView = new YAHOO.widget.TabView();
	onglet_serie= new YAHOO.widget.Tab({
            label: "numeros dans une serie",
            content: "<div id='zone_abos'></div>",
            active: true
    });
    tabView.addTab(onglet_serie);
    onglet_hs= new YAHOO.widget.Tab({
            label: "HS ou ancien numero",
            content: "<div id='zone_hs'></div>",
            active: false
    });
    tabView.addTab(onglet_hs);
    /**
    onglet_manquants= new YAHOO.widget.Tab({
            label: "numeros manquants.",
            content: "<div id='zone_manquants'></div>",
            active: false
    });
    tabView.addTab(onglet_manquants);
    **/
    tabView.appendTo('onglets_abos');
}

function lister_fascicules (ID_revue, ID_abo) {
    window.open("bib.php?module=catalogue/recherches/formulaires/biblio/fascicule_acces&id_serie="+ID_revue+"&id_abo="+ID_abo);
}

function afficher_tous_fascicules () {
    lister_fascicules (ID_revue, "");
}

function afficher_fascicules_abo () {
    var ID_abo=get_coche_abo();
    lister_fascicules ("", ID_abo);
}



</script>