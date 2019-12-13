<!--  Pour les onglets -->

<script type="text/javascript">

var tabview;
var structure_parametres=eval("(<?PHP  print ($structure_parametres); ?>)");
var erreurs="<?PHP  print ($erreurs); ?>";
var liste_onglets = new Object();
var ws_url="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_url"]); ?>";

function init () {
    init_onglets();
    if (erreurs != "") {
        alert (erreurs);
    }
    
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init_onglets() {
	tabView = new YAHOO.widget.TabView();
	 
     for (idx_onglet in structure_parametres) { // pour chaque onglet
        var titre_onglet=structure_parametres[idx_onglet]["titre"];
        var rubriques=structure_parametres[idx_onglet]["rubriques"];
        var html_rubrique="<table width='100%'>";
        for (idx_rubrique in rubriques) { // pour chaque rubrique
            var titre_rubrique=rubriques[idx_rubrique]["titre"];
            var description_rubrique=rubriques[idx_rubrique]["description"];
            var lien_rubrique=rubriques[idx_rubrique]["lien"];
            var valeur_rubrique=rubriques[idx_rubrique]["valeur"];
            var type=rubriques[idx_rubrique]["type"];
            var autres_liens=rubriques[idx_rubrique]["autres_liens"];
            
            // on génère les icones pour accès au registre
            var html_liens="";
            if (autres_liens != undefined) {
                var liens=autres_liens.split("$$$$");
                liens.unshift(lien_rubrique);

            } else {
                liens=new Array(lien_rubrique);
            }
            for (idx_lien in liens) {
                var lien=liens[idx_lien];
                html_liens+="&nbsp;<a href='bib.php?module=admin/registre&acces_direct=Registre/"+lien+"' target='_blank'><img src='IMG/icones/application_form_edit.png' title='"+lien+"'/></a>&nbsp;";
            }
            
            
            var champ="";
            if (type=="select") {
                var liste=rubriques[idx_rubrique]["liste"];
                var selected="";
                for (idx_option in liste) {
                    selected="";
                    var intitule=liste[idx_option]["intitule"];
                    var valeur=liste[idx_option]["valeur"];
                    if (valeur == valeur_rubrique) {
                        selected=" selected='true' ";
                    }
                    champ+="<option "+selected+" value=\""+valeur+"\">"+intitule+"</option>";
                }
                champ="<select style='width:100%' onchange=\"update_clef(this, '"+lien_rubrique+"', '"+autres_liens+"');\">"+champ+"</select>";
                
            } else if (type=="textarea") { 
                champ="<textarea style='width:100%' onchange=\"update_clef(this, '"+lien_rubrique+"', '"+autres_liens+"');\" />"+valeur_rubrique+"</textarea>";
            } else if (type=="vide") {
                champ="";
            } else {
                champ="<input style='width:100%' onchange=\"update_clef(this, '"+lien_rubrique+"', '"+autres_liens+"');\"  value=\""+valeur_rubrique+"\"/>";
            }
            
            
            
            html_rubrique+="<tr><td colspan='2'><b>"+titre_rubrique+"</b> "+html_liens+" <br \> "+description_rubrique+"</td></tr><tr><td width='5%'>&nbsp;</td><td>"+champ+"<br><br></td></tr>";
            //html_rubrique+="<b><a href='bib.php?module=admin/registre&acces_direct=Registre/"+lien_rubrique+"' target='_blank'>"+titre_rubrique+"</a></b><br>"+description_rubrique+"<br><br>valeur : "+valeur_rubrique+"<br><hr /><br>";
        }
        html_rubrique+="</table>"
        liste_onglets[idx_onglet]=new YAHOO.widget.Tab({
            label: titre_onglet,
            content: html_rubrique,
            active: true
        });
        tabView.addTab(liste_onglets[idx_onglet]);
     }
     

    tabView.appendTo('container');
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function update_clef_old (element, clef) {
    var valeur=element.value;
    alert (valeur+" => "+clef);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function update_clef (element, clef, autres_liens) {
    var valeur=encodeURIComponent(element.value);
    var sUrl = ws_url+"&operation=update_clef&valeur="+valeur+"&clef="+clef+"&autres_liens="+autres_liens;

  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] != 1) {
                alert (oResults['erreur']);
                return("");
            } else {
                alert ("OK");
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    
}



</script>