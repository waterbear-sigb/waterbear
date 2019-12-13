///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///// CLASS tvs_element_formulator
///// Classe dont h�riteront tous les �l�ments de formulaire
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////

function tvs_element_formulator (formulator_array) {
    // VARIABLES
    this.div; // balise <DIV> contenant le champ de formulaire
    this.input; // �l�ment de formulaire proprement dit (p-� input, textarea...)
    this.valeur; // valeur du champ (p-� diff�rent de ce qui est affich� dans le <input>)
    this.formulator = formulator_array;
    this.classe; // la classe appliqu�e � l'�l�ment
    this.ID; // id associ� � cet �l�ment
    this.bool_post_init=0;  // si 1, la formulator appelera la m�thode post_init() de cet objet quand tous les �l�ments auront �t� ajout�s au DOM
    
     
    
    // METHODES
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Retourne le code HTML correspondant aux icones
    this.genere_icones = function (icones) {
        var html_icones="";
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
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Retourne le code HTML correspondant aux �v�nements
    this.genere_evenements = function (evenements) {
        var html_evenements="";
        for (type_evenement in evenements) {
            var action = evenements[type_evenement];
            action=this.formate_lien(action); // on remplace les motifs par les valeurs
            html_evenements+=" "+type_evenement+"=\""+action+"\"";
        }
        return (html_evenements);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette m�thode permet de remplacer  les mots-clefs par des varaibles (dans un lien par ex)
    this.formate_lien = function (chaine) {
        if (chaine == undefined) {
            return ("");
        }
        var reg1 = new RegExp ("#formulator#", "gi");
        var reg2 = new RegExp ("#element#", "gi");
        var reg3 = new RegExp ("#conteneur#", "gi");
        chaine = chaine.replace(reg1, this.formulator["formulator"]);
        chaine = chaine.replace(reg2, this.formulator["element"]);
        chaine = chaine.replace(reg3, this.formulator["conteneur"]);
        return(chaine);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Met � jour l'�l�ment'
    this.set_valeur = function (texte) {
        this.input.value=texte;
        this.valeur=texte;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Descend l'�l�ment d'un cran
    this.descendre = function () {
        var param = new Object();
        param["action"]="descendre";
        param["ID_element"]=this.ID;
        formulator.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // monte l'�l�ment d'un cran
    this.monter = function () {
        var param = new Object();
        param["action"]="monter";
        param["ID_element"]=this.ID;
        formulator.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // supprime l'�l�ment'
    this.supprimer = function () {
        if (! confirm (intitules["l_suppr_valider"])) {
            return (true);
        }
        var param = new Object();
        param["action"]="supprimer";
        param["ID_element"]=this.ID;
        formulator.transaction (param);
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // fonction appel�e par le conteneur lorsqu'il supprime un �l�ment, pour permettre � ce dernier �ventuellement d'effectuer des actions
    // avant d'�tre supprim�. Par d�faut, rien, mais peut �tre surcharg�
    this.delete_self = function () {
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Validation par d�faut
    this.validation = function () {
        var param = new Object();
        param["action"]="validation";
        param["valeur"]=this.get_valeur();
        param["ID_element"]=this.ID;
        formulator.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // M�thode appel�e apr�s init(), une fois que le <div> a �t� ajout� au DOM
    this.post_init = function() {
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    this.hide_element = function() {
        this.div.style.height="0px";
        this.div.style.visibility="hidden";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    this.show_element = function() {
        this.div.style.height="";
        this.div.style.visibility="visible";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_couleur_fond = function (couleur) {
        if (couleur=="#null") {
            couleur="";
        }
        this.input.style.backgroundColor=couleur;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_couleur = function (couleur) {
        if (couleur=="#null") {
            couleur="";
        }
        this.input.style.color=couleur;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_graisse = function (graisse) {
        if (graisse=="#null") {
            graisse="";
        }
        this.input.style.fontWeight=graisse;
    }
    
    this.genere_aide  = function (aide) {
        if (aide == "" || aide == null || aide == undefined) {
            return ("");
        }
        var html_aide="<a onclick='alert(\""+aide+"\")'> <img src='IMG/icones_modif/aide_champ_formulaire.png' title=\""+aide+"\" alt=\""+aide+"\" /> </a>";
        
        return(html_aide);
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Teste la valeur du sous-champ et peut modifier son style en cons�quence
    // La d�finition doit �tre donn�e dans la d�finition du ss-champ sous la forme :
    // [test_valeur_champ][0,1,2... | else]  ==> diff�rentes v�rifications � effectuer. Si aucune v�rification n'est positive, la clause else peut �tre applel�e
    // Cette fonction est appel�e automatiquement au moment de l'init(), mais il faut l'aapeler manuellement en cas de changement via les switchers
    
    //                        [condition][comparaison]  => ==, !=, >, <
    //                                   [a_comparer] => valeur de la comparaison (peut �tre #date_jour) 
    //                                   [type_comparaison] => si vaut date, les chaines de carat�res sont converties en objet Date
    //                        [consequence][couleur_fond | ...] => pour l'instant il n'y a que la couleur de fond
    
     
    this.test_valeur = function () {
        //var valeur_comp=this.input.value;
        var valeur_comp=this.get_valeur();
        //var valeur_comp=this.valeur;
        if (valeur_comp == undefined) {
            valeur_comp="";
        }
        var bool_trouve = 0;
        var bool_else = 0;
        for (idx_valeur in this.test_valeur_champ) { // pour chaque �l�ment � �valuer
            if (idx_valeur == "else") {
                bool_else = 1;
                continue;
            }
            var bool_comparaison = 0;
            var condition = this.test_valeur_champ[idx_valeur]["condition"];
            var consequence = this.test_valeur_champ[idx_valeur]["consequence"];
            var comparaison=condition["comparaison"]; //== != < > <= >=
            var a_comparer=condition["a_comparer"]; // une valeur, #date_jour
            var type_comparaison=condition["type_comparaison"]; // par ex. date
            
            // si type date
            if (type_comparaison == "date") {
                valeur_comp=date_us_2_date(valeur_comp);
                if (a_comparer == "#date_jour") {
                    a_comparer = new Date();
                } else {
                    a_comparer = date_us_2_date(a_comparer);
                }
            }

            
            // diff�rentes comparaisons
            if (comparaison == "==") {
                if (valeur_comp == a_comparer) {
                    bool_comparaison = 1;
                }
            } else if (comparaison == ">") {
                if (valeur_comp > a_comparer) {
                    bool_comparaison = 1;
                }
            } else if (comparaison == "<") {
                if (valeur_comp < a_comparer) {
                    bool_comparaison = 1;
                }
            } else if (comparaison == "!=") {
                if (valeur_comp != a_comparer) {
                    bool_comparaison = 1;
                }
            } else if (comparaison == "<=") {
                if (valeur_comp <= a_comparer) {
                    bool_comparaison = 1;
                }
            } else if (comparaison == ">=") {
                if (valeur_comp >= a_comparer) {
                    bool_comparaison = 1;
                }
            } 
            
            // Cons�quences
            if (bool_comparaison == 1) {
                bool_trouve = 1;
                if (consequence["couleur_fond"] != "") {
                    this.set_couleur_fond(consequence["couleur_fond"]);
                }
                if (consequence["couleur"] != "") {
                    this.set_couleur(consequence["couleur"]);
                }
                if (consequence["graisse"] != "") {
                    this.set_graisse(consequence["graisse"]);
                }
            }
            
        } // fin du pour chaque �l�ment � �valuer
        
        // Si aucune comparaison concluante, �ventuelle clause "else"
        if (bool_trouve == 0 && bool_else == 1) {
            if (this.test_valeur_champ["else"]["couleur_fond"] != "") {
                this.set_couleur_fond(this.test_valeur_champ["else"]["couleur_fond"]);
            }
            if (this.test_valeur_champ["else"]["couleur"] != "") {
                this.set_couleur(this.test_valeur_champ["else"]["couleur"]);
            }
            if (this.test_valeur_champ["else"]["graisse"] != "") {
                this.set_graisse(this.test_valeur_champ["else"]["graisse"]);
            }
        }
    }
    
    // D�faut : pour r�cup�rer la valeur
    // peut �tre surcharg� pour certains types d'�l�ments
    this.get_valeur = function () {
        return (this.input.value);
    }
    
    
} // fin de la classe

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///// CLASS tvs_simple_textbox
///// Element de formulaire contenant un textbox simple
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function tvs_simple_textbox (formulator_array) {
    
    // H�ritage
    this.parent = tvs_element_formulator;
    this.parent(formulator_array);

    // VARIABLES
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne l'objet sous-champ (this)
    // on peut acc�der au <div> contenant l'input par XXX.div
    
    
    this.init = function (parametres) {
        //var ID = parametres["ID"]; // ID du champ
        var nom = parametres["nom"];
        var description = parametres["intitule"];
        var auto_plugin=parametres["auto_plugin"];
        description = "<a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+auto_plugin+"/parametres' target='_blank' title='parametrer cet element dans le registre'>"+description+"</a>";
        var readonly = "";
        if (parametres["readonly"] == "readonly") {
            readonly="readOnly='readonly'";
        }
        var valeur = parametres["valeur"];
        var icones = parametres["icones"]; // array
        var evenements = parametres["evenements"]; // array
        this.test_valeur_champ = parametres["test_valeur_champ"];
        if (valeur == undefined) {
            valeur="";
        }
        var valeur_affiche=escape_guillemets(valeur);
        if (icones == undefined) {
            icones=new Array();
        }
        if (evenements == undefined) {
            evenements=new Array();
        }
        
        this.classe = parametres["classe"];
       
        //icones=this.ajoute_icones_defaut(icones);

        this.div = document.createElement ("div");
        this.ID=parametres["id"];
        var html_icones = this.genere_icones(icones);
        var html_evenements = this.genere_evenements(evenements);
        var html_aide = this.genere_aide (parametres["aide"]);
        var tmp="<table class='"+this.classe+"' role='table_generale'><tr><td class='"+this.classe+"' role='cellule_nom'>"+nom+"</td><td class='"+this.classe+"' role='cellule_description'>"+description+html_aide+"</td><td class='"+this.classe+"' role='cellule_input'><input "+html_evenements+" class='"+this.classe+"' role='input' type='text' name='formulator_input1' value=\""+valeur_affiche+"\" "+readonly+" name='champ' /></td><td class='"+this.classe+"' role='cellule_toutes_icones'><table class='"+this.classe+"' role='tableau_icones'><tr>"+html_icones+"</tr></table></td></tr></table>";
        this.div.innerHTML = tmp;
        
        //this.input=this.div.getElementsByTagName("formulator_input1").item(0);
        this.input=this.div.getElementsByTagName("input").item(0);

        this.test_valeur();
        return (this);
    }
    
} // fin de la classe

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///// CLASS tvs_simple_textarea
///// Element de formulaire contenant un textarea simple
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function tvs_simple_textarea (formulator_array) {
    
    // H�ritage
    this.parent = tvs_element_formulator;
    this.parent(formulator_array);

    // VARIABLES
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne l'objet sous-champ (this)
    // on peut acc�der au <div> contenant l'input par XXX.div
    
    
    this.init = function (parametres) {
        //var ID = parametres["ID"]; // ID du champ
        var nom = parametres["nom"];
        var description = parametres["intitule"];
        var auto_plugin=parametres["auto_plugin"];
        description = "<a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+auto_plugin+"/parametres' target='_blank' title='parametrer cet element dans le registre'>"+description+"</a>";
        var readonly = "";
        if (parametres["readonly"] == "readonly") {
            readonly="readOnly='readonly'";
        }
        var valeur = parametres["valeur"];
        var icones = parametres["icones"]; // array
        var evenements = parametres["evenements"]; // array
        this.test_valeur_champ = parametres["test_valeur_champ"];

        if (valeur == undefined) {
            valeur="";
        }
        //valeur=escape_guillemets(valeur);
        if (icones == undefined) {
            icones=new Array();
        }
        if (evenements == undefined) {
            evenements=new Array();
        }
        this.classe = parametres["classe"];
        
        //icones=this.ajoute_icones_defaut(icones);

        this.div = document.createElement ("div");
        this.ID=parametres["id"];
        var html_icones = this.genere_icones(icones);
        var html_evenements = this.genere_evenements(evenements);
        var html_aide = this.genere_aide (parametres["aide"]);
        this.div.innerHTML = "<table class='"+this.classe+"' role='table_generale'><tr><td class='"+this.classe+"' role='cellule_nom'>"+nom+"</td><td class='"+this.classe+"' role='cellule_description'>"+description+html_aide+"</td><td class='"+this.classe+"' role='cellule_input'><textarea "+html_evenements+" class='"+this.classe+"' role='input' type='text' name='formulator_input1' "+readonly+" name='champ' >"+valeur+"</textarea></td><td class='"+this.classe+"' role='cellule_toutes_icones'><table class='"+this.classe+"' role='tableau_icones'><tr>"+html_icones+"</tr></table></td></tr></table>";
        //this.input=this.div.getElementsByTagName("formulator_input1").item(0);
        this.input=this.div.getElementsByTagName("textarea").item(0);
        this.test_valeur();
        return (this);
    }
   
} // fin de la classe

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///// CLASS tvs_simple_select
///// Element de formulaire contenant un select simple
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function tvs_simple_select (formulator_array) {
    
    // H�ritage
    this.parent = tvs_element_formulator;
    this.parent(formulator_array);

    // VARIABLES
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne l'objet sous-champ (this)
    // on peut acc�der au <div> contenant l'input par XXX.div
    
    
    this.init = function (parametres) {
        //var ID = parametres["ID"]; // ID du champ
        var nom = parametres["nom"];
        var description = parametres["intitule"];
        var auto_plugin=parametres["auto_plugin"];
        description = "<a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+auto_plugin+"/parametres' target='_blank' title='parametrer cet element dans le registre'>"+description+"</a>";
        var readonly = "";
        if (parametres["readonly"] == "readonly") {
            readonly="readOnly='readonly'";
        }
        var valeur = parametres["valeur"];
        var icones = parametres["icones"]; // array
        var evenements = parametres["evenements"]; // array
        var liste_choix = parametres["liste_choix"];
        this.test_valeur_champ = parametres["test_valeur_champ"];

        if (valeur == undefined) {
            if (parametres["valeur_defaut"] != undefined) {
                valeur=parametres["valeur_defaut"];
            } else {
                valeur="";
            }
        }
        //valeur=escape_guillemets(valeur);
        var ancienne_valeur=valeur;
        
        if (icones == undefined) {
            icones=new Array();
        }
        if (evenements == undefined) {
            evenements=new Array();
        }
        this.classe = parametres["classe"];
        
        // ON DETERMINE LA LISTE DE CHOIX
        if (liste_choix == undefined || liste_choix == "" || liste_choix == null) {
            liste_choix=new Array();
            liste_choix[0]=new Array();
            liste_choix[0]["intitule"]="-";
            liste_choix[0]["valeur"]="";
        }
        var html_select=""; // html pour tout le select
        var html_selected=""; // selected="true"
        
        for (choix in liste_choix) {
            var intitule=liste_choix[choix]["intitule"];
            var valeur=liste_choix[choix]["valeur"];
            if (valeur == "_void") {
                valeur="";
            }
            html_selected="";
            if (valeur == ancienne_valeur) {
                html_selected="selected='true'";
            } 
            var valeur_affiche=escape_guillemets(valeur);
            html_select += "<option class='"+this.classe+"' role='option' "+html_selected+" value=\""+valeur_affiche+"\">"+intitule+"</option>";
        }
        
        
        
        //icones=this.ajoute_icones_defaut(icones);

        this.div = document.createElement ("div");
        this.ID=parametres["id"];
        var html_icones = this.genere_icones(icones);
        var html_evenements = this.genere_evenements(evenements);
        var html_aide = this.genere_aide (parametres["aide"]);
        var html_param=" <a target='_blank' href='bib.php?module=admin/admin_listes/catalogage&autoplugin="+auto_plugin+"'><img src='IMG/icones/cog.png' title='Modifier les elements de la liste' /></a>"
        this.div.innerHTML = "<table class='"+this.classe+"' role='table_generale'><tr><td class='"+this.classe+"' role='cellule_nom'>"+nom+"</td><td class='"+this.classe+"' role='cellule_description'>"+description+html_aide+html_param+"</td><td class='"+this.classe+"' role='cellule_input'><select "+html_evenements+" class='"+this.classe+"' role='select' type='text' name='formulator_input1' "+readonly+" name='champ' >"+html_select+"</select></td><td class='"+this.classe+"' role='cellule_toutes_icones'><table class='"+this.classe+"' role='tableau_icones'><tr>"+html_icones+"</tr></table></td></tr></table>";
        this.input=this.div.getElementsByTagName("select").item(0);
        this.test_valeur();
        return (this);
    }
    
    this.set_valeur = function (texte) {
        var liste_options=this.input.options;
        for (idx in liste_options) {
            var option=liste_options[idx];
            if (option.value == texte) {
                this.input.selectedIndex=idx;
                this.valeur=texte;
                this.validation(); // oblig� de faire �a, car JS ne d�tecte pas l'�v�nement 'onChange' quand c'est modifi� par le script pour les select
                return ("");
            }
        }
        //this.valeur=texte;
    }
    
    this.get_valeur = function () {
        var selectedIndex=this.input.selectedIndex;
        var valeur=this.input.options[selectedIndex].value;
        return (valeur);
    }
   
} // fin de la classe

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///// CLASS tvs_autocomplete
///// Element de formulaire contenant un autocomplete
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////

function tvs_autocomplete (formulator_array) {
    
    // H�ritage
    this.parent = tvs_element_formulator;
    this.parent(formulator_array);

    // VARIABLES
    this.evenements;
    this.oDS; // objet YUI g�rant l'�change de donn�es
    this.oAC; // Objet YUI autocomplete
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne l'objet sous-champ (this)
    // on peut acc�der au <div> contenant l'input par XXX.div
    
    
    this.init = function (parametres) {
       
        // Variables sp�cifiques � autocomplete
        var conteneur; // objet DOM (<div>) contenant la liste des propositions
        
        
        
        var nom = parametres["nom"];
        var description = parametres["intitule"];
        this.valeur = parametres["valeur"];
        var auto_plugin=parametres["auto_plugin"];
        description = "<a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+auto_plugin+"/parametres' target='_blank' title='parametrer cet element dans le registre'>"+description+"</a>";
        var icones = parametres["icones"]; // array
        this.forceSelection=parametres["forceSelection"];
        this.maxResultsDisplayed=parametres["maxResultsDisplayed"];
        this.minQueryLength=parametres["minQueryLength"];
        this.queryDelay=parametres["queryDelay"];
        
        this.evenements = parametres["evenements"]; // array
        this.test_valeur_champ = parametres["test_valeur_champ"];
        //var liste_choix = parametres["liste_choix"];
        this.ws_url=parametres["ws_url"];
        var id_operation=formulator.id_operation; // on rajoute id_opertaion au ws_url pour les recherches qui seraient contextualis�es par la saisie de la grille
        this.ws_url+="ID_operation="+id_operation+"&";
        

        if (this.valeur == undefined) {
            this.valeur="";
        }
        var valeur_affiche=escape_guillemets(this.valeur);
        if (icones == undefined) {
            icones=new Array();
        }
        if (this.evenements == undefined) {
            this.evenements=new Array();
        }
        this.classe = parametres["classe"];

        this.div = document.createElement ("div");
        this.ID=parametres["id"];

        var html_icones = this.genere_icones(icones);
        var html_aide = this.genere_aide (parametres["aide"]);
        this.div.innerHTML = "<table class='"+this.classe+"' role='table_generale'><tr><td class='"+this.classe+"' role='cellule_nom'>"+nom+"</td><td class='"+this.classe+"' role='cellule_description'>"+description+html_aide+"</td><td class='"+this.classe+"' role='cellule_input'><div><input role='input' type='text' value=\""+valeur_affiche+"\"/><div></div></div></td><td class='"+this.classe+"' role='cellule_toutes_icones'><table class='"+this.classe+"' role='tableau_icones'><tr>"+html_icones+"</tr></table></td></tr></table>";
        this.bool_post_init = 1; // pour appeler la m�thode post_init() quand tous les �l�ments auront �t� ajout�s au DOM
        return (this);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // M�thode appel�e apr�s init(), une fois que le <div> a �t� ajout� au DOM
    this.post_init = function() {
        this.input=this.div.getElementsByTagName("input").item(0);
        this.conteneur=this.input.nextSibling;
        
        
        this.oDS = new YAHOO.util.XHRDataSource(this.ws_url);
        this.oDS.responseType = YAHOO.util.DataSourceBase.TYPE_JSARRAY;
        this.oDS.responseSchema = {fields : ["nom", "id"]}; // attention BUG YUI : les donn�es ne seront pas accessibles via ["xxx"] mais [0], [1]...
        this.oDS.maxCacheEntries = 5;
        this.oAC = new YAHOO.widget.AutoComplete(this.input, this.conteneur, this.oDS);
        this.oAC.queryQuestionMark=false;
        if (this.forceSelection != undefined) {
            this.oAC.forceSelection=parseBool(this.forceSelection);
        }
        if (this.maxResultsDisplayed != undefined) {
            this.oAC.maxResultsDisplayed=parseInt(this.maxResultsDisplayed);
        }
        if (this.minQueryLength != undefined) {
            this.oAC.minQueryLength=parseInt(this.minQueryLength);
        }
        if (this.queryDelay != undefined) {
            this.oAC.queryDelay=parseInt(this.queryDelay);
        }
        
        this.genere_evenements(); // les �v�nements sont g�r�s diff�rement avec un autocomplete qu'avec les champs normaux
        this.test_valeur();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Surcharge la validation "classique"
    this.validation = function (sType, aArgs, obj_this) {
        //alert ("validation : "+obj_this.ID);
        var myAC = aArgs[0]; // reference back to the AC instance
        var elLI = aArgs[1]; // reference to the selected LI element
        var oData = aArgs[2]; // object literal of selected item's result data
        var param = new Object();
        param["action"]="validation";
        param["valeur"]=oData[1];
        param["intitule"]=oData[0];
        param["ID_element"]=obj_this.ID;
        formulator.transaction (param);
    }
    

    
    // Quand le texte tap� ne correspond pas aux suggestions, mais qu'on veut quand m�me le valider simplement sans passer par un wizard
    this.validation_creation = function (sType, aArgs, obj_this) {
        //alert ("validation_creation");
        var myAC = aArgs[0]; // reference back to the AC instance
        var valeur=myAC.getInputEl().value;
        //alert ("valeur : "+valeur);
        var param = new Object();
        param["action"]="validation";
        param["valeur"]=valeur;
        param["intitule"]=valeur;
        param["ID_element"]=obj_this.ID;
        formulator.transaction (param);
    }
    
    
    // Quand le texte tap� ne correspond pas aux suggestions, mais qu'on ne peut pas utiliser de wizard
    // FAUTE DE MIEUX POUR L'INSTANT
    this.validation_annulation = function (sType, aArgs, obj_this) {
        //alert ('validation_annulation !');
        var myAC = aArgs[0]; // reference back to the AC instance
        var chaine = aArgs[1]; // reference to the selected LI element
        if (chaine=="") {
            return (true);
        }
        if (chaine == obj_this.valeur) {
            obj_this.set_valeur(chaine);
            return (true);
        }
        obj_this.set_valeur("");
        alert ("@&Cette vedette n'existe pas. Vous devez en creer une nouvelle");
        
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Surcharge la gestion des �v�nements "classiques"
    this.genere_evenements = function () {
        for (type_evenement in this.evenements) {
            var action = this.evenements[type_evenement];
            action2=this.formate_lien(action); // on remplace les motifs par les valeurs
            var ev3="this.oAC."+type_evenement+".subscribe("+action2+", this);"; // il faut passer this en param�tre, car apparemment, YUI perd la r�f�rence � this dasn la gestion des �v�nements
            //var ev3="this.oAC."+type_evenement+".subscribe(this.validation, this);";
            //alert (ev3);
            try {
                eval (ev3);
            } catch (err) {
                alert ("Erreur : "+err+" - "+ev3);
            }
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Surcharge la suppression
    // on fait le m�nage avant la suppression
      this.delete_self = function () {
        //alert ("delete_self : "+this.ID);
        this.oAC.itemSelectEvent.unsubscribeAll();
        this.oAC.destroy();
        this.oDS=null;
        this.oAC=null;
        return (true);
    }
    
  
    this.wizard_creation_notice = function (sType, aArgs, obj_this) {
        var myAC = aArgs[0]; // reference back to the AC instance
        var chaine = aArgs[1]; // reference to the selected LI element
        
        if (chaine=="") {
            obj_this.set_valeur(obj_this.valeur);
            return (true);
        }
        if (chaine == obj_this.valeur) {
            obj_this.set_valeur(chaine);
            return (true);
        }
        if (confirm("la vedette "+chaine+" n'existe pas. Voulez-vous la creer ?")) {
            var param = new Object();
            param["action"]="wizard_creation_notice";
            param["chaine"]=chaine;
            param["ID_element"]=obj_this.ID;
            formulator.transaction (param);
        } else {
            obj_this.set_valeur(obj_this.valeur);
        }
    }
    
  
} // fin de la classe


///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///// CLASS tvs_choix_multiple
///// Element de formulaire contenant une liste de choix. On peut en choisir un ou plusieurs
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function tvs_choix_multiple (formulator_array) {
    
    // H�ritage
    this.parent = tvs_element_formulator;
    this.parent(formulator_array);

    // VARIABLES
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne l'objet sous-champ (this)
    // on peut acc�der au <div> contenant l'input par XXX.div
    
    
    this.init = function (parametres) {
        //var ID = parametres["ID"]; // ID du champ
        var bool_selection_multiple=parametres["bool_selection_multiple"]; // si vaut 1, on peut s�lectionner plusieurs �l�ments
        var nom = parametres["nom"];
        var description = parametres["intitule"];
        var readonly = "";
        if (parametres["readonly"] == "readonly") {
            readonly="readOnly='readonly'";
        }
        var valeur = parametres["valeur"];
        var auto_plugin=parametres["auto_plugin"];
        description = "<a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+auto_plugin+"/parametres' target='_blank' title='parametrer cet element dans le registre'>"+description+"</a>";
       
        var icones = parametres["icones"]; // array
        var evenements = parametres["evenements"]; // array
        var liste_choix = parametres["liste_choix"];
        this.test_valeur_champ = parametres["test_valeur_champ"];

        if (valeur == undefined) {
            if (parametres["valeur_defaut"] != undefined) {
                valeur=parametres["valeur_defaut"];
            } else {
                valeur="";
            }
        }
        //valeur=escape_guillemets(valeur);
        var ancienne_valeur=valeur;
        
        if (icones == undefined) {
            icones=new Array();
        }
        if (evenements == undefined) {
            evenements=new Array();
        }
        var html_evenements = this.genere_evenements(evenements);
        this.classe = parametres["classe"];
        
        // ON DETERMINE LA LISTE DE CHOIX
        if (liste_choix == undefined) {
            liste_choix=new Array();
        }
        var html_select="<table class='"+this.classe+"' role='select'><tr>"; // html pour tout le select
        
        for (choix in liste_choix) {
            var intitule=liste_choix[choix]["intitule"];
            var valeur=liste_choix[choix]["valeur"];
            
            if (valeur == "_void") {
                valeur="";
            }
            var valeur_affiche=escape_guillemets(valeur);
            
            html_select+="<td><input "+html_evenements+" type='checkbox' value='"+valeur_affiche+"'>"+intitule+"</td>";
        }
        html_select+="</tr></table>";
        
        
        
        //icones=this.ajoute_icones_defaut(icones);

        this.div = document.createElement ("div");
        this.ID=parametres["id"];
        var html_icones = this.genere_icones(icones);
        
        var html_aide = this.genere_aide (parametres["aide"]);
        this.div.innerHTML = "<table class='"+this.classe+"' role='table_generale'><tr><td class='"+this.classe+"' role='cellule_nom'>"+nom+"</td><td class='"+this.classe+"' role='cellule_description'>"+description+html_aide+"</td><td class='"+this.classe+"' role='cellule_input'>"+html_select+"</td><td class='"+this.classe+"' role='cellule_toutes_icones'><table class='"+this.classe+"' role='tableau_icones'><tr>"+html_icones+"</tr></table></td></tr></table>";
        this.input=this.div.getElementsByTagName("input"); // attention ici c'est une liste d'input auxquels on fait r�f�rence par this.input.item(1,2,3...)
        this.set_valeur(ancienne_valeur);
        this.test_valeur();
        return (this);
    }
    
    
    this.set_valeur = function (texte) {
        var nb_select=this.input.length;
        var elements=texte.split("|");
        for (idx=0 ; idx < nb_select ; idx++) {
            var select=this.input[idx];
            for (element in elements) {
                if (select.value==elements[element]) {
                    select.checked=true;
                }
            }
        }
        this.validation();
        return("");    
    }
    
    this.get_valeur = function () {
        var retour="";
        var nb_select=this.input.length;
        for (idx=0 ; idx < nb_select ; idx++) {
            var select=this.input[idx];
            var valeur=select.value;
            var checked=select.checked;
            if (checked==true) {
                if (retour != "") {
                    retour+="|";
                }
                retour+=valeur;
            }
        }
        return (retour);
    }
    
    this.validation = function () {
        var param = new Object();
        param["action"]="validation";
        param["valeur"]=this.get_valeur();
        param["ID_element"]=this.ID;
        formulator.transaction (param);
    }
   
} // fin de la classe