<script language="javascript">

var ws_path="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_url"]);  ?>";

function init () {
    get_liste_paniers();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_liste_paniers() {
    var sUrl = ws_path+"&operation=get_liste_paniers";
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert ("Impossible de recuperer la liste des paniers");
                return("");
            }
            var mel_liste_paniers=document.getElementById("mel_liste_paniers");
            var str_select="";
            for (idx in oResults['resultat']) {
                var infos_panier=oResults['resultat'][idx];
                var description=infos_panier["description"];
                var ID_panier=infos_panier["ID_panier"];
                var option = "<option value='"+ID_panier+"'>"+description+" - "+ID_panier+"</option>";
                str_select+=option;
            }
            mel_liste_paniers.innerHTML=str_select;
            
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function importer_panier () {
    affiche_waiting(true);
    var mel_liste_paniers=document.getElementById("mel_liste_paniers");
    var ID_panier=mel_liste_paniers.options[mel_liste_paniers.selectedIndex].value;
    var nom_panier=mel_liste_paniers.options[mel_liste_paniers.selectedIndex].text;
    nom_panier=encodeURI(nom_panier);
    var sUrl = ws_path+"&operation=importe_panier&ID_panier="+ID_panier+"&nom_panier="+nom_panier;
    document.getElementById("mel_resultat").innerHTML="<?PHP print (get_intitule("bib/catalogue/mel", "l_patientez", array()));  ?><br>";
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            
            var commentaire=oResults["resultat"]["commentaire"];
            document.getElementById("mel_resultat").innerHTML+=commentaire;
            if (oResults['succes'] != 1) {
                alert (oResults['erreur']);
                return("");
            }
            
            
            
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur_connexion", array()));  ?> "+sUrl);
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 1000000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function go_mel_url (mel_page) {
    affiche_waiting(true);
    //var mel_liste_paniers=document.getElementById("mel_liste_paniers");
    //var ID_panier=mel_liste_paniers.options[mel_liste_paniers.selectedIndex].value;
    var sUrl = ws_path+"&operation=get_url&mel_page="+mel_page;
    document.getElementById("mel_resultat").innerHTML="<?PHP print (get_intitule("bib/catalogue/mel", "l_patientez", array()));  ?><br>";
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert ("Impossible d'afficher la page");
                return("");
            }
            
            var url=oResults["resultat"]["url"];
            window.open(url);
            
            
            
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur_connexion", array()));  ?> "+sUrl);
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 1000000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}




</script>