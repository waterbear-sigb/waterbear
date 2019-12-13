function tvs_panierator () {
// toto
    this.type_objet=""; 
    this.chemin_parent="";  
    this.ID_parent="";
    this.ws_url=""; 
    this.ID_panier_en_cours="";
    this.type_panier_en_cours="";
    this.nom_panier_en_cours="";
    this.tableau_node;
    this.ID_panier_modif="";
    this.ID_appel="";
    
    this.div_liste=document.getElementById("div_navigation_paniers");
    this.div_nom=document.getElementById("input_nom_panier");
    this.div_ID=document.getElementById("input_ID_panier_modif");
    this.div_description=document.getElementById("textarea_description_panier");
    this.div_chemin_parent=document.getElementById("input_chemin_parent");
    this.div_panier_en_cours=document.getElementById("input_panier_en_cours");
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////

    this.init_variable = function(nom, valeur) {
        var str="this."+nom+" = '"+valeur+"';";
        eval (str);
    }   
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.init_variable_json = function(nom, valeur) {
        var tmp=eval("(" + valeur + ")");
        var str="this."+nom+" = tmp;";
        eval (str);
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.create_node = function (type) {
        var sUrl = this.ws_url+"&operation=create_node&type="+type+"&type_objet="+this.type_objet+"&chemin_parent="+this.chemin_parent;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                this_panierator.affiche_liste(oResults.resultat["liste"]);
                this_panierator.clique_node(oResults.resultat["ID"]);
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.get_liste = function () {
        var sUrl = this.ws_url+"&operation=get_liste&type_objet="+this.type_objet+"&chemin_parent="+this.chemin_parent;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                this_panierator.affiche_liste(oResults.resultat["liste"]);
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }
    
     ////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
     this.affiche_liste = function (liste) {
        var tmp="<table width='100%'>";
        if (this.chemin_parent != "") {
            tmp+="<tr><td width='25px'><img src='IMG/icones_modif/repertoire_parent.png'/></td><td><a href='#' onClick='panierator.retour()'>"+this.l_retour+"</a></td><td></td><td></td></tr>\n";
        } 
        this.tableau_node=new Array();
        for (idx in liste) {
            var nom=liste[idx]["nom"];
            var ID=liste[idx]["ID"];
            var type=liste[idx]["type"];
            var nb=liste[idx]["nb"];
            var date_creation=liste[idx]["date_creation"];
            var description=liste[idx]["description"].replace("'", " ");
            var icone="<img alt='"+description+"' title='"+description+"' src='IMG/icones_modif/panier_statique.png'/>";
            if (type=="repertoire") {
                icone="<img alt='"+description+"' title='"+description+"' src='IMG/icones_modif/repertoire.png'/>";
            } else if (type=="dynamique") {
                icone="<img alt='"+description+"' title='"+description+"' src='IMG/icones_modif/panier_dynamique.png'/>";
            }
            this.tableau_node[ID]=liste[idx];
            tmp+="<tr><td>"+icone+"</td><td> <a href='#' onClick=\"panierator.clique_node('"+ID+"')\">"+nom+"</a></td><td>"+date_creation+"</td></tr>\n";
        }
        tmp+="</table>";
        this.div_liste.innerHTML=tmp;
        
        // si le panier en cours de modif est dans la liste on focus sur lui
        if (this.tableau_node[this.ID_panier_modif] != undefined) {
            //this.clique_node(this.ID_panier_modif);
        } else { // sinon on focus sur le répertoire
            //this.clique_node(this.ID_parent);
        }
     }
     
    
     
     ////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
     this.set_panier_en_cours = function () {
        var type=this.tableau_node[this.ID_panier_modif]["type"];
        if (type == "") {
            alert (this.l_selectionnez_panier);
            return ("");
        }
        if (type == "repertoire") {
            alert (this.l_pas_repertoire);
            return ("");
        }
        this.ID_panier_en_cours=this.ID_panier_modif;
        this.type_panier_en_cours=type;
        this.nom_panier_en_cours=this.tableau_node[this.ID_panier_en_cours]["chemin_parent"]+"/"+this.tableau_node[this.ID_panier_en_cours]["nom"];
        this.div_panier_en_cours.value=this.nom_panier_en_cours;
     }
     
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
    this.save = function () {
        var sUrl = this.ws_url+"&operation=save&ID="+this.ID_panier_modif+"&nom="+this.div_nom.value+"&description="+this.div_description.value;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                this_panierator.get_liste();
                this_panierator.clique_node(this_panierator.ID_panier_modif);
                
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
    this.clique_node = function (ID) {
        var sUrl = this.ws_url+"&operation=get_panier_by_ID&ID="+ID;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                //this_panierator.get_liste();
                 this_panierator.ID_panier_modif=ID;
                 this_panierator.div_nom.value=oResults.resultat["nom"];
                 this_panierator.div_description.value=oResults.resultat["description"];
                 this_panierator.div_chemin_parent.value=oResults.resultat["chemin_parent"]+"/"+oResults.resultat["nom"];
                 
                 if (oResults.resultat["type"]=="repertoire") {
                    this_panierator.ID_parent=ID;
                    if (this_panierator.chemin_parent=="") {
                        this_panierator.chemin_parent=oResults.resultat["nom"];
                    } else {
                        this_panierator.chemin_parent=oResults.resultat["chemin_parent"]+"/"+oResults.resultat["nom"];
                    }
                    this_panierator.get_liste();
                } else {
            
                }
                
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.add_liste = function () {
        var ID=this.ID_panier_en_cours;
        var type=this.type_panier_en_cours;
        var bool_appel=0;
        var sUrl = this.ws_url
        var sPost="";
        if (type=="dynamique") {
            var tableau=recherchator.formulaire_2_array();
            var contenu=recherchator.formulaire_2_json(tableau);
            sPost="operation=add_dynamique&ID="+ID+"&contenu="+contenu;
            if (statator != undefined) {
                var tableau2=statator.formulaire_2_array();
                var contenu2=statator.formulaire_2_json(tableau2);
                sPost += "&contenu_stat="+contenu2;
            }
            
        } else if (type=="statique") {
            recherchator.valide_formulaire_panier("add_statique", ID);
            return("");
        } else {
            if (this.id_appel != "") { // si la page a été appelée par une autre, on crée un panier automatiquement
                var tableau=recherchator.formulaire_2_array();
                var contenu=recherchator.formulaire_2_json(tableau);
                sPost = "operation=add_dynamique&crea_panier=1&type_objet="+this.type_objet+"&contenu="+contenu;
                bool_appel=1;
            } else {
                alert (this.l_selectionnez_panier);
                return ("");
            }
        }
        
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                if (bool_appel==1) {
                    //this_panierator.ID_panier_en_cours=oResults.resultat["ID"];
                    //this_panierator.type_panier_en_cours="dynamique";
                    //this_panierator.nom_panier_en_cours=oResults.resultat["chemin_parent"]+"/"+oResults.resultat["nom"];
                    //this_panierator.div_panier_en_cours.value=this_panierator.nom_panier_en_cours;
                    this_panierator.div_chemin_parent.value=oResults.resultat["chemin_parent"]+"/"+oResults.resultat["nom"];
                    this_panierator.retour_panier();
   
                } else {
                    alert("OK"); // TMP
                }
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, sPost);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.remove_liste = function () {
        var ID=this.ID_panier_en_cours;
        var type=this.type_panier_en_cours;
        if (type != "statique") {
            alert (this.l_impossible_non_statique);
            return ("");
        }
        recherchator.valide_formulaire_panier("remove_statique", ID);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.add_notice = function (ID_notice) {
        var ID=this.ID_panier_en_cours;
        var type=this.type_panier_en_cours;
        var str_crea_panier="";
        var bool_appel=0;
        if (type != "statique") {
            if (this.id_appel != "") {
                str_crea_panier="&crea_panier=1&type_objet="+this.type_objet;
                bool_appel=1;
            } else {
                alert (this.l_impossible_non_statique);
                return ("");
            }
        }
        if (ID_notice==undefined) {
            ID_notice=recherchator.ID_notice_en_cours;
        }
        if (ID_notice == 0) {
            alert (this.l_selectionnez_notice);
            return ("");
        }
        var sUrl = this.ws_url+"&operation=add_statique&ID="+ID+"&contenu="+ID_notice+str_crea_panier;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                if (bool_appel==1) {
                    this_panierator.ID_panier_en_cours=oResults.resultat["ID"];
                    this_panierator.type_panier_en_cours="statique";
                    this_panierator.nom_panier_en_cours=oResults.resultat["chemin_parent"]+"/"+oResults.resultat["nom"];
                    this_panierator.div_panier_en_cours.value=this_panierator.nom_panier_en_cours;
                }
                alert("OK"); // TMP
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
        
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.remove_notice = function (ID_notice) {
        var ID=this.ID_panier_en_cours;
        var type=this.type_panier_en_cours;
        if (type != "statique") {
            alert (this.l_impossible_non_statique);
            return ("");
        }
        if (ID_notice==undefined) {
            ID_notice=recherchator.ID_notice_en_cours;
        }
        if (ID_notice == 0) {
            alert (this.l_selectionnez_notice);
            return ("");
        }
        var sUrl = this.ws_url+"&operation=remove_statique&ID="+ID+"&contenu="+ID_notice;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                alert("OK"); // TMP
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
        
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    this.delete_panier = function () {
        if (!confirm(this.l_confirm_delete)) {
            return ("");
        }
        var sUrl = this.ws_url+"&operation=delete_panier&ID="+this.ID_panier_modif;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
			    this_panierator.chemin_parent=oResults.resultat["chemin_parent"];
                this_panierator.get_liste();
                this_panierator.clique_node(oResults.resultat["ID_parent"])
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // retour au panier parent 
    this.retour = function () {
        var tmp=this.chemin_parent.split("/");
        var nb=tmp.length;
        var chaine="";
        for (var i=0 ; i < nb-1 ; i++) {
            if (chaine != "") {
                chaine+="/";
            }
            chaine+=tmp[i];
        }
        this.chemin_parent=chaine;
        this.get_liste();
        this.div_chemin_parent.value=this.chemin_parent;
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne le nom du panier en cours à window.opener
    this.retour_panier = function () {
        if (this.id_appel == "") {
            return ("");
        }
        var nom_panier_modif=this.div_chemin_parent.value;
        //window.opener.set_panier_appel (this.id_appel, this.div_panier_en_cours.value);
        window.opener.set_panier_appel (this.id_appel, nom_panier_modif);
        window.close();
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // retourne le nom du panier en cours à window.opener
    this.copier_panier = function (bool_suppr) {
        var chemin_dest=prompt("Saisissez le chemin du dossier ou copier ou deplacer ce panier", "commun/xxx");
        if (chemin_dest==null) {
            return(true);
        }
        var sUrl = this.ws_url+"&operation=copie_panier&ID="+this.ID_panier_modif+"&chemin_dest="+chemin_dest+"&bool_suppr="+bool_suppr;
        var this_panierator = this;
        ///////////////// !!! CALLBACK !!!
        var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
			     alert ("OK");
			    //this_panierator.chemin_parent=oResults.resultat["chemin_parent"];
                //this_panierator.get_liste();
                //this_panierator.clique_node(oResults.resultat["ID_parent"])
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
        timeout: 7000,
        cache:false
    };
    ///////////////// !!! FIN DU CALLBACK !!!
    YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
    }
        

    
} // fin de la classe