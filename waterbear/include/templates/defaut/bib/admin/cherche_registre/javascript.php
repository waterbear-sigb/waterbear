<script language='javascript'>

var ws_url="<?PHP print ($GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]);  ?>?module=admin/registre";

function init() {
    var nom_noeud=document.getElementById("nom_noeud").value;
    var valeur_noeud=document.getElementById("valeur_noeud").value;
    if (nom_noeud != "" || valeur_noeud != "") {
        valide_form();
    }
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function valide_form () {
    var nom_noeud=document.getElementById("nom_noeud").value;
    var valeur_noeud=document.getElementById("valeur_noeud").value;
    
    var sUrl = ws_url+"&operation=recherche_noeud&nom="+nom_noeud+"&valeur="+valeur_noeud;
	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
        	if (oResults.succes == 0) {
			    alert (oResults.erreur);  
            } else {
                affiche_resultat(oResults.resultat);
            }
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("<?PHP print (get_intitule("bib/admin/registre", "erreur_connexion", array()));  ?>");
            oResponse.argument.fnLoadComplete();
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

function affiche_resultat (liste) {
    var html="<table border='1px'><tr><td><?PHP print (get_intitule("", "bib/admin/cherche_registre/l_col_idx", array())); ?></td>";
    html+="<td><?PHP print (get_intitule("", "bib/admin/cherche_registre/l_col_chemin", array())); ?></td>";
    html+="<td><?PHP print (get_intitule("", "bib/admin/cherche_registre/l_col_icone", array())); ?></td></tr>";
    var div=document.getElementById("div_table");
    for (idx in liste) {
        var idx2=idx;
        idx2++;
        var ligne=liste[idx];
        var chemin=ligne["chemin"];
        var url="bib.php?module=admin/registre&acces_direct=Registre/"+chemin;
        var str_image="<img src='IMG/icones/application_double.png'/>";
        html+="<tr><td>"+idx2+"</td><td><a href='"+url+"'>"+chemin+"</a></td><td><a href='"+url+"' target='_blank'>"+str_image+"</a></td></tr>";
    }
    html+="</table>"
    div.innerHTML=html;
}


</script>