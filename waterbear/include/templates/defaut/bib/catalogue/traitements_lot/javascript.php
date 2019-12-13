<script language="javascript">

var ws_url="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_url"]); ?>";
var ws_panier="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_panier"]); ?>";

var champs_formulaire=new Array();
var oDS;
var oAC;

function init() {
    init_autocomplete();
}

function get_type_obj () {
    var select=document.getElementById("type_obj");
    var selected=select.selectedIndex;
    var type_obj=select.options[selected].value;
    return(type_obj);
}

function get_type_obj_text () {
    var select=document.getElementById("type_obj");
    var selected=select.selectedIndex;
    var type_obj=select.options[selected].text;
    return(type_obj);
}

function get_traitement () {
    var select=document.getElementById("select_liste_traitements");
    var selected=select.selectedIndex;
    var traitement=select.options[selected].value;
    return(traitement);
}

function get_liste_traitements() {
    var type_obj=get_type_obj();
    var type_obj_text=get_type_obj_text();
    var ws_panier2=ws_panier+"type_obj="+type_obj_text+"&";
    oDS.liveData=ws_panier2;
    var sUrl = ws_url+"&operation=get_liste_traitements&type_obj="+type_obj;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert ("Impossible de recuperer la liste des traitements");
                return("");
            }
            var select_liste_traitements=document.getElementById("select_liste_traitements");
            var str_select="";
            for (idx in oResults['resultat']) {
                var infos_traitement=oResults['resultat'][idx];
                var intitule=infos_traitement["intitule"];
                var valeur=infos_traitement["valeur"];
                var option = "<option value='"+valeur+"'>"+intitule+"</option>";
                str_select+=option;
            }
            select_liste_traitements.innerHTML=str_select;
            
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

function get_formulaire() {
    var traitement=get_traitement();
    champs_formulaire=new Array();
    var sUrl = ws_url+"&operation=get_formulaire&traitement="+traitement;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert (oResults['erreur']);
                return("");
            }
            var div_formulaire=document.getElementById("div_formulaire");
            var str_formulaire="<table>";
            var aide_formulaire=oResults['resultat']['aide'];
            for (idx in oResults['resultat']['formulaire']) {
                var infos_formulaire=oResults['resultat']['formulaire'][idx];
                var ID=infos_formulaire["ID"];
                var intitule=infos_formulaire["intitule"];
                var valeur=infos_formulaire["valeur"];
                var aide=infos_formulaire["aide"];
                champs_formulaire.push(ID);
                var champ="<tr><td><a href='#' alt=\""+aide+"\" title=\""+aide+"\" >"+intitule+"</a></td><td><input value=\""+valeur+"\" id='"+ID+"'/></td></tr> \n";
                
                str_formulaire+=champ;
            }
            str_formulaire+="</table>";
            div_formulaire.innerHTML=str_formulaire;
            div_aide.innerHTML=aide_formulaire;
            
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

function formulaire_2_json () {
    var retour=new Object();
    for (idx in champs_formulaire) {
        var intitule=champs_formulaire[idx];
        var champ = document.getElementById(intitule);
        var valeur= champ.value;
        retour[intitule]=valeur;
    }
    var chaine=JSON.encode(retour);
    return (chaine);
}


function valide_formulaire (page) {
    var chaine=formulaire_2_json();
    var type_obj=get_type_obj_text();
    var traitement=get_traitement();
    var panier=get_panier();
    if (panier == "" && page == 1) {
        if (confirm(("ATTENTION : vous n'avez saisi aucun panier. Le traitement s'effectuera sur toute la base. Confirmez-vous ?"))) {
            // on ne fait rien
        }  else {
            alert ("operation annulee");
            return ("");
        }
    }
    var sUrl = ws_url+"&operation=lance_traitement&traitement="+traitement+"&type_obj="+type_obj+"&params="+chaine+"&panier="+panier+"&page="+page;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert (oResults['erreur']);
                return("");
            }
            
            //var resume=oResults['resultat']['resume'];
            var nb_notices=oResults['resultat']['nb_notices'];
            var nb_pages=oResults['resultat']['nb_pages'];
            var erreurs=oResults['resultat']['erreurs'];
            document.getElementById("div_resume").innerHTML=nb_notices+" notices. Page "+page+" / "+nb_pages;
            document.getElementById("div_erreurs").innerHTML+=erreurs;
            if (page < nb_pages) {
                valide_formulaire(page+1);
            } else {
                alert ("FIN");
            }
            
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur_connexion", array()));  ?> "+sUrl);
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 90000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    
}

function init_autocomplete(){

    oDS = new YAHOO.util.XHRDataSource(ws_panier);
    oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    oDS.maxCacheEntries = 5;
    oAC = new YAHOO.widget.AutoComplete("input_panier", "autocomplete_input_panier", oDS);
    oAC.queryQuestionMark=false;
    //oAC.itemSelectEvent.subscribe(set_id_biblio, "bidon");
    
}

function get_panier () {
    var panier=document.getElementById("input_panier").value;
    return (panier);
}


</script>


