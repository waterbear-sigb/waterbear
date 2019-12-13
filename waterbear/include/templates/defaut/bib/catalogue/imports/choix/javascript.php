


<script type="text/javascript">
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var interactif_path="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["interactif_path"]);  ?>";
var ws_panier="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_panier"]);  ?>";
var ws_options="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ws_options"]);  ?>";
var champs_formulaire=new Array();
var input;
var conteneur;
var oDS;
var oAC;
// INTITULES
// Formulaire acces
//intitules["l_form_acces_acces_a_modifier"]="<?PHP print (get_intitule("bib/admin/objets", "l_form_acces_acces_a_modifier", array()));  ?>";


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// au démarrage...
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init() {
  	// init champ autocomplete panier
    oDS = new YAHOO.util.XHRDataSource(ws_panier);
    oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    oDS.maxCacheEntries = 5;
    oAC = new YAHOO.widget.AutoComplete("autocomplete_panier", "autocomplete_conteneur", oDS);
    oAC.queryQuestionMark=false;
    get_formulaire();
}

function valide_formulaire () {
    //var type_objet = document.getElementById("type_objet").value;
    //var action="bib.php?module=catalogue/imports/formulaire/"+type_objet;
    var options=formulaire_2_json();
    document.getElementById("import_options").value=options;
    var action=interactif_path;
    var formulaire=document.getElementById("formulaire")
    formulaire.action=action;
    formulaire.submit();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_formulaire() {
    var filtre=get_filtre();
    champs_formulaire=new Array();
    var sUrl = ws_options+"&operation=get_options&filtre="+filtre;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                document.getElementById("div_options").innerHTML="";
                document.getElementById("div_aide").innerHTML="";
                return("");
            }
            var div_options=document.getElementById("div_options");
            var div_aide=document.getElementById("div_aide");
            var str_formulaire="<table width='100%'>";
            var aide_formulaire=oResults['resultat']['aide'];
            for (idx in oResults['resultat']['formulaire']) {
                var infos_formulaire=oResults['resultat']['formulaire'][idx];
                var ID=infos_formulaire["ID"];
                var intitule=infos_formulaire["intitule"];
                var valeur=infos_formulaire["valeur"];
                var aide=infos_formulaire["aide"];
                var type_champ=infos_formulaire["type_champ"];
                var liste_choix=infos_formulaire["liste_choix"];
                champs_formulaire.push(ID);
                if (type_champ=="select") {
                    var champ="<tr><td><a href='#' alt=\""+aide+"\" title=\""+aide+"\" >"+intitule+"</a></td><td><select id='"+ID+"'>";
                    for (idx_choix in liste_choix) {
                        var intitule_choix=liste_choix[idx_choix]["intitule"];
                        var valeur_choix=liste_choix[idx_choix]["valeur"];
                        var selected="";
                        if (valeur_choix == valeur) {
                            selected=" selected='selected' ";
                        }
                        champ+="<option "+selected+" value='"+valeur_choix+"'>"+intitule_choix+"</option>\n";
                    }
                    champ+="</select></td></tr>";
                } else {
                    var champ="<tr><td><a href='#' alt=\""+aide+"\" title=\""+aide+"\" >"+intitule+"</a></td><td><input value=\""+valeur+"\" id='"+ID+"'/></td></tr> \n";
                }
                
                str_formulaire+=champ;
            }
            str_formulaire+="</table>"
            div_options.innerHTML=str_formulaire;
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_filtre () {
    var select=document.getElementById("filtre");
    var selected=select.selectedIndex;
    var filtre=select.options[selected].value;
    return(filtre);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

</script>

