
<script language="javascript">

var ws_panier="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_panier"]);  ?>";
var input_panier;
var conteneur_panier;
var oDS_panier;
var oAC_panier;

var ws_commande="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_commande"]);  ?>";
var input_commande;
var conteneur_commande;
var oDS_commande;
var oAC_commande;

var url_recherche_panier="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_recherche_panier"]);  ?>";
var url_recherche_commande="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_recherche_commande"]);  ?>";
var url_crea_commande="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_crea_commande"]);  ?>";
var ws_url="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url"]);  ?>";

function init () {
    init_autocomplete();
}

function init_autocomplete(){
    // init champ autocomplete panier
    oDS_panier = new YAHOO.util.XHRDataSource(ws_panier);
    oDS_panier.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    oDS_panier.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    oDS_panier.maxCacheEntries = 5;
    oAC_panier = new YAHOO.widget.AutoComplete("autocomplete_panier", "autocomplete_conteneur_panier", oDS_panier);
    oAC_panier.queryQuestionMark=false;
    
    // init champ autocomplete commande
    oDS_commande = new YAHOO.util.XHRDataSource(ws_commande);
    oDS_commande.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    oDS_commande.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    oDS_commande.maxCacheEntries = 5;
    oAC_commande = new YAHOO.widget.AutoComplete("autocomplete_commande", "autocomplete_conteneur_commande", oDS_commande);
    oAC_commande.queryQuestionMark=false;
    oAC_commande.itemSelectEvent.subscribe(set_id_commande, "bidon");
}

function set_panier_appel (id_appel, valeur) {
    if (id_appel == 1) {
        document.getElementById("autocomplete_panier").value=valeur;
    } 
}

function open_recherche_panier () {
    window.open(url_recherche_panier+"&id_appel=1");
}

function open_recherche_commande () {
    window.open(url_recherche_commande+"&id_appel=2");
}

function open_crea_commande () {
    window.open(url_crea_commande+"&id_appel=3");
}

function callback_appel (id_appel, ID_notice) {
    if (id_appel == 2) {
        document.getElementById("autocomplete_commande").value=ID_notice;
    } else if (id_appel == 3) {
        document.getElementById("autocomplete_commande").value=ID_notice;
    }
}

function set_id_commande (sType, aArgs, bidon) {
    var myAC = aArgs[0]; // reference back to the AC instance
    var elLI = aArgs[1]; // reference to the selected LI element
    var oData = aArgs[2]; // object literal of selected item's result data
    var valeur=oData[1];
    var intitule=oData[0];
    document.getElementById("autocomplete_commande").value=valeur;
}

function valide_commande () {
    var nom_panier=document.getElementById("autocomplete_panier").value;
    var ID_commande=document.getElementById("autocomplete_commande").value;
    var sUrl = ws_url+"&nom_panier="+nom_panier+"&ID_commande="+ID_commande;
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                alert("OK");
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

}
    
    
</script>
    