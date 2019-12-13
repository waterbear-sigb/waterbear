<!-- Ce template contient des fonctions JS utiles partout  -->
<script type="text/javascript" src="js/yui/yahoo/yahoo-min.js"></script> 
<script type="text/javascript" src="js/yui/event/event-min.js"></script> 
<script type="text/javascript" src="js/yui/connection/connection-min.js"></script> 
<script type="text/javascript" src="js/yui/dom/dom-min.js"></script> 
<script src="js/yui/dragdrop/dragdrop-min.js"></script>
<script src="js/yui/container/container-min.js"></script>

<script language="javascript">

// Variables JS utiles partout
var skin="<?PHP  print($_SESSION["system"]["skin"]);  ?>";
var timestamp_last_schedule="<?PHP  print($_SESSION["system"]["timestamp_last_schedule"]);  ?>";
var frequence_schedule_recherches=<?PHP print($GLOBALS["affiche_page"]["parametres"]["schedule_recherches"]["frequence"]);  ?>;
var liste_schedule_recherches=new Array();
<?PHP

    foreach ($GLOBALS["affiche_page"]["parametres"]["schedule_recherches"]["recherches"] as $nom_recherche => $recherche) {
        $ws=$recherche["ws"];
        $lien=$recherche["lien"];
        $alt=$recherche["alt"];
        $code=$recherche["code"];
        $img=$recherche["img"];
        $img2=add_skin($img);
        $nb_notices=$_SESSION["system"]["schedule_recherches"][$nom_recherche]["nb_notices"];
        print ("liste_schedule_recherches['$nom_recherche']=new Array(); \n");
        print ("liste_schedule_recherches['$nom_recherche']['ws']='$ws'; \n");
        print ("liste_schedule_recherches['$nom_recherche']['lien']='$lien'; \n");
        print ("liste_schedule_recherches['$nom_recherche']['alt']='$alt'; \n");
        print ("liste_schedule_recherches['$nom_recherche']['code']='$code'; \n");
        print ("liste_schedule_recherches['$nom_recherche']['img']='$img'; \n");
        print ("liste_schedule_recherches['$nom_recherche']['nb_notices']='$nb_notices'; \n");
        
    }

?>



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cette fonction remplace toutes les occurences de #XXX# par la valeur de XXX
// motif : chaine de caractère contenant les éléments à remplacer
// remplacement : tableau associatif de type ["a_remplacer"]=>"chaine de remplacement"
function get_intitule (motif, remplacement) {
    for (var a_remplacer in remplacement) {
        var remplacer = remplacement[a_remplacer];
        var a_remplacer="#"+a_remplacer+"#";
        var reg=new RegExp("("+a_remplacer+")", "g");
        motif=motif.replace (reg, remplacer);
    }
    return(motif);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Return a boolean value telling whether // the first argument is a string. 
function isString() {if (typeof arguments[0] == 'string') return true;if 

(typeof arguments[0] == 'object') {  var criterion =   

    arguments[0].constructor.toString().match(/string/i); 
 return (criterion != null);  }return false;}
 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
// Return a boolean value telling whether // the first argument is an Array object. 
function isArray() {if (typeof arguments[0] == 'object') {  var 

criterion = 

    arguments[0].constructor.toString().match(/array/i); 
 return (criterion != null);  }return false;} 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// type_affichage : nl | br
function print_r (objet, nb_tab, type_affichage) {
    var nl = "\n";
    var esp = "   ";
    if (type_affichage=="br") {
        nl = "<br/>";
        esp = "&nbsp;&nbsp;&nbsp;";
        
    }
    if (typeof(objet) != 'object') {
        return ("pas un objet");
    }
    var tab="";
    for (var i=0 ; i <= nb_tab ; i++) {
        tab+=esp;
    }
    var tab2=tab+esp;
    
    var chaine=tab+"{"+nl;
    for (var clef in objet) {
        element=objet[clef];
        if (typeof(element) != 'object') {
            //alert (typeof(element));
            chaine_element=element;
        } else {
            chaine_element=print_r(element, nb_tab+1, type_affichage);
        }
        chaine+=tab2+clef+" => "+chaine_element+nl;
    }
    chaine+=tab+"}"+nl;
    return (chaine);
}

var menu_contextuel="";
var mc_contexte="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["mc_contexte"])  ?>";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function parseBool (val) {
    if (val == "false" || val == "0" || val == false || val == "") {
        return (false);
    }
    return (true);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function fn_mc (e, type_obj, ID, idx) {
    
    if (mc_contexte==undefined) {
        mc_contexte="defaut";
    }
    
    // on récupère le type d'événement : click ou contextmenu. Peut être fourni sous forme de chaine (pour forcer) ou obj javascript
    var type;
    var x=document.body.clientWidth/2;
    var y=document.body.clientHeight/2;
    if (typeof(e)=="string") {
        type=e;
    } else {
        x=e.clientX;
        y=e.clientY;
        type=e.type;
    }
    
    
    
    
    // on RAZ le menu contextuel si existant
    if (menu_contextuel != "") {
        try {
        menu_contextuel.destroy();
        } catch (err) {
            // on en fait rien, mais je ne sais pas pourquoi, une fois sur 10, cette fonction génère une erreur qui bloque si on ne fait pas un try catch :/
        }
    }
    
    // On récupère les infos
    var sUrl="bib_ws.php?module=div/menus_contextuels&operation=get_menu_contextuel&ID="+ID+"&type_obj="+type_obj+"&contexte="+mc_contexte+"&idx="+idx;
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                oResults.resultat.type_obj=type_obj;
                oResults.resultat.ID=ID;
                oResults.resultat.idx=idx;
                oResults.resultat.contexte=mc_contexte;
                if (type == "contextmenu") {
                    affiche_menu_contextuel (x,y, oResults.resultat);
                } else if (type == "click") {
                    var param = new Object();
                    param["ID"]=ID;
                    param["idx"]=idx;
                    param["type_obj"]=type_obj;
                    param["contexte"]=mc_contexte;
                    for (i in oResults.resultat.menus) {
                        var fn = oResults.resultat.menus[i].onclick.fn;
                        param["param_registre"]=oResults.resultat.menus[i].onclick.param_registre;
                        var chaine = fn+"('', '', param);";
                        eval (chaine);
                        return (false);
                    }
                }
            
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Echec lors de la connexion");
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
    return (false);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function affiche_menu_contextuel (x,y, menus) {
    // on RAZ le menu contextuel si existant
    if (menu_contextuel != "") {
        try {
        menu_contextuel.destroy();
        } catch (err) {
            // on en fait rien, mais je ne sais pas pourquoi, une fois sur 10, cette fonction génère une erreur qui bloque si on ne fait pas un try catch :/
        }
    }
    var param=new Object();
    param.x=x;
    param.y=y;
    var ID=menus.ID;
    var contexte=menus.contexte;
    var type_obj=menus.type_obj;
    var idx=menus.idx;
    
    var liste_menus=new Array();
    for (i in menus.menus) {
        var menu=new Object();
        menu.text=menus.menus[i].text;
        menu.onclick=new Object;
        menu.onclick.obj=new Object();
        //menu.onclick.obj=menus.menus[idx].onclick.obj;
        menu.onclick.obj.ID=ID;
        menu.onclick.obj.idx=idx;
        menu.onclick.obj.contexte=contexte;
        menu.onclick.obj.type_obj=type_obj;
        menu.onclick.obj.param_registre=menus.menus[i].onclick.param_registre;
        var tmp="menu.onclick.fn="+menus.menus[i].onclick.fn+";";
        eval(tmp);
        liste_menus.push(menu);
    }
    
    menu_contextuel = new YAHOO.widget.Menu("menu_contextuel", param);
    menu_contextuel.addItems(liste_menus);
    menu_contextuel.render("div_menu_contextuel");
    menu_contextuel.show();   
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function mc_toto (a, b, param) {
    alert ("mc_toto - ID : "+param.ID+" - type_obj : "+param.type_obj+" - p1 : "+param.p1);
    //alert (print_r(param, 0));
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function affiche_waiting (bool_waiting) {
    var img=document.getElementById("ico_waiting");
    if (bool_waiting == true) {
        img.style.visibility="visible";
    } else {
        img.style.visibility="hidden";
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function date_us_2_date (chaine) {
    var tmp = chaine.split('-');

    var annee = parseInt(tmp[0], 10);
    var mois = parseInt(tmp[1], 10);
    var jour = parseInt(tmp[2], 10);
    mois--; // on retire 1 car les mois commencent à 0
    var date = new Date(annee, mois, jour);
    //alert (tmp[0]+"*"+tmp[1]+"*"+tmp[2]+"*******"+annee+"*"+mois+"*"+jour);
    return (date);    
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function barre_laterale (action) {
    var barre_ouverte=document.getElementById("div_barre_laterale_ouverte");
    var barre_fermee=document.getElementById("div_barre_laterale_fermee");
    var main=document.getElementById("div_main");
    if (action == "fermer") {
        barre_ouverte.style.visibility="hidden";
        barre_fermee.style.visibility="visible";
        main.className="div_main_extended";
    } else if (action == "ouvrir") {
        barre_ouverte.style.visibility="visible";
        barre_fermee.style.visibility="hidden";
        main.className="div_main";
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function escape_guillemets (chaine) {
    if (!isString(chaine)) {
        return (chaine);
    }
    chaine2=chaine.replace (/"/g, "&quot;");
    return(chaine2);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



function mc_ouvrir_special (a, b, param) {
    var type_obj=param.type_obj;
    var ID=param.ID;
    var idx=param.idx;
    var url=param.param_registre.url;
    url+=ID;
    window.open(url);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function catalogage_rapide (obj) {
    var valeur=obj.value;
    var ID_notice=obj.getAttribute("ID_notice");
    var type_obj=obj.getAttribute("type_obj");
    var ws_url_traitement=obj.getAttribute("ws_url");
    var sUrl = ws_url_traitement+"&ID_notice="+ID_notice+"&valeur="+valeur+"&type_obj="+type_obj;
        //var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                
                // on ne fait rien
                
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur lors de la connexion");
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function recherche_rapide_focus () {
    var champ=document.getElementById("champ_recherche_rapide");
    champ.value="";
    champ.className="champ_recherche_rapide_focus";
}

function recherche_rapide_blur() {
    var champ=document.getElementById("champ_recherche_rapide");
    champ.value="code barre, ISBN, carte lecteur";
    champ.className="champ_recherche_rapide_blur";
    
}

function recherche_rapide_submit () {
    var champ=document.getElementById("champ_recherche_rapide");
    var valeur=champ.value;
    var ws_url=champ.getAttribute("ws_url");
    //alert("submit "+valeur+" -> "+ws_url);
    
    var sUrl = ws_url+"&cab="+valeur;
        //var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                //alert (oResults.resultat.url);
                menu_action_clic('', '', oResults.resultat.url);
                
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur lors de la connexion");
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
    
    return false; // pour enmpêcher le formulaire de valider
}

// copie le contenu d'un élément dans le presse-papier
// à partir du nom (text) d'un élément dom

function clipboard_copy (elem) {
    var selectedText = "";
    var div=document.getElementById(elem);
    var range = document.createRange() // create new range object
    range.selectNodeContents(div) // set range to encompass desired element text
    var selection = window.getSelection() // get Selection object from currently user selected text
    selection.removeAllRanges() // unselect any user selected text (if any)
    selection.addRange(range) // add range to Selection object to select it
    if (window.getSelection){ // all modern browsers and IE9+
        selectedText = window.getSelection().toString();
        
    } else {
        alert ("impossible de copier dans le presse papier avec ce navigateur. Vous devez le faire manuellement");
        return ("");
    }
    
    try{
        document.execCommand("copy") // run command to copy selected text to clipboard
        alert ("OK");
    } catch(e){
        alert ("impossible de copier dans le presse papier avec ce navigateur. Vous devez le faire manuellement");
        return ("");
    }
    
    return (selectedText);
    
}

// Pour modifier le skin

function change_skin (select) {
    var skin=select.value;
    window.location.href="bib.php?skin="+skin; 

}

// Pour appliquer un skin sur une chaine (img surtout)
function add_skin (chaine) {
    if (skin=="" || skin=="defaut" || skin==undefined) {
        // on ne fait rien
    } else {
        chaine="skins/"+skin+"/"+chaine;
    }
    //alert (chaine);
    return(chaine);
}

// Recherches automatisées
function schedule_recherches() {
    var now=Date.now(); // timestamp en ms

    if (now - timestamp_last_schedule > frequence_schedule_recherches) {
        timestamp_last_schedule=now;
        for (idx_recherche in liste_schedule_recherches) {
            recherche=liste_schedule_recherches[idx_recherche];
            var sUrl=recherche["ws"];
            var lien=recherche["lien"];
            var alt=recherche["alt"];
            var code=recherche["code"];
            
            sUrl+="&timestamp="+now+"&idx="+idx_recherche;

            var callback = {
          	// SUCCES
            success: function(oResponse) {
                var oResults = eval("(" + oResponse.responseText + ")");
                if (oResults.succes != 1) {
    				alert (oResults.erreur);
    			} else {
                    var nb_notices=oResults.resultat.nb_notices;
                    var idx=oResults.resultat.idx;

                    var recherche=liste_schedule_recherches[idx];
                    var code=recherche["code"];
                    var img=recherche["img"];
                    var alt=recherche["alt"];
                    alt+=" ("+nb_notices+")";
                    img=add_skin(img);

                    var dom_img=document.getElementById(code);
                    if (nb_notices != "0") {
                        dom_img.setAttribute("src", img);
                        dom_img.setAttribute("title", alt);
                        dom_img.setAttribute("class", "img_schedule_on");
                    } else {
                        dom_img.setAttribute("src", "");
                        dom_img.setAttribute("title", "");
                        dom_img.setAttribute("class", "img_schedule");
                    }

                    
    			}
            },
            
            // ECHEC
            failure: function(oResponse) {
                alert ("Erreur lors de la connexion");
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
        
    }

}

// pour rafraichir les icones schedule
function update_schedule () {
    timestamp_last_schedule="";
    schedule_recherches();
}

// Cette fonction affiche les icones schedule lors d'un rechargement de page (sans faire de recherche
function affiche_schedule (){
    for (idx_recherche in liste_schedule_recherches) {
        var recherche=liste_schedule_recherches[idx_recherche];
        var code=recherche["code"];
        var img=recherche["img"];
        var alt=recherche["alt"];
        var nb_notices=recherche["nb_notices"];
        alt+=" ("+nb_notices+")";
        img=add_skin(img);

        var dom_img=document.getElementById(code);
        if (nb_notices != "0" && nb_notices != "" && nb_notices != undefined) {
            dom_img.setAttribute("src", img);
            dom_img.setAttribute("title", alt);
            dom_img.setAttribute("class", "img_schedule_on");
        } 
    }  
}
setTimeout (affiche_schedule, 2000);
if (timestamp_last_schedule==0 || timestamp_last_schedule==undefined) {
    schedule_recherches();
}
var schedule = setInterval (schedule_recherches, frequence_schedule_recherches);



</script>