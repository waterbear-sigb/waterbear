<!--  Template "javascript" de la page -->

<script language="javascript">



// variables pour le champ cab
var cab_input;
var cab_conteneur;
var cab_oDS; // objet YUI gérant l'échange de données
var cab_oAC; // Objet YUI autocomplete

var obj_message;

var cab_ws="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["cab_ws"]);  ?>"; // WS pour l'autocomplete
var transaction_ws="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["transaction_ws"]);  ?>"; // WS quand on valide un cab
var prolongation_ws="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["prolongation_ws"]);  ?>"; // WS pour prolonger un ou tous les prêts
var delete_resa_ws="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["delete_resa_ws"]);  ?>"; // WS pour supprimer une résa

var id_operation = ("<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ID_operation"]); ?>");
var type_2_grille=eval("(<?PHP print ("$type_2_grille");   ?>)"); // JSON

var cab_lecteur="<?PHP print($_REQUEST['cab_lecteur']); ?>";

var bool_autocomplete=0; // si 1, on est en train d'utiliser l'autocomplétion
var last_fonction="";
var bool_validation=0;


// variables pour le resultat
var tabView; // l'objet onglets
var tmp="<?PHP print ($def_onglets);  ?>";
var def_onglets=eval("(" + tmp + ")");
var liste_onglets=new Object(); // [prets | resas | ...][idx|objet_js]

var last_cab_lecteur;
var last_ID_lecteur;

// si cab_lecteur fourni, on se positionne directement sur le lecteur



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cab_keypress (sType, aArgs) {
    var car = aArgs[1]; // code ascii du caractère tapé
    //alert (car);
    //return("");
    if (car == 33) {
        set_mode ("pret");
    } else if (car == 34) {
        set_mode ("retour");
    } 
}

function set_mode (mode) {
    if ((mode == "pret" || mode == "resa") && last_cab_lecteur != "" && last_cab_lecteur != undefined) {
        var param="{\"clefs\" : {\"mode\" : \""+mode+"\", \"cab\" : \""+last_cab_lecteur+"\"}, \"reload\" : \"1\"}";
        set_couleur_cab("pret");
    } else {
        var param="{\"clefs\" : {\"mode\" : \""+mode+"\"}, \"reload\" : \"1\"}";
        set_couleur_cab("retour");
    }
    cab_action (param);
//alert ("active_onglet : "+mode);
    active_onglet (mode);
    set_focus_cab();
    
    
}

function set_couleur_cab (mode) {
//alert ("set_couleur_cab : "+mode);
    var cab = document.getElementById ("cab_input");
    var couleur="#88de25";
    if (mode == "retour") {
        couleur="#f85353";
    }
    cab.style.backgroundColor=couleur;
}

function set_focus_cab () {
    var cab = document.getElementById ("cab_input");
    cab.focus();
}

function active_onglet (nom) {
    if (nom=="pret") {
        set_couleur_cab("pret");
    } else if (nom == "retour") {
        set_couleur_cab("retour");
    } else if (nom == "resa") {
        set_couleur_cab("pret");
    }
    var idx=liste_onglets[nom]["idx"];
    tabView.set('activeIndex', idx);
}

function set_last_lecteur (parametres) {
    last_cab_lecteur=parametres["cab"];
    last_ID_lecteur=parametres["ID"];
    //alert ("set_last_lecteur : "+last_cab_lecteur+" - "+last_ID_lecteur);
}

// ouvre un lien quelconque en fournissant à la fin l'id du lecteur
function ouvrir_lien_lecteur (lien) {
    var url=lien+last_ID_lecteur;
    window.open(url);
}

function ouvrir_lecteur () {
    var url="bib.php?module=catalogue/catalogage/grilles/lecteur/unimarc_standard_redirect&ID_notice="+last_ID_lecteur;
    window.open(url);    
}

function lister_prets () {
    var url="bib.php?module=catalogue/recherches/formulaires/pret/acces&ID_lecteur="+last_ID_lecteur;
    window.open(url);    
}

function lister_abos () {
    var url="bib.php?module=catalogue/recherches/formulaires/abonnement/acces&ID_lecteur="+last_ID_lecteur;
    window.open(url);    
}

function add_resa () {
    var url="bib.php?module=transactions/resas/standard&ID_lecteur="+last_ID_lecteur;
    window.open(url);    
}

function lister_paiements () {
    var url="bib.php?module=catalogue/recherches/formulaires/paiement/acces&ID_lecteur="+last_ID_lecteur;
    window.open(url);    
}

function event_onglet_change (e) {
    var ancien=e.prevValue;
    var nouveau=e.newValue;
    //alert ("onglet change : "+ancien+" => "+nouveau);
    //alert (print_r(e, 0, "nl"));
    alert (e.target.innerHTML);
    if (ancien == nouveau) {
        return (1);
    }
    if (nouveau == 0) { // PRET
        set_mode ("pret");
    } else if (nouveau == 1) { // RETOUR
        set_mode ("retour");
    } else if (nouveau == 2) {  // RESA
        set_mode ("resa");
    }
    
    //alert ("onglet change : "+ancien+" => "+nouveau);
    //alert (print_r(e, 0, "nl"));
}

function clique_pret () {
    set_mode("pret");
    set_couleur_cab("pret");
}

function clique_retour () {
    set_mode("retour");
    set_couleur_cab("retour");
}

function clique_resa () {
    set_mode("resa");
    set_couleur_cab("pret");
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cab_valide() {
    if (bool_autocomplete == 1) {
        return (false);
    }
    var cab=cab_input.value;
    cab_input.value="";
    try {
    var param="{\"clefs\" : {\"cab\" : \""+cab+"\"}}";
    cab_action (param);
    } catch (e) {
        return (false);
    }
    return (false); // pour éviter que le formulaire envoie VRAIMENT qqchse
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cab_valide_complete(sType, aArgs) {
    var oData = aArgs[2]; // object literal of selected item's result data
    var cab=oData[1];
    cab_input.value="";
    try {
        var param="{\"clefs\" : {\"cab\" : \""+cab+"\"}}";
        cab_action (param);
    } catch (e) {
        return (false);
    }
    bool_autocomplete=0;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function cab_action (param) {
        //alert (param);
        var parametres = eval("(" + param + ")");
        var reload=parametres["reload"];
        var sUrl = transaction_ws+"&ID_operation="+id_operation;
        for (idx in parametres["clefs"]) {
            var clef=parametres["clefs"][idx];
            sUrl+="&"+idx+"="+clef;
        }
        //alert (sUrl);
        if (bool_validation != 0) {
            sUrl+="&validation_message="+bool_validation;
            bool_validation=0;
        }
        last_fonction="cab_action('"+param+"');";
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
				var commandes=oResults.resultat["commandes"];
                var messages=oResults.resultat["messages"];
                var arbre=oResults.resultat["arbre"];
              
                // Commandes
                for (ID_commande in commandes) {
                    var commande=commandes[ID_commande];
                    var objet=commande["objet"];
                    var methode=commande["methode"];
                    var parametres=commande["parametres"];
                    var str_eval="";
                    if (objet != "" && objet != undefined) {
                        str_eval=objet+"."+methode+"(parametres)";
                    } else {
                        str_eval=methode+"(parametres)";
                    }
                    eval (str_eval);
                }
          
                // Messages
                if (reload == "1") {
                    // on ne fait rien
                } else {
                    affiche_messages(messages);
                }
               
                // Arbre
                affiche_arbre (arbre);
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 99000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    affiche_waiting(true);
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
 }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function prolonger_pret (id_pret) {
    if (id_pret=="" || id_pret == undefined || id_pret == null) {
        var sUrl = prolongation_ws+"&ID_lecteur="+last_ID_lecteur;
    } else {
        var sUrl = prolongation_ws+"&ID_pret="+id_pret;
    }
    if (bool_validation == "oui") {
        sUrl+="&bool_force=1";
        bool_validation=0;
    }
    if (bool_validation == "non") {
        bool_validation=0;
        return("");
    }
    last_fonction="prolonger_pret("+id_pret+");";
    ///////////////// !!! CALLBACK !!!
    var callback = {
    success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
				var commandes=oResults.resultat["commandes"];
                var messages=oResults.resultat["messages"];
              
                // Commandes
                for (ID_commande in commandes) {
                    var commande=commandes[ID_commande];
                    var objet=commande["objet"];
                    var methode=commande["methode"];
                    var parametres=commande["parametres"];
                    var str_eval="";
                    if (objet != "" && objet != undefined) {
                        str_eval=objet+"."+methode+"(parametres)";
                    } else {
                        str_eval=methode+"(parametres)";
                    }
                    eval (str_eval);
                }
                affiche_messages(messages);
                
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 30000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function delete_resa (id_resa) {
    var sUrl = delete_resa_ws+"&ID_resa="+id_resa;
    
    ///////////////// !!! CALLBACK !!!
    var callback = {
    success: function(oResponse) {
            affiche_waiting(false);
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
				var commandes=oResults.resultat["commandes"];
                var messages=oResults.resultat["messages"];
              
                // Commandes
                for (ID_commande in commandes) {
                    var commande=commandes[ID_commande];
                    var objet=commande["objet"];
                    var methode=commande["methode"];
                    var parametres=commande["parametres"];
                    var str_eval="";
                    if (objet != "" && objet != undefined) {
                        str_eval=objet+"."+methode+"(parametres)";
                    } else {
                        str_eval=methode+"(parametres)";
                    }
                    eval (str_eval);
                }
                affiche_messages(messages);
                
			}
            clique_resa();
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 10000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    affiche_waiting(true);
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function maj_date_retour (parametres) {
    var id_pret=parametres["id_pret"];
    var date_retour=parametres["date_retour"];
    var nb_prolongations=parametres["nb_prolongations"];
    //alert ("prolonger pret "+id_pret+" au "+date_retour+" ("+nb_prolongations+")");
    var span_date=document.getElementById("span_retour_prevu_"+id_pret);
    span_date.innerHTML=date_retour;
    var a_date=document.getElementById("a_retour_prevu_"+id_pret);
    a_date.innerHTML=nb_prolongations;
}
 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cab_maj_bool (sType, aArgs) {
    //var nb=aResults.length;
    var aResults = aArgs[2];
    var nb = aResults.length;
    if (nb > 0) {
        bool_autocomplete=1;
    } else {
        bool_autocomplete=0;
    }
}
 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init () {
    init_cab(); // on initialise le cab (autocomplete)
    init_resultat(); // systèmes d'onglets avec liste des prêts, résas...
    set_focus_cab();
    if (cab_lecteur != "") {
        document.getElementById("cab_input").value=cab_lecteur;
        cab_valide();
    }
    set_couleur_cab("pret");
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init_cab() {
    cab_input=document.getElementById("cab_input");
    cab_conteneur=document.getElementById("cab_conteneur");
    cab_oDS = new YAHOO.util.XHRDataSource(cab_ws);
    cab_oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
    cab_oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
    cab_oDS.maxCacheEntries = 5;
    cab_oAC = new YAHOO.widget.AutoComplete(cab_input, cab_conteneur, cab_oDS);
    cab_oAC.queryQuestionMark=false;
    // evenements
    cab_oAC.textboxKeyEvent.subscribe(cab_keypress);
    cab_oAC.itemSelectEvent.subscribe(cab_valide_complete);
    cab_oAC.dataReturnEvent.subscribe(cab_maj_bool);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function init_resultat () {
    // 1) On crée les onglets
    tabView=new YAHOO.widget.TabView();
    var idx=0;
    for (ID_onglet in def_onglets) {
        var infos_onglet=def_onglets[ID_onglet];
        var intitule_onglet=infos_onglet["intitule_onglet"];
        var type=infos_onglet["type"];
        var ID=infos_onglet["ID"];
        var param=infos_onglet["param"];
        var evenements=infos_onglet["evenements"];
        var tab=new YAHOO.widget.Tab({
            label: intitule_onglet,
            content: "<div id='div_tab_"+ID+"'></div>",
        });
        
        for (ID_evenement in evenements) {
            var fonction_evenement=evenements[ID_evenement]["fonction"];
            var str="tab.addListener(\""+ID_evenement+"\", "+fonction_evenement+");"
            eval (str);
            
        }
        
        //tab.addListener('click', event_onglet_change);
        tabView.addTab(tab, idx);

                
        liste_onglets[ID]=new Object();
        liste_onglets[ID]["idx"]=idx;
        liste_onglets[ID]["type"]=type;
        liste_onglets[ID]["param"]=param;
        
        idx++
    }
    tabView.appendTo(document.getElementById("div_tab"));
    tabView.set('activeIndex', 0);
    //tabView.addListener('activeIndexChange', event_onglet_change);
    
    // 2) On les peuple
    for (ID_onglet in liste_onglets) {
        if (liste_onglets[ID_onglet]["type"]=="datatable") {
            crea_datatable(ID_onglet);
        }
    }
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function crea_datatable (ID_onglet) {
    var param=liste_onglets[ID_onglet]["param"];
    var idx=liste_onglets[ID_onglet]["idx"];
    var param_table=param["table"];
    
    param_table["scrollable"]=parseBool(param_table["scrollable"]);
    param_table["resizeable"]=parseBool(param_table["resizeable"]);
    param_table["initialLoad"]=parseBool(param_table["initialLoad"]);
    param_table["draggableColumns"]=parseBool(param_table["draggableColumns"]);
    
    // on génère la définition des colonnes 
    // les infos sont fournies par le registre sous forme d'Object et YUI attend une Array :/ 
    var param_col=new Array();
    for (id in param["colonnes"]) {
        param["colonnes"][id]["width"]=parseInt(param["colonnes"][id]["width"]);
        param["colonnes"][id]["sortable"]=parseBool(param["colonnes"][id]["sortable"]);
        param_col.push(param["colonnes"][id]);
    }
    
    // DataSource bidon pour initialiser le tableau
    var myDataSource = new YAHOO.util.DataSource(new Array()); 
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY; 
    
    var myDataTable = new YAHOO.widget.DataTable("div_tab_"+ID_onglet, param_col, myDataSource, param_table);
    liste_onglets[ID_onglet]["objet_js"]=myDataTable;

}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_zone_texte (parametres) {
    var ID_zone=parametres["ID_zone"];
    var texte=parametres["texte"];
    var div=document.getElementById(ID_zone);
    if (div == null) {
        return ("");
    }
    div.innerHTML=texte;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function ajoute_ligne (parametres) {
    var ID_onglet = parametres["ID_onglet"];
    var data = parametres["data"];
    liste_onglets[ID_onglet]["objet_js"].addRow(data, 0);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function reset_table (parametres) {
    var ID_onglet = parametres["ID_onglet"];
    var rs=liste_onglets[ID_onglet]["objet_js"].getRecordSet();
    var nb = rs.getLength();
    liste_onglets[ID_onglet]["objet_js"].deleteRows(0, nb);

}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_messages (messages) {
    var niveau_message="0";
    var a_afficher="";
    for (ID_message in messages) {
        var message=messages[ID_message];
        var code_message=message["code"];
        var texte_message=message["message"];
        if (texte_message == "-") {
            texte_message="";
        }
        
        
        if (code_message > niveau_message) {
            niveau_message = code_message;
        }
        if (a_afficher != "") {
            a_afficher += "<hr />" ;
        }
        a_afficher +=  texte_message;

    }
    
    // maj du conteneur
    affiche_conteneur_messages (a_afficher);
    
    if (niveau_message <= 1) {
        return ("");
    }
    
    if (niveau_message == "3") { // au choix
        var fin="<br/><hr /><br/><span class='div_bouton'><a class='div_bouton' href=\"javascript:valide_message('oui')\"><?PHP print (get_intitule  ("", $GLOBALS["affiche_page"]["parametres"]["intitules"]["label_bouton_accepter"], array())); ?></a></span><span class='div_bouton'><a class='div_bouton' href=\"javascript:valide_message('non')\"><?PHP print (get_intitule  ("", $GLOBALS["affiche_page"]["parametres"]["intitules"]["label_bouton_refuser"], array())); ?></a></span><br/>";
        //var fin="Accepter - Refuser"
    } else if (niveau_message == "4"){ // refus
        var fin="<br/><hr /><br/><span class='div_bouton'><a class='div_bouton' href=\"javascript:valide_message('non')\"><?PHP print (get_intitule  ("", $GLOBALS["affiche_page"]["parametres"]["intitules"]["label_bouton_refuser"], array())); ?></a></span><br/>";
    } else if (niveau_message == "2") { // info
        var fin="<br/><hr /><br/><span class='div_bouton'><a class='div_bouton' href=\"javascript:obj_message.destroy(); set_focus_cab();\"><?PHP print (get_intitule  ("", $GLOBALS["affiche_page"]["parametres"]["intitules"]["label_bouton_ok"], array())); ?></a></span><br/>";
    }
    
    
    
    a_afficher += fin;


    obj_message = new YAHOO.widget.Panel("panel2", { width:"320px", visible:false, draggable:true, close:true, modal:true, fixedcenter:true } ); 
    obj_message.setHeader("<?PHP print (get_intitule  ("", $GLOBALS["affiche_page"]["parametres"]["intitules"]["label_messages"], array())); ?>"); 
    obj_message.setBody(a_afficher); 
    //obj_message.render("div_formulaire"); 
    obj_message.render(document.body); 
    obj_message.center();
    obj_message.show();
   
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_conteneur_messages (chaine) {
    var param = new Object();
    param["ID_zone"]="indicateur_message";
    param["texte"]=chaine;
    affiche_zone_texte(param);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function valide_message (valeur) {
    obj_message.destroy();
    bool_validation=valeur;
    if (last_fonction != "") {
        var tmp = last_fonction;
        last_fonction="";
        eval (tmp);
    }
    set_focus_cab();
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_arbre (arbre) {
    var tmp=affiche_arbre_recursif(arbre);
    var div_compteur=document.getElementById("div_compteur");
    var html="<table class='div_compteur'><tr>";
    for (idx in tmp) {
        var elem=tmp[idx];
        var label=elem["label"];
        var compteur=elem["compteur"];
        var max=elem["max"];
        var classe="div_compteur_ok";
        if (compteur >= max) {
            classe="div_compteur_depasse";
        }
        html+="<td class='"+classe+"'><b>"+label+"</b> : "+compteur+"/"+max+"</td>";
    }
    html+="</table>";
    
    
    div_compteur.innerHTML=html;
    
}

function affiche_arbre_recursif (arbre) {
    var retour=new Array();
    var label="";
    var max="";
    var compteur="";
    for (idx in arbre) {
        branche=arbre[idx];
        if (idx == "_label") {
            label=branche;
        } else if (idx == "_max") {
            max=branche;
        } else if (idx == "_compteur") {
            compteur=branche;
        } else {
            if (typeof(branche)=="object"){
                var tmp=affiche_arbre_recursif(branche);
                if (tmp.length > 0) {
                    for (idx_tmp in tmp) {
                        var tmp_elem=tmp[idx_tmp];
                        retour.push(tmp_elem);
                    }
                }
            }
        }
        
    } // fin du pour chaque branche
    if (label != "") {
        var tmp_retour=new Object();
        tmp_retour["label"]=label;
        tmp_retour["max"]=max;
        tmp_retour["compteur"]=compteur;
        retour.unshift(tmp_retour);
    }
    return (retour);
}

function mc_cataloguer (a, b, param) {
    var type_obj=param.type_obj;
    var ID=param.ID;
    var grille=type_2_grille[type_obj]["grille"];
    var url="bib.php?module="+grille+"&ID_notice="+ID;
    window.open(url);
}

function mc_faire_du_pret (a, b, param) {
    var type_obj=param.type_obj;
    var ID=param.ID;
    //cab_input.value=ID;
    try {
        var param="{\"clefs\" : {\"cab\" : \""+ID+"\", \"mode\" : \"pret\"}}";
        cab_action (param);
    } catch (e) {
        return (false);
    }
}

function dbg_pret () {
    var url=transaction_ws+"&operation=debug"+"&ID_operation="+id_operation;
    window.open(url);
}

</script>
    
<!--  Fin du template "javascript" de la page -->
