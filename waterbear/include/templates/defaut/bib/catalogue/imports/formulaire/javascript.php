<!--  Pour les onglets -->
<script type="text/javascript" src="js/yui/element/element-min.js"></script>
<script type="text/javascript" src="js/yui/tabview/tabview-min.js"></script> 

<script language="javascript">

var type_objet="<?PHP print ($_SESSION["operations"][$GLOBALS["affiche_page"]["parametres"]["ID_operation"]]["type_objet"]); ?>";
var ws_path="<?PHP print ($GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]);  ?>?module=<?PHP print ($GLOBALS["affiche_page"]["parametres"]["page_ws"]); ?>";
var page_valider="bib.php?module=<?PHP print ($GLOBALS["affiche_page"]["parametres"]["page_valider"]); ?>";
var ID_operation="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ID_operation"]); ?>";

/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Au démarrage
function init () {
    init_onglets(); // initialise les onglets simple/avancé
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Recherche le nombre de notices dans le fichier (en fonction du méta-format)
function split_fichier () {
var sUrl = ws_path+"/"+document.getElementById("filtre").value+"&operation=split_fichier&ID_operation="+ID_operation;
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert ("<?PHP print (get_intitule("erreurs/messages_erreur", "ws_erreur", array()));  ?> "+oResults['erreur']);
            }
         	//alert (oResults['resultat']);
            document.getElementById("nb_notices").value=oResults['resultat'];
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Valide le formulaire simple
function valide_simple () {
    var filtre = document.getElementById("filtre").value;
    var action=page_valider+"/"+filtre;
    var formulaire=document.getElementById("formulaire_simple")
    formulaire.action=action;
    formulaire.submit();
}
</script>