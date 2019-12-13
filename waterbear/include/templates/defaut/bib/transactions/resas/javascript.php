<!--  Template "javascript" de la page -->

<script language="javascript">

var doc_input;
var doc_conteneur;
var doc_oDS; // objet YUI gérant l'échange de données
var doc_oAC; // Objet YUI autocomplete
var lecteur_input;
var lecteur_conteneur;
var lecteur_oDS; // objet YUI gérant l'échange de données
var lecteur_oAC; // Objet YUI autocomplete

var ID_doc="<?PHP print($_REQUEST["ID_doc"]);  ?>";
var ID_lecteur="<?PHP print($_REQUEST["ID_lecteur"]);  ?>";

var ws_doc="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_doc"]);  ?>"; 
var ws_lecteur="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_lecteur"]);  ?>"; 
var ws_url="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url"]);  ?>"; 



function init() {
    init_champ_doc();
    init_champ_lecteur();
}

function init_champ_doc() {
    champ_doc=document.getElementById("champ_doc");
    conteneur_doc=document.getElementById("conteneur_doc");
    doc_oDS = new YAHOO.util.XHRDataSource(ws_doc);
    doc_oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    doc_oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    doc_oDS.maxCacheEntries = 5;
    doc_oAC = new YAHOO.widget.AutoComplete(champ_doc, conteneur_doc, doc_oDS);
    doc_oAC.queryQuestionMark=false;
    doc_oAC.itemSelectEvent.subscribe(set_id_biblio, "bidon");
    // evenements
    //doc_oAC.textboxKeyEvent.subscribe(cab_keypress);
    //cab_oAC.itemSelectEvent.subscribe(cab_valide_complete);
    //cab_oAC.dataReturnEvent.subscribe(cab_maj_bool);
    
}

function set_id_biblio (sType, aArgs, bidon) {
    var myAC = aArgs[0]; // reference back to the AC instance
    var elLI = aArgs[1]; // reference to the selected LI element
    var oData = aArgs[2]; // object literal of selected item's result data
    var valeur=oData[1];
    var intitule=oData[0];
    
    ID_doc=valeur;

}

function init_champ_lecteur() {
    champ_lecteur=document.getElementById("champ_lecteur");
    conteneur_lecteur=document.getElementById("conteneur_lecteur");
    lecteur_oDS = new YAHOO.util.XHRDataSource(ws_lecteur);
    lecteur_oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    lecteur_oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    lecteur_oDS.maxCacheEntries = 5;
    lecteur_oAC = new YAHOO.widget.AutoComplete(champ_lecteur, conteneur_lecteur, lecteur_oDS);
    lecteur_oAC.queryQuestionMark=false;
    lecteur_oAC.itemSelectEvent.subscribe(set_id_lecteur, "bidon");
    // evenements
    //doc_oAC.textboxKeyEvent.subscribe(cab_keypress);
    //cab_oAC.itemSelectEvent.subscribe(cab_valide_complete);
    //cab_oAC.dataReturnEvent.subscribe(cab_maj_bool);
    
}

function set_id_lecteur (sType, aArgs, bidon) {
    var myAC = aArgs[0]; // reference back to the AC instance
    var elLI = aArgs[1]; // reference to the selected LI element
    var oData = aArgs[2]; // object literal of selected item's result data
    var valeur=oData[1];
    var intitule=oData[0];
    
    ID_lecteur=valeur;

}

function valide_reservation(validation_message) {
    var bib=document.getElementById("bib").value;
    //alert ("reserver le doc "+ID_doc+" pour le lecteur "+ID_lecteur);
    var sUrl = ws_url+"&ID_doc="+ID_doc+"&ID_lecteur="+ID_lecteur+"&bib="+bib+"&validation_message="+validation_message;
    
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var reservable=oResults.resultat.reservable;
                var message=oResults.resultat.message;
                if (reservable == 0) {
                    if (confirm(message)) {
                        valide_reservation("oui");
                    }
                    //alert (message);
                } else {
                    alert ("OK");
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
        timeout: 700000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    
}























</script>
    
<!--  Fin du template "javascript" de la page -->