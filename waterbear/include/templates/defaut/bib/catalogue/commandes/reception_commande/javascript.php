<script language="javascript">

var ID_commande="<?PHP print($_REQUEST["ID_commande"]); ?>";
var ws_cab="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_cab"]); ?>";
ws_cab=ws_cab+"ID_commande="+ID_commande+"&";
var ws_url="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_url"]); ?>";
var ID_operation="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ID_operation"]); ?>";
var oDS;
var oAC;

function init () {
    if (ID_commande == "") {
        alert ("Aucune notice selectionnee !!");
    }
    init_autocomplete();
    set_focus_cab ();
    
}

function submit_cab () {
    var input = document.getElementById("input_cab");
    var cab=input.value;
    
    var sUrl = ws_url+"&ID_operation="+ID_operation+"&ID_commande="+ID_commande+"&cab="+cab+"&action=submit_cab";
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var texte = oResults.resultat.texte;
                var message = oResults.resultat.message;
                if (message != "" && message != null) {
                    alert(message);
                }
                maj_texte (texte);
                document.getElementById("input_cab").value="";
                set_focus_cab();
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

function maj_texte (texte) {
    var div_texte=document.getElementById("zone_affichage");
    div_texte.innerHTML=texte;
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




</script>