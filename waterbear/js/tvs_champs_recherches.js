function tvs_champ_recherche (parametres) {
    // Les icones peuvent soit exécuter des fonctions globales
    // ou des méthodes propres au champ auquel elles sont associées
    // pour cela, l'appel aura la forme "recherchator.liste_champs[#id#].maFonction()"
    
    this.id=parametres.id;
    this.booleens=parametres["booleens"];
    this.critere=parametres["critere"];
    this.critere_intitule=parametres["critere_intitule"];
    this.types_recherches=parametres["types_recherches"];
    this.classe_css=parametres["classe_css"];
    this.autoplugin=parametres["autoplugin"];
    this.schema_jointure=parametres["schema_jointure"];
    this.icones=parametres.icones;
    this.plugin_formate_critere=parametres["plugin_formate_critere"];
    
    this.booleen=parametres["booleen"];
    this.type_recherche=parametres["type_recherche"];
    this.valeur=parametres["valeur"];
    this.valeur_defaut=parametres["valeur_defaut"];
    this.type_obj_lien=parametres["type_obj_lien"];
    this.type_lien=parametres["type_lien"];
    this.sens_lien=parametres["sens_lien"];
    
    if (this.booleen===undefined) {
        this.booleen="";
    }

    if (this.valeur===undefined || this.valeur === false) {
        this.valeur="";
    }
    
    if (this.valeur_defaut===undefined || this.valeur_defaut === false) {
        this.valeur_defaut="";
    }
    
    if (this.valeur === "") {
        this.valeur=this.valeur_defaut;
    }
    
    
    
    if (this.type_recherche===undefined) {
        this.type_recherche="";
    }
    //this.icones;
    this.div=document.createElement ("div");
    
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_booleen = function () {
        var str="";
        for (bidon in this.booleens) {
            var selected=" ";
            var valeur = this.booleens[bidon]["valeur"];
            var intitule = this.booleens[bidon]["intitule"];
            if (valeur == this.booleen) {
                selected = " selected ";
            }
            str+="<option name='booleen' "+selected+" value='"+valeur+"'>"+intitule+"</option>";
        }
        str="<select class='"+this.classe_css+"' role='select_booleen'>"+str+"</select>";
        return (str);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.get_booleen = function () {
        var tmp=this.div.getElementsByTagName("select");
        var valeur=tmp[0].value;
        return (valeur);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_types_recherches = function () {
        var str="";
        for (bidon in this.types_recherches) {
            var selected=" ";
            var valeur = this.types_recherches[bidon]["valeur"];
            var intitule = this.types_recherches[bidon]["intitule"];
            if (valeur == this.type_recherche) {
                selected = " selected ";
            }
            str+="<option "+selected+" value='"+valeur+"'>"+intitule+"</option>";
        }
        str="<select class='"+this.classe_css+"' role='select_type'>"+str+"</select>";
        return (str);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.get_type_recherche = function () {
        var tmp=this.div.getElementsByTagName("select");
        var valeur=tmp[1].value;
        return (valeur);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // fonction par défaut mais doit être surchargée la plupart du temps
    this.get_valeur = function () {
        var tmp=this.div.getElementsByTagName("input");
        var valeur=tmp[0].value;
        return (valeur);
    }
    
    
    

    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.genere_champ = function () {
        
        var str="";
        var str_booleen = this.set_booleen ();
        var str_types_recherches = this.set_types_recherches ();
        var str_champ = this.set_champ ();
        var html_icones = this.genere_icones();
        
        str="<table role='table_critere' class='"+this.classe_css+"'><tr role='tr_critere' class='"+this.classe_css+"'><td role='td_booleen' class='"+this.classe_css+"'>"+str_booleen+"</td><td role='td_intitule' class='"+this.classe_css+"'><a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+this.autoplugin['nom_plugin']+"/parametres' title='parametres du champ de recherche dans le registre' target='_blank'> "+this.critere_intitule+" </a>: </td><td role='td_type' class='"+this.classe_css+"'>"+str_types_recherches+"</td><td role='td_champ' class='"+this.classe_css+"'> "+str_champ+"</td><td role='td_icones' class='"+this.classe_css+"'><table>"+html_icones+"</table></td></tr></table>";
        
        
        this.div.innerHTML=str;
        return (this.div);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.valide_champ = function () {
        var retour=new Object();
        retour.booleen=this.get_booleen();
        retour.intitule_critere=this.critere;
        retour.type_recherche=this.get_type_recherche();
        retour.valeur_critere=this.get_valeur();
        retour.autoplugin=this.autoplugin;
        retour.schema_jointure=this.schema_jointure;
        
        // si schema_jointure, on remplace @valeur_critere et @type_recherche par les variables du même nom
        if (retour.schema_jointure != undefined) {
            //alert (retour.schema_jointure);
            retour.schema_jointure=this.formate_jointure(retour.schema_jointure, retour.valeur_critere, retour.type_recherche);
            retour.type_recherche="jointure";
        }
        
        retour.plugin_formate_critere=this.plugin_formate_critere;
        retour.type_obj_lien=this.type_obj_lien;
        retour.type_lien=this.type_lien; 
        retour.sens_lien=this.sens_lien;
        return (retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ = function () {
        // A SURCHARGER DANS LES ENFANTS
        // génère un champ de recherche spécifique (textbox, autocomplete, select...) 
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.formate_jointure = function (schema_jointure, valeur_critere, type_recherche) {
        for (id in schema_jointure) {
            if (typeof(schema_jointure[id])=="string") {
                schema_jointure[id]=schema_jointure[id].replace("@valeur_critere", valeur_critere);
                schema_jointure[id]=schema_jointure[id].replace("@type_recherche", type_recherche);
            } else if (typeof(schema_jointure[id]=="object")) {
                schema_jointure[id]=this.formate_jointure(schema_jointure[id], valeur_critere, type_recherche);
            }
        }
        return(schema_jointure);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.post_genere_champ = function () {
        // A SURCHARGER DANS LES ENFANTS (optionnel)
        // fonction appelée UNE FOIS que le DOM a été mis à jour avec les données retournées par genere_champ()
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Retourne le code HTML correspondant aux icones
    this.genere_icones = function () {
        var html_icones="";
        var icones=this.icones;
        if (!isArray(icones)) {
            //return("");
        }
        for (id in icones) {
            var src=icones[id]["src"];
            var alt=icones[id]["alt"];
            var action=icones[id]["action"];
            src=add_skin(src);
            action=this.formate_lien(action); // on remplace les motifs par les valeurs
            html_icones+="<td class='"+this.classe+"' role='cellule_icone'><img class='"+this.classe+"' role='image_icone' src='"+src+"' alt='"+alt+"' title='"+alt+"' onClick=\""+action+"\"/></td>";
        }
        return (html_icones);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode permet de remplacer  les mots-clefs par des varaibles (dans un lien par ex)
    this.formate_lien = function (chaine) {
        if (chaine == undefined) {
            return ("");
        }
        var reg1 = new RegExp ("#id#", "gi");
        chaine = chaine.replace(reg1, this.id);
        return(chaine);
    }
    
     
    
} // fin de la classe

   



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////






function tvs_champ_textbox (parametres) {
    
    // Héritage
    this.parent = tvs_champ_recherche;
    this.parent(parametres);
    
   
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ = function () {
        var str="<input class='"+this.classe_css+"' role='select_champ' value='"+this.valeur+"'/>";
        return (str);
    }
  
} // fin de la classe

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////






function tvs_champ_textarea (parametres) {
    
    // Héritage
    this.parent = tvs_champ_recherche;
    this.parent(parametres);
    
   
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ = function () {
        //var str="<input class='"+this.classe_css+"' role='select_champ' value='"+this.valeur+"'/>";
        var str="<textarea class='"+this.classe_css+"' role='select_champ'>"+this.valeur+"</textarea>";
        return (str);
    }
    
    this.get_valeur = function () {
        var tmp=this.div.getElementsByTagName("textarea");
        var valeur=tmp[0].value;
        return (valeur);
    }
  
} // fin de la classe

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function tvs_champ_select (parametres) {
    // Héritage
    this.parent = tvs_champ_recherche;
    this.parent(parametres);
    
   
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ = function () {
        var liste_choix=parametres["liste_choix"];
        var valeur_defaut=String(this.valeur);
        var selected = "";
        var str="<option  value=''> --- </option>\n";
        for (idx in liste_choix) {
            var choix=liste_choix[idx];
            var intitule=choix["intitule"];
            var valeur=choix["valeur"];
            if (String(valeur) == valeur_defaut) {
                selected = "selected='true'";
            } else {
                selected="";
            }
            str+="<option "+selected+" value=\""+valeur+"\"> "+intitule+"</option>\n";
        }
        
        var str="<select class='"+this.classe_css+"' role='select_champ'>"+str+"</select>";
        return (str);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.get_valeur = function () {
        var tmp=this.div.getElementsByTagName("select");
        var valeur=tmp[2].value;
        return (valeur);
    }
    
} // fin de la classe

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function tvs_champ_autocomplete (parametres) {
    // Héritage
    this.parent = tvs_champ_recherche;
    this.parent(parametres);
    
    // Paramètres spécifiques à autocomplete
    this.ws_url=parametres["ws_url"];
    
    // variables spécifiques à autocomplete    
    this.conteneur; // objet DOM (<div>) contenant la liste des propositions
    this.oDS; // objet YUI gérant l'échange de données
    this.oAC; // Objet YUI autocomplete
    this.input; // élément <input> de l'autocomplete (où on saisit le texte)
    
    this.valeur_autocomplete="";
    
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ = function () {
        var str="<div><input role='input' type='text' value='"+this.valeur+"'/><div></div></div>";
        return str;
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.post_genere_champ = function () {
        this.input=this.div.getElementsByTagName("input").item(0);
        this.conteneur=this.input.nextSibling;
        this.oDS = new YAHOO.util.XHRDataSource(this.ws_url);
        this.oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
        this.oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les données ne seront pas accessibles via ["xxx"] mais [0], [1]...
        this.oDS.maxCacheEntries = 5;
        this.oAC = new YAHOO.widget.AutoComplete(this.input, this.conteneur, this.oDS);
        this.oAC.queryQuestionMark=false;
        this.oAC.itemSelectEvent.subscribe(this.valide_autocomplete, this);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.valide_autocomplete = function (sType, aArgs, obj_this) {

        var myAC = aArgs[0]; // reference back to the AC instance
        var elLI = aArgs[1]; // reference to the selected LI element
        var oData = aArgs[2]; // object literal of selected item's result data
        var intitule=oData[0];
        var valeur =oData[1];
        if (valeur != null) {
            var tmp=obj_this.div.getElementsByTagName("input");
            tmp[0].value=valeur;
        } 
    }
    

} // fin de la classe

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function tvs_champ_panier_lien (parametres) { // hérite de tvs_champ_autocomplete
    // Héritage
    this.parent = tvs_champ_autocomplete;
    this.parent(parametres);
    
    // variables spécifiques
    //this.type_obj_lien=parametres["type_obj_lien"];
    //this.type_lien=parametres["type_lien"];
    //this.sens_lien=parametres["sens_lien"];
    this.liste_types_liens=parametres["liste_types_liens"];
    
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SURCHARGE !!
    this.genere_champ = function () {
        var str="";
        var str_booleen = this.set_booleen ();
        var str_types_liens = this.set_types_liens ();
        var str_champ = this.set_champ ();
        var html_icones = this.genere_icones();
        
        str="<table role='table_critere' class='"+this.classe_css+"'><tr role='tr_critere' class='"+this.classe_css+"'><td role='td_booleen' class='"+this.classe_css+"'>"+str_booleen+"</td><td role='td_intitule' class='"+this.classe_css+"'> "+this.critere_intitule+" : </td><td role='td_type' class='"+this.classe_css+"'>"+str_types_liens+"</td><td role='td_champ' class='"+this.classe_css+"'> "+str_champ+"</td><td role='td_icones' class='"+this.classe_css+"'><table>"+html_icones+"</table></td></tr></table>";
        
        
        this.div.innerHTML=str;
        return (this.div);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SURCHARGE !!
    this.valide_champ = function () {
        var retour=new Object();
        retour.booleen=this.get_booleen();
        retour.intitule_critere=this.critere;
        retour.type_recherche="panier_lien";
        retour.valeur_critere=this.get_valeur();
        retour.type_obj_lien=this.type_obj_lien;
        retour.type_lien=this.get_type_recherche(); // on utilise la même méthode, mais en fait c'est le type de lien qu'on récupère, pas le type de recherche
        retour.sens_lien=this.sens_lien;
        retour.autoplugin=this.autoplugin;
        return (retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_types_liens = function () {
        var str="<option selected value=''>----</option>";
        for (bidon in this.liste_types_liens) {
            var selected=" ";
            var valeur = this.liste_types_liens[bidon]["valeur"];
            var intitule = this.liste_types_liens[bidon]["intitule"];
            
            str+="<option  value='"+valeur+"'>"+intitule+"</option>";
        }
        str="<select role='select_type_lien' class='"+this.classe_css+"'>"+str+"</select>";
        return (str);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_valeur = function (valeur) {
        this.div.getElementsByTagName("input")[0].value=valeur;
    }
    
    
    
} // fin de la classe

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function tvs_champ_comptage (parametres) { // hérite de tvs_champ_panier_lien
    // Héritage
    this.parent = tvs_champ_panier_lien;
    this.parent(parametres);
    
    this.nom_panier=parametres["nom_panier"];
    
    if (this.nom_panier==undefined) {
        this.nom_panier="";
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SURCHARGE !!
    this.genere_champ = function () {
        var str="";
        var str_booleen = this.set_booleen ();
        var str_types_liens = this.set_types_liens ();
        var str_types_recherches = this.set_types_recherches();
        var str_champ_panier = this.set_champ_panier ();
        var str_champ_valeur = this.set_champ_valeur();
        var html_icones = this.genere_icones();
        
        str="<table role='table_critere' class='"+this.classe_css+"'><tr role='tr_critere' class='"+this.classe_css+"'><td role='td_booleen' class='"+this.classe_css+"'>"+str_booleen+"</td><td role='td_intitule' class='"+this.classe_css+"'> "+this.critere_intitule+" : </td><td role='td_type' class='"+this.classe_css+"'>"+str_types_liens+"</td><td role='td_panier_comptage' class='"+this.classe_css+"'> "+str_champ_panier+"</td> <td role='td_type_comptage' class='"+this.classe_css+"'>"+str_types_recherches+"</td>  <td role='td_champ_comptage' class='"+this.classe_css+"'>"+str_champ_valeur+"</td>  <td role='td_icones' class='"+this.classe_css+"'><table>"+html_icones+"</table></td></tr></table>";
        
        
        this.div.innerHTML=str;
        return (this.div);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SURCHARGE !!
    this.valide_champ = function () {
        var retour=new Object();
        retour.booleen=this.get_booleen();
        retour.intitule_critere=this.critere;
        retour.type_recherche="comptage";
        retour.nom_panier_comptage=this.get_valeur();
        retour.type_obj_lien=this.type_obj_lien;
        retour.type_lien=this.get_type_recherche(); // on utilise la même méthode, mais en fait c'est le type de lien qu'on récupère, pas le type de recherche
        retour.sens_lien=this.sens_lien;
        retour.valeur_critere=this.get_valeur_comptage();
        retour.type_comptage=this.get_type_comptage();
        retour.autoplugin=this.autoplugin;
        return (retour);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ_panier = function () {
        var str="<div><input role='input' type='text' value='"+this.nom_panier+"'/><div></div></div>";
        return str;
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_champ_valeur = function () {
        var str="<input class='"+this.classe_css+"' role='select_champ' value='"+this.valeur+"'/>";
        return (str);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.get_valeur_comptage = function () {
        var tmp=this.div.getElementsByTagName("input");
        var valeur=tmp[1].value;
        return (valeur);
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    this.get_type_comptage = function () {
        var tmp=this.div.getElementsByTagName("select");
        var valeur=tmp[2].value;
        return (valeur);
    }
    
} // fin de la classe
