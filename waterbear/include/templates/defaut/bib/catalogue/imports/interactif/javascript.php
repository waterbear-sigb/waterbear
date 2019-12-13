<script language="javascript">


var nb_notices=0;
var ID_operation="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ID_operation"]);  ?>";
var filtre="<?PHP print ($_SESSION["operations"][$GLOBALS["affiche_page"]["parametres"]["ID_operation"]]["filtre"]);  ?>";
var ws_path="<?PHP print ($GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]);  ?>?module=<?PHP print ($GLOBALS["affiche_page"]["parametres"]["page_ws"]); ?>/<?PHP print ($_SESSION["operations"][$GLOBALS["affiche_page"]["parametres"]["ID_operation"]]["filtre"]);  ?>";
var interactif="<?PHP print ($_REQUEST["interactif"]);  ?>";
var bool_verif="<?PHP print ($_REQUEST["bool_verif"]);  ?>";


//////////////////////////////////////////////////////////////////////////////////////////////////
// Au démarrage
function init () {
    split_fichier();
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// Récupérer la notice suivante
// Si suivant == 1 => notice suivante. Si 0, la même notice

function get_notice (suivant) {
    var sUrl = ws_path+"&operation=get_notice&suivant="+suivant+"&ID_operation="+ID_operation+"&bool_verif="+bool_verif;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert (oResults['erreur']);
                return("");
            }
         	//alert (oResults['resultat']['notice_fichier']);
            document.getElementById("div_notice_txt_fichier").innerHTML+=oResults['resultat']['notice_fichier'];
            var download_url=oResults['resultat']['url'];
            if (download_url != "" && download_url != null) {
                document.getElementById("div_notice_txt_url").innerHTML+="<a href='"+download_url+"' target='_blank'>"+download_url+"</a><br>\n";
            }
            var no_notice=oResults['resultat']['no_notice'];
            maj_compteur (no_notice, nb_notices);
            if (interactif == "0") {
                get_notice (1);
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
        //timeout: 100000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ compteur de notice

function maj_compteur (notice, total) {
    var modele="<?PHP print (get_intitule("bib/catalogue/imports/interactif", "recap_notices", array()));?>";
    var remplacements=new Array();
    remplacements["notice"]=notice;
    remplacements["total"]=total;
    var a_afficher=get_intitule(modele, remplacements);
    document.getElementById("div_recap").textContent=a_afficher;
    
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Recherche le nombre de notices dans le fichier (en fonction du méta-format)
function split_fichier () {
var sUrl = ws_path+"&operation=split_fichier&ID_operation="+ID_operation;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur", array()));  ?> "+oResults['erreur']);
            }
         	//alert (oResults['resultat']);
            nb_notices=oResults['resultat'];
            maj_compteur (0, nb_notices);
            get_notice(1);
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur_connexion", array()));  ?> "+sUrl);
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        //timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}



</script>