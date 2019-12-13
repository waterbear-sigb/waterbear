function tvs_formulator (element_parent, formulator) {
    // VARIABLES
    this.idx=0;
    this.element_parent=element_parent;
    this.liste_objets=new Object(); // dictionnaire de tous les objets (onglets, conteneurs et sous-champs) identifiés par un ID. on donc ID => objet (onglet ou tvs_conteneur ou tvs_element_formulator(ou hérités)) 
    this.liste_objets_infos=new Object(); // comme le précédent, mais associe des infos aux ID (type (onglet|champ|ss_champ), nom (pour les champs ou ss-champs))
    this.formulator=formulator; // nom donné dans le script à la vraible contenant l'objet formulator
    this.ws_path="";
    this.id_operation="";
    this.id_notice="";
    this.infos_init;
    this.TABS; // objet YUI tabView
    this.message; // objet YUI contenant des messages... il ne peut y en avoir qu'1 ouvert
    this.icones_ss_champ_defaut; // icones par défaut pour les sous-champs
    this.icones_champ_defaut; // icones par défaut pour les champs
    this.classe;
    this.id_appel=""; // si != 0 => ID d'appel : la page a été ouverte par une autre, à laquelle il faudra renvoyer l'ID_notice
    this.idx_appel=1; // compteur des appels. S'incrémente à chaque appel
    this.liste_appels=new Array();
    this.elements_post_init = new Array(); // éléments du formulator dont il faudra appeler la méthode post_init() une fois que tous les éléments auront été ajoutés au DOM
    
    this.bool_transaction_en_cours=0; // vaut 1 si une transaction est en cours
    this.bool_close_window=0; // si vaut 1 on fermera la fenêtre à la prochaine transaction
    this.pile_transactions=new Array(); // pile des transactions en attente d'être traitées (si on en lance plusieurs en même temps)
    
    this.bool_modif=0; // si vaut 1, des actions n'ont pas été enregistrées
    
    this.actions_fin = new Array();
    
    this.liste_masques;
    this.masque_actuel;
    
    this.auto_grille;
    
    // METHODES
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // TEMP !!! 
    this.get_id = function () {
        this.idx++;
        return ("tvs_formulator_id_"+this.idx);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.focus_onglet = function (onglet) {
        //var ID_onglet=this.TABS.get('activeIndex');
        //alert (onglet.target);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.genere_formulaire = function (onglets) {
        // 1) on crée les onglets
        var tabView = new YAHOO.widget.TabView();
        for (id_onglet in onglets) {
            var infos_onglet=onglets[id_onglet];
            var tab=new YAHOO.widget.Tab({
                label: infos_onglet["intitule"],
                content: "<div id='"+infos_onglet["id"]+"'></div>"
            });
            
            tabView.addTab(tab);
            tab.addListener('click', this.focus_onglet);
            
        } // fin du pour chaque onglet
        tabView.appendTo(this.element_parent.id);
        tabView.set('activeIndex', 0);
        this.TABS=tabView;
        
        // 2) on les peuple
        for (id_onglet in onglets) {
            this.genere_onglet(onglets[id_onglet]); 
        }
        
        // 3) on appelle les méthodes post_init() des éléments quand c'est nécessaire
        this.lance_post_init();
        if (this.masque_actuel != "") {
            this.applique_masque_glob();
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.delete_formulaire = function () {
        this.TABS=undefined;
        this.liste_objets=new Object();
        this.liste_objets_infos=new Object();
        this.element_parent.innerHTML="";
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.genere_onglet = function (infos_onglet) {
        var tab=document.getElementById(infos_onglet["id"]); // pour ancrer le conteneur dans l'onglet
        var conteneur = new tvs_conteneur(tab, this.classe+"_onglet", this.classe+"_conteneur_champ");
        var tmp = new Object();
        tmp["formulator"]=this.formulator;
        tmp["element"]=infos_onglet["id"];
        tmp["conteneur"]=""; // pas de conteneur
        conteneur.set_formulator(tmp);
        this.liste_objets[infos_onglet["id"]]=conteneur; // Conteneurs "onglets"
        this.liste_objets_infos[infos_onglet["id"]]=new Object();
        this.liste_objets_infos[infos_onglet["id"]]["type"]="onglet";
        for (id_champ in infos_onglet["champs"]) { // pour chaque champ
            var div = document.createElement("div");
            var contenu = this.genere_champ (infos_onglet["champs"][id_champ], div, infos_onglet["id"]);
            conteneur.add_element(div, infos_onglet["champs"][id_champ]["id"]); 
            
            this.liste_objets[infos_onglet["champs"][id_champ]["id"]]=contenu; // Conteneurs "champs"
            this.liste_objets_infos[infos_onglet["champs"][id_champ]["id"]]=new Object();
            this.liste_objets_infos[infos_onglet["champs"][id_champ]["id"]]["type"]="champ"
            this.liste_objets_infos[infos_onglet["champs"][id_champ]["id"]]["nom"]=infos_onglet["champs"][id_champ]["nom"];
        }
    }   
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.genere_champ = function (infos_champ, div, id_conteneur) {
        var conteneur = new tvs_conteneur(div, this.classe+"_champ", this.classe+"_ss_champ");
        var tmp = new Object();
        tmp["formulator"]=this.formulator;
        tmp["element"]=infos_champ["id"];
        tmp["conteneur"]=id_conteneur;
        conteneur.set_formulator(tmp);
        for (id_ss_champ in infos_champ["ss_champs"]) {
            var tmp = new Object();
            tmp["formulator"]=this.formulator;
            tmp["element"]=infos_champ["ss_champs"][id_ss_champ]["id"];
            tmp["conteneur"]=infos_champ["id"]; 
            if (typeof(infos_champ["ss_champs"][id_ss_champ]["icones"])!='object') {
                infos_champ["ss_champs"][id_ss_champ]["icones"]=this.icones_ss_champ_defaut;
            } 
            var contenu = this.genere_ss_champ (infos_champ["ss_champs"][id_ss_champ], tmp);
            conteneur.add_element(contenu.div, infos_champ["ss_champs"][id_ss_champ]["id"]); 
            this.liste_objets[infos_champ["ss_champs"][id_ss_champ]["id"]]=contenu; 
            this.liste_objets_infos[infos_champ["ss_champs"][id_ss_champ]["id"]]=new Object();
            this.liste_objets_infos[infos_champ["ss_champs"][id_ss_champ]["id"]]["type"]="ss_champ";
            this.liste_objets_infos[infos_champ["ss_champs"][id_ss_champ]["id"]]["nom"]=infos_champ["ss_champs"][id_ss_champ]["nom"];
            this.liste_objets_infos[infos_champ["ss_champs"][id_ss_champ]["id"]]["nom_champ"]=infos_champ["nom"];
        }
        if (typeof(infos_champ["icones"])!='object') {
            infos_champ["icones"]=this.icones_champ_defaut;
        } 
        //infos_champ["icones"]=this.ajoute_icones_defaut (infos_champ["icones"]);
        var auto_plugin=infos_champ["auto_plugin"];
        var nom_champ=infos_champ["nom"];
        var intitule_champ=infos_champ["intitule"];
        intitule_champ="<a href='bib.php?module=admin/registre&acces_direct=Registre/profiles/defaut/plugins/plugins/"+auto_plugin+"/parametres' target='_blank' title='parametrer cet element dans le registre'>"+intitule_champ+"</a>";
        conteneur.add_menu("<b>"+nom_champ+"</b> - "+intitule_champ, "déprécié", infos_champ["icones"]);
        return (conteneur);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.genere_ss_champ = function (infos_ss_champ, formulator) {
        if (infos_ss_champ["classe"]==undefined) {
            infos_ss_champ["classe"]=this.classe+"_elements_defaut";
        }
        if (infos_ss_champ["type"]=="textbox") {
            retour=this.genere_textbox(infos_ss_champ, formulator);
        } else if (infos_ss_champ["type"]=="textarea") {
            retour=this.genere_textarea(infos_ss_champ, formulator);
        } else if (infos_ss_champ["type"]=="select") {
            retour=this.genere_select(infos_ss_champ, formulator);
        } else if (infos_ss_champ["type"]=="autocomplete") {
            retour=this.genere_autocomplete(infos_ss_champ, formulator);
        } else if (infos_ss_champ["type"]=="choix_multiple") {
            retour=this.genere_choix_multiple(infos_ss_champ, formulator);
        }
        
        if (retour.bool_post_init == 1) {
            this.elements_post_init.push(retour.ID);
        }
 
        return (retour);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.init = function () {
        affiche_waiting(true);
        var sUrl = this.ws_path+"&operation=init_formulator&ID_operation="+this.id_operation+"&ID_notice="+this.id_notice;
        var this_formulator = this; // nécessaire pour que la fonction callback puisse accéder à this
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
                affiche_waiting(false);
				alert (oResults.erreur);
			} else {
				this_formulator.infos_init = oResults["resultat"];
                this_formulator.icones_ss_champ_defaut=oResults["resultat"]["icones_ss_champ_defaut"];
                this_formulator.icones_champ_defaut=oResults["resultat"]["icones_champ_defaut"];
                this_formulator.genere_formulaire(this_formulator.infos_init["onglets"]);
                // on définit des intitulés utilisés par les fonctions
                for (intitule in oResults["resultat"]["intitules"]) {
                    var valeur=oResults["resultat"]["intitules"][intitule];
                    intitules[intitule]=valeur;
                }
                affiche_waiting(false);
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            affiche_waiting(false);
            alert ("Echec lors de la recuperation des donnees");
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 20000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_ws_path = function (url) {
        this.ws_path=url;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_id_operation = function (id) {
        this.id_operation=id;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_classe_css = function (classe) {
        this.classe=classe;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_id_notice = function (id_notice) {
        this.id_notice=id_notice;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    this.set_appel = function (id_appel) {
        this.id_appel=id_appel;
    }
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    // !! IL FAUT UN OBJET TEXTBOX. ON RETOURNE UN <DIV> pour ajouter au conteneur
    // !! MAIS on ajoute l'objet formulaire à liste_objets
    
    this.genere_textbox = function(parametres, formulator) {
        var textbox = new tvs_simple_textbox(formulator);
        var retour = textbox.init(parametres);
        return (retour);
    }
    
    this.genere_textarea = function(parametres, formulator) {
        var textarea = new tvs_simple_textarea(formulator);
        var retour = textarea.init(parametres);
        return (retour);
    }
    
    this.genere_select = function(parametres, formulator) {
        var select = new tvs_simple_select(formulator);
        var retour = select.init(parametres);
        return (retour);
    }
    
    this.genere_choix_multiple = function(parametres, formulator) {
        var select = new tvs_choix_multiple(formulator);
        var retour = select.init(parametres);
        return (retour);
    }
    
    this.genere_autocomplete = function(parametres, formulator) {
        var autocomplete = new tvs_autocomplete(formulator);
        var retour = autocomplete.init(parametres);
        return (retour);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cette méthode va traiter toutes les actions sur des objets du formulator
    
    this.transaction = function (parametres) {
        // Si on ne passe pas de paramètres, on regarde s'il y a des transactions à récupérer dans pile_transactions
        if (parametres == "") {
            if (this.pile_transactions.length >0) {
                parametres=this.pile_transactions.shift();
            } else {
                this.bool_transaction_en_cours=0;
                affiche_waiting(false);
                if (this.bool_close_window==1) {
                    if (window.opener==null || window.opener.closed==true) {
                        history.back();
                    } else {
                        window.close();
                    }
                }
                return (true);
            }
        }
                
        affiche_waiting(true);
        
        // On regarde s'il n'y a pas déjà une transaction en cours
        if (this.bool_transaction_en_cours == 1) {
            this.pile_transactions.push(parametres);
            return (true);
        }
        
        this.bool_transaction_en_cours=1;
        
        var sUrl = this.ws_path+"&operation=action&ID_operation="+this.id_operation+"&auto_grille="+this.auto_grille;
        if (isString(parametres)) {
            sUrl+="&"+parametres;
        } else {
            for (clef_param in parametres) {
                parametres[clef_param]=encodeURIComponent(parametres[clef_param]);
                sUrl+="&"+clef_param+"="+parametres[clef_param];
            }
        }
        
        //alert (sUrl);
        /**
        if (parametres["action"] == "enregistrer_notice") {
            this.bool_modif=0;
        } else {
            this.bool_modif=1;
        }**/
        if (sUrl.indexOf("maj_bool_modif") != -1) {
            this.maj_bool_modif(0);
        } else {
            this.maj_bool_modif(1);
        }
        var this_formulator = this; // nécessaire pour que la fonction callback puisse accéder à this
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var param = new Object();
                var bool_param=0;
                for (idx in oResults.resultat) {
                    if (! isString(oResults.resultat[idx])) { // si Objet => parametres
                        param=oResults.resultat[idx];
                        bool_param=1;
                    } else { // Si String => méthode
                        eval (oResults.resultat[idx]);
                        bool_param=0;
                    }
				    
                }
			}
            this_formulator.bool_transaction_en_cours=0;
            affiche_waiting(false);
            this_formulator.transaction("");
        },
        
        // ECHEC
        failure: function(oResponse) {
            this_formulator.bool_transaction_en_cours=0;
            affiche_waiting(false);
            this_formulator.transaction("");
            alert (intitules["erreur_connexion"]);
            oResponse.argument.fnLoadComplete();
        },
        
        // ARGUMENTS
        argument: {
        },
        
        // AUTRES INFOS
        timeout: 70000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Suppression d'un élément
    
    this.delete_element = function (ID_element, ID_parent) {
        //alert ("le parent "+ID_parent+" supprime l'élement "+ID_element);
        try {
            this.liste_objets[ID_element].delete_self(); // si l'objet doit faire qqchse avant d'être supprimé
        } catch (err) {
        }
        this.liste_objets[ID_parent].delete_element(ID_element);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Ajouter ss_champ
    
    this.add_ss_champ = function (param) {
        var ID=param.ID;
        var idx=param.idx;
        var ID_rempl=param.ID_rempl;
        var ID_champ=param.ID_champ;
        var ss_champ=param.ss_champ;
        if (typeof(ss_champ["icones"])!='object') {
            ss_champ["icones"]=this.icones_ss_champ_defaut;
        } 
        var tmp = new Object();
        var conteneur=this.liste_objets[ID_champ];
        tmp["formulator"]=this.formulator;
        tmp["element"]=ID;
        tmp["conteneur"]=ID_champ; 
        var contenu = this.genere_ss_champ (ss_champ, tmp);
        conteneur.add_element(contenu.div, ID); 
        //if (contenu.bool_post_init == 1) { --> non car on lance déjà lance_post_init() à la fin
        //   contenu.post_init();
        //}
        
        this.liste_objets[ID]=contenu;
        
        this.liste_objets_infos[ID]=new Object();
        this.liste_objets_infos[ID]["type"]="ss_champ";
        this.liste_objets_infos[ID]["nom"]=ss_champ["nom"];
        this.liste_objets_infos[ID]["nom_champ"]=this.liste_objets_infos[ID_champ]["nom"];
        
        if (ID_rempl != "") {
            conteneur.move_element(ID, ID_rempl)
        }
        this.applique_masque_champ(ID_champ, true);
        this.lance_post_init();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Ajouter champ
    
    this.add_champ = function (param) {
        var ID=param.ID;
        var idx=param.idx;
        var ID_rempl=param.ID_rempl;
        var ID_onglet=param.ID_onglet;
        var champ=param.champ;
        
        var div = document.createElement("div");
        var contenu = this.genere_champ (champ, div, this.liste_objets[ID_onglet]);
        this.liste_objets[ID_onglet].add_element(div, ID);
        this.liste_objets[ID]=contenu; 
        
        this.liste_objets_infos[ID]=new Object();
        this.liste_objets_infos[ID]["type"]="champ";
        this.liste_objets_infos[ID]["nom"]=champ["nom"];
                
        if (ID_rempl != "") {
            this.liste_objets[ID_onglet].move_element(ID, ID_rempl)
        }
        
        this.applique_masque_champ(ID, true);
        this.lance_post_init();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Afficher la liste des champs à insérer
    
    this.affiche_liste_champs = function (param) {
        var str = "";
        for (idx in param) {
            str += "<a href=\"javascript:"+this.formulator+".ajouter_champ ('"+param[idx].auto_plugin + "');\">"+param[idx].nom + " : "+param[idx].intitule + " </a><br> ";
        }
        this.message = new YAHOO.widget.Panel("panel2", { width:"320px", visible:false, draggable:true, close:true, modal:true, fixedcenter:true } ); 
        this.message.setHeader(intitules["l_ajouter_champ"]); 
        this.message.setBody(str); 
        //var verif = this.message.render("test"); 
        var verif = this.message.render(document.body); 
        this.message.center();
        this.message.show();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Afficher la liste des champs à insérer
    
    this.affiche_liste_ss_champs = function (ID_element, param) {
        var str = "";
        for (idx in param) {
            str += "<a href=\"javascript:"+this.formulator+".ajouter_ss_champ ('"+ID_element+"','"+param[idx].auto_plugin + "');\">"+param[idx].nom + " : "+param[idx].intitule + " </a><br> ";
        }
        this.message = new YAHOO.widget.Panel("panel2", { width:"320px", visible:false, draggable:true, close:true, modal:true, fixedcenter:true } ); 
        this.message.setHeader("a"+intitules["l_ajouter_ss_champ"]); 
        this.message.setBody(str); 
        //var verif = this.message.render("test"); 
        var verif = this.message.render(document.body); 
        this.message.center();
        this.message.show();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Afficher la liste des notices
    
    this.afficher_notice = function () {
        var param = new Object();
        param["action"]="afficher_notice";
        this.transaction (param);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Enregistrer la notice
    
    this.enregistrer = function () {
        // traitements de fin
        for (idx in this.actions_fin) {
            var tmp_action=this.actions_fin[idx];
            var param = new Object();
            param["action"]=tmp_action;
            this.transaction (param);
        }
        
        //enregistrer
        var param = new Object();
        param["action"]="enregistrer_notice";
        param["maj_bool_modif"]=0; // pour indiquer qu'on pourra fermer la notice sans message de validation
        this.transaction (param);
        //this.maj_bool_modif(0);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Supprimer la notice 
    
    this.supprimer = function () {
        //supprimer
        if (confirm ("Voulez-vous supprimer cette notice ?")) {
            var param = new Object();
            param["action"]="supprimer_notice";
            param["maj_bool_modif"]=0; // pour indiquer qu'on pourra fermer la notice sans message de validation
            this.bool_close_window=1;
            this.transaction (param);
            //window.close();
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // lancer les fonctions post_init des éléments
    
    this.lance_post_init = function () {
        for (idx in this.elements_post_init) {
            var id_elem = this.elements_post_init[idx]; // ID de l'élément
            this.liste_objets[id_elem].post_init();
        }
        this.elements_post_init=new Array();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    // Fonctions spécifiques pour manipuler les champs du formulator 
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // monte l'élément d'un cran
    this.champ_descendre = function (ID_element) {
        var param = new Object();
        param["action"]="descendre";
        param["ID_element"]=ID_element;
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // monte l'élément d'un cran
    this.champ_monter = function(ID_element) {
        var param = new Object();
        param["action"]="monter";
        param["ID_element"]=ID_element;
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // supprime l'élément'
    this.champ_supprimer = function(ID_element) {
        if (! confirm (intitules["l_suppr_valider"])) {
            return (true);
        }
        var param = new Object();
        param["action"]="supprimer";
        param["ID_element"]=ID_element;
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ajoute un ss-champ
    this.ajouter_ss_champ = function (ID_element, auto_plugin) {
        var param = new Object();
        param["action"]="ajouter_ss_champ";
        param["auto_plugin"]=auto_plugin;
        param["ID_element"]=ID_element;
        this.transaction (param);
        this.message.destroy();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ajoute un champ
    this.ajouter_champ = function (auto_plugin) {
        var param = new Object();
        param["action"]="ajouter_champ";
        param["auto_plugin"]=auto_plugin;
        param["idx_onglet"]=this.TABS.get('activeIndex');
        this.transaction (param);
        this.message.destroy();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Récupérer la liste des champs qu'on peut ajouter
    this.get_liste_champs = function () {
        var param = new Object();
        param["action"]="get_liste_champs";
        param["idx_onglet"]=this.TABS.get('activeIndex');
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Récupérer la liste des sous-champs qu'on peut ajouter
    this.get_liste_ss_champs = function(ID_element) {
        var param = new Object();
        param["action"]="get_liste_ss_champs";
        param["ID_element"]=ID_element;
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Ouvrir un champ de lien
    this.ouvrir_champ_lien = function(ID_element) {
        var param = new Object();
        param["action"]="ouvrir_champ_lien";
        param["ID_element"]=ID_element;
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Une fois qu'on a enregistré la notice... on met éventuellement à jour la page appelante
    this.post_enregistrer_notice = function(ID_notice) {
        //alert ("notice créée avec l'ID "+ID_notice);
        if (this.id_appel != 0 && this.id_appel != "") {
            window.opener.callback_appel (this.id_appel, ID_notice);
            window.close();
        }
    }
    
        
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Lorsqu'on appelle une autre page (pour créer, rechercher ou modifier une autorité par exemple)
    this.appel = function(page, ID_element, action) {
        var idx_appel=this.idx_appel;
        this.idx_appel++;
        this.liste_appels[idx_appel]=new Object();
        this.liste_appels[idx_appel]["ID_element"]=ID_element;
        this.liste_appels[idx_appel]["action"]=action;
        
        window.open(page+"&id_appel="+idx_appel);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Lorsqu'on appelle une autre page (pour créer, rechercher ou modifier une autorité par exemple)
    this.callback_appel = function(id_appel, id_notice) {
        var ID_element=this.liste_appels[id_appel]["ID_element"];
        var action=this.liste_appels[id_appel]["action"];
        var param = new Object();
        param["action"]=action;
        param["ID_element"]=ID_element;
        param["ID_notice_liee"]=id_notice;
        this.transaction (param);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Applique un masque sur l'ensemble du formulaire
    
    this.applique_masque_glob = function () {
        
        //var masque=({"champs" : {"700" : {"masquer" : "1"}, "200" : {"masquer" : "0", "ss_champs" : {"a" : {"masquer" : "1"}}}}});
        if (this.masque_actuel != "aucun") {
            var masque=this.liste_masques["masques"][this.masque_actuel];
        }
        //alert (print_r(masque, 0, "nl"));
        for (id_objet in this.liste_objets_infos) {
            var type=this.liste_objets_infos[id_objet]["type"];
            if (type == "champ") { // CHAMP
                if (this.masque_actuel == "aucun") {
                    this.liste_objets[id_objet].show_element();
                    continue;
                }
                var nom_champ=this.liste_objets_infos[id_objet]["nom"];
                try {
                    if (masque["champs"][nom_champ] == undefined) {
                        this.liste_objets[id_objet].show_element();
                    } else if (masque["champs"][nom_champ]["masquer"] == "1") {
                        this.liste_objets[id_objet].hide_element();
                    } else {
                        this.liste_objets[id_objet].show_element();
                    }
                } catch (err) {
                    alert ("erreur application masque champ : "+nom_champ);
                }
            } else if (type == "ss_champ") { // SS-CHAMP
                if (this.masque_actuel == "aucun") {
                    this.liste_objets[id_objet].show_element();
                    continue;
                }
                var nom_ss_champ=this.liste_objets_infos[id_objet]["nom"];
                var nom_champ=this.liste_objets_infos[id_objet]["nom_champ"];
                try {
                    if (masque["champs"][nom_champ] == undefined) {
                        this.liste_objets[id_objet].show_element();
                    } else if (masque["champs"][nom_champ]["ss_champs"]==undefined) {
                        this.liste_objets[id_objet].show_element();
                    } else if (masque["champs"][nom_champ]["ss_champs"][nom_ss_champ]==undefined){
                        this.liste_objets[id_objet].show_element();
                    }else if (masque["champs"][nom_champ]["ss_champs"][nom_ss_champ]["masquer"] == "1") {
                        this.liste_objets[id_objet].hide_element();
                    } else {
                        this.liste_objets[id_objet].show_element();
                    }
                    
                    
                } catch (err) {
                    alert ("erreur application masque ss-champ : "+nom_champ+"$"+nom_ss_champ);
                }
                
                try {
                // on attribue une valeur au ss-champ uniquement pour une création de notice
                    if (masque["champs"][nom_champ]["ss_champs"][nom_ss_champ]["valeur"] != "" && masque["champs"][nom_champ]["ss_champs"][nom_ss_champ]["valeur"] != undefined && this.id_notice == "") {
                        this.liste_objets[id_objet].set_valeur(masque["champs"][nom_champ]["ss_champs"][nom_ss_champ]["valeur"]);
                    }
                } catch (err) { }
            }
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Applique un masque sur un champ (en fait tous les champs de même nom)
    
    this.applique_masque_champ = function (ID_champ, bool) {
        //var masque=({"champs" : {"700" : {"masquer" : "1"}, "200" : {"masquer" : "0", "ss_champs" : {"a" : {"masquer" : "1"}}}}});
        if (this.masque_actuel != "aucun") {
            var masque=this.liste_masques["masques"][this.masque_actuel];
        } else {
            var masque=this.liste_masques["masques"][this.masque_defaut];
        }
        var nom_champ_test=this.liste_objets_infos[ID_champ]["nom"];
        for (id_objet in this.liste_objets_infos) {
            var type=this.liste_objets_infos[id_objet]["type"];
            if (type == "ss_champ") {
                var nom_ss_champ=this.liste_objets_infos[id_objet]["nom"];
                var nom_champ=this.liste_objets_infos[id_objet]["nom_champ"];
                if (nom_champ == nom_champ_test) {
                    if (bool==false) {
                        this.liste_objets[id_objet].show_element();
                        continue;
                    }
                    try {
                        if (masque["champs"][nom_champ]["ss_champs"][nom_ss_champ]["masquer"] == "1") {
                            this.liste_objets[id_objet].hide_element();
                        }
                    } catch (err) {
                        
                    }
                }
            }
        }  
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Affiche la liste des masques
    
    this.affiche_liste_masques = function () {
        var str = "";
        for (nom_masque in this.liste_masques["masques"]) {
            var intitule_masque=this.liste_masques["masques"][nom_masque]["intitule"];
            str += "<a href=\"javascript:"+this.formulator+".set_masque_actuel ('"+nom_masque+"');\">"+ intitule_masque + " </a><br> ";
        }

        this.message = new YAHOO.widget.Panel("panel2", { width:"320px", visible:false, draggable:true, close:true, modal:true, fixedcenter:true } ); 
        this.message.setBody(str); 

        //var verif = this.message.render("test"); 
        var verif = this.message.render(document.body); 
        this.message.center();
        this.message.show();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // MAJ le masque actuel
    
    this.set_masque_actuel = function (nom_masque) {
        this.masque_actuel=nom_masque;
        this.applique_masque_glob();
        if (this.message != undefined) {
            this.message.destroy();
        }
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // MAJ le masque actuel
    
    this.ouvrir_page_special = function (url) {
        url+=this.id_notice;
        window.open(url);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // MAJ bool_modif
    
    this.maj_bool_modif = function (val) {
        this.bool_modif=val;
        
        var liste_imgs=document.getElementsByTagName("img");
        var ln=liste_imgs.length;
        
        for (idx_img=0;idx_img<ln;idx_img++) {
            var img=liste_imgs[idx_img];
            var src=img.getAttribute('src');
            
            if (src=="IMG/icones/disk.png" && this.bool_modif==1) {
                img.setAttribute("src", "IMG/icones/ico_save.gif");
            } else if (src=="IMG/icones/ico_save.gif" && this.bool_modif==0) {
                img.setAttribute("src", "IMG/icones/disk.png");
            }
            
        }
        
        
        //alert ("maj_bool_modif : "+this.bool_modif);
    }
    
    
    
} // fin de la classe