<script language="javascript">

var ws_url="<?PHP print($GLOBALS["affiche_page"]["parametres"]["ws_url"]); ?>";

var listes_choix=new Array();
var liste_codes=new Array();

<?PHP
$listes=$GLOBALS["affiche_page"]["parametres"]["listes"];
print ("listes_choix = {");
$tmp="";
foreach ($listes as $intitule => $def) {
    $chemin_liste=$def["chemin_liste"];
    if ($tmp !== "") {
        $tmp.=" , ";
    }
    $tmp.=("\"$intitule\":\"$chemin_liste\"");
}
print ("$tmp };\n");

?>

function init () {
    get_liste ("", "", "", "");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_liste (operation, code, valeur, nouv_code) {
    var nom_liste=document.getElementById("nom_liste").value;
    var texte_liste=document.getElementById("nom_liste").options[document.getElementById("nom_liste").selectedIndex].text;
    var nom_liste_codes=listes_choix[texte_liste];
  
    
    var sUrl = ws_url+"&operation="+operation+"&valeur="+valeur+"&code="+code+"&nouv_code="+nouv_code+"&nom_liste="+nom_liste+"&nom_liste_codes="+nom_liste_codes;
    //alert (sUrl);

  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults['succes'] !== 1) {
                alert (oResults['erreur']);
            } 
            var liste=oResults['resultat']['liste'];
            liste_codes=oResults['resultat']['liste_codes'];
            var code_defaut=oResults['resultat']['code_defaut'];
            var decode_defaut=oResults['resultat']['decode_defaut'];
            affiche_liste(liste);
            affiche_defaut(code_defaut, decode_defaut);
            
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
    var html="<table><tr><td>code</td><td>valeur</td><td>action</td></tr>";
    for (idx_liste in liste) {
        var element=liste[idx_liste];
        var code=element["code"];
        var valeur=element["valeur"];
        var alerte=element["alerte"];
        //html+="<tr><td><input onChange='update_code(this, \""+code+"\");' value='"+code+"' /></td><td> <input onChange='update_valeur(this, \""+code+"\");' value='"+valeur+"' /> </td><td><img src='IMG/icones/cross.png'onClick='delete_element(\""+code+"\");'/></td></tr>";
        html+="<tr><td><input onChange='update_code(this, \""+code+"\");' value='"+code+"' /></td><td> "+ affiche_liste_codes(code, valeur) +" </td><td><img src='IMG/icones/cross.png'onClick='delete_element(\""+code+"\");'/></td></tr>";
    }
    //html+="<tr><td><input id='crea_code'/></td><td><input id='crea_valeur'/></td><td><img src='IMG/icones/add.png' onClick='add_element();'/></td></tr>";
    html+="<tr><td><input id='crea_code'/></td><td>"+affiche_liste_codes("", "")+"</td><td><img src='IMG/icones/add.png' onClick='add_element();'/></td></tr>";
    html+="</table>";
    document.getElementById("affiche_liste").innerHTML=html;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_liste_codes(code_995, valeur) {
    var retour="";
    if (code_995 !== "") { // si modification
        retour="<select onChange=\"update_valeur(this, '"+code_995+"');\">";
    } else { // si création
        retour="<select id='crea_valeur'>"; 
       
    }    var bool_selected=0;
    for (var i=0 ; i<liste_codes.length ; i++) {
        var selected="";
        var element=liste_codes[i];
        if (valeur === element["valeur"]) {
            selected="selected";
            bool_selected=1;
        }
        retour+="<option "+selected+"  value=\""+element["valeur"]+"\">"+element["intitule"]+"</option>";
    }
    retour+="</select>";
    if (code_995 !== "") {
        if (valeur === "" || valeur === undefined) {
            retour+="aucun code defini";
        } else if (bool_selected===0) {
            retour+="le code "+valeur+" n'existe pas dans la liste";
        }
    }
    
    return (retour);
        
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_defaut(code_defaut, decode_defaut) {
    var html="<br/><br/>";
    html+="<table><tr><td>decode par defaut</td><td><input onChange=\"update_valeur(this, 'decode_defaut');\" value=\""+decode_defaut+"\"></td></tr>";
    html+="<tr><td>code par defaut</td><td>  "+affiche_liste_codes('code_defaut', code_defaut)+"   </td></tr>";
    html+="</table>";
    document.getElementById("affiche_defaut").innerHTML=html;
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
    if (code==="code_defaut") {
        get_liste ("update_code_defaut", "", valeur, "");
    } else if (code==="decode_defaut") {
        get_liste ("update_decode_defaut", "", valeur, "");
    } else {
        get_liste ("update_valeur", code, valeur, "");
    }
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
    if (code === "" || valeur === "") {
        alert ("Le code et la valeur ne peuvent pas etre vides");
        return ("");
    }
    get_liste ("add_element", code, valeur, "");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function open_liste () {
    var nom_liste=document.getElementById("nom_liste").value;
    var url="bib.php?module=admin/registre&acces_direct=Registre/"+nom_liste;
    window.open(url);
}

































</script>