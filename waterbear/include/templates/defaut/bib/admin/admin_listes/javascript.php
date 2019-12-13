<script language="javascript">

var ws_url="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_url"]); ?>";


function init () {
    get_liste ("", "", "", "")
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_liste (operation, code, valeur, nouv_code) {
    var nom_liste=document.getElementById("nom_liste").value;
    var langue=document.getElementById("langue").value;
    
    
    var sUrl = ws_url+"&operation="+operation+"&valeur="+valeur+"&code="+code+"&nouv_code="+nouv_code+"&nom_liste="+nom_liste+"&langue="+langue;
    //alert (sUrl);

  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert (oResults['erreur']);
            } 
            var liste=oResults['resultat']['liste'];
            affiche_liste(liste);
            
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur_connexion", array()));  ?> "+sUrl);
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_liste (liste) {
    var html="<table><tr><td>code</td><td>valeur</td><td>action</td></tr>"
    for (idx_liste in liste) {
        var element=liste[idx_liste];
        var code=element["code"];
        var valeur=element["valeur"];
        html+="<tr><td><input onChange='update_code(this, \""+code+"\");' value='"+code+"' /></td><td> <input onChange='update_valeur(this, \""+code+"\");' value='"+valeur+"' /> </td><td><img src='IMG/icones/cross.png'onClick='delete_element(\""+code+"\");'/></td></tr>";
    }
    html+="<tr><td><input id='crea_code'/></td><td><input id='crea_valeur'/></td><td><img src='IMG/icones/add.png' onClick='add_element();'/></td></tr>";
    html+="</table>";
    document.getElementById("affiche_liste").innerHTML=html;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function update_code (select, code) {
    var nouv_code=select.value;
    get_liste ("update_code", code, "", nouv_code);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function update_valeur (select, code) {
    var valeur=select.value;
    get_liste ("update_valeur", code, valeur, "");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function delete_element (code) {
    get_liste ("delete_element", code, "", "");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function add_element () {
    var code=document.getElementById("crea_code").value;
    var valeur=document.getElementById("crea_valeur").value;
    if (code === "") {
        alert ("Le code ne peut pas etre vide");
        return ("");
    }
    get_liste ("add_element", code, valeur, "");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function open_liste () {
    var nom_liste=document.getElementById("nom_liste").value;
    var url="bib.php?module=admin/registre&acces_direct=Registre/"+nom_liste+"/_intitules";
    window.open(url);
}

































</script>