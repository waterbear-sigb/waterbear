


<script type="text/javascript">

intitules["l_onglet_scripts"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_scripts", array()));  ?>";
intitules["l_onglet_plugins"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_plugins", array()));  ?>";
intitules["l_onglet_infos"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_infos", array()));  ?>";
intitules["l_onglet_ss_plugins"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_ss_plugins", array()));  ?>";
intitules["l_onglet_plugins_inclus"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_plugins_inclus", array()));  ?>";
intitules["l_onglet_alias"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_alias", array()));  ?>";
intitules["l_onglet_alias_retour"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_alias_retour", array()));  ?>";
intitules["l_onglet_var_inc"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_var_inc", array()));  ?>";
intitules["l_onglet_parametres"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_parametres", array()));  ?>";
intitules["l_onglet_retour"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_retour", array()));  ?>";
intitules["l_onglet_retour_alias"]="<?PHP print (get_intitule("bib/admin/dbg_plugins", "l_onglet_retour_alias", array()));  ?>";

var ws_path="<?PHP print ($GLOBALS["tvs_global"]["conf"]["ini"]["WS_path"]["bib"]);  ?>?module=admin/dbg_plugins";
var tabView;
var tab_scripts;
var tab_plugins;
var tab_infos;
var tab_ss_plugins;
var tab_plugins_inclus;
var tab_alias;
var tab_alias_retour;
var tab_var_inc;
var tab_parametres;
var tab_retour;

var ID_script_en_cours;
var ID_plugin_en_cours;
var nom_plugin_en_cours;
var bool_onglet_parametres;


function init() {
    init_onglets();
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function init_onglets() {
	tabView = new YAHOO.widget.TabView();
	 
	//tabView.addTab();
	
    tab_scripts=new YAHOO.widget.Tab({
        label: intitules["l_onglet_scripts"],
        content: '<div id="tab_scripts">Rechargez la liste des scripts...</div>',
        active: true
    });
    
    tab_plugins=new YAHOO.widget.Tab({
        label: intitules["l_onglet_plugins"],
        content: '<div id="tab_plugins">Vous devez selectionner un script...</div>',
        active: false
    });
    
    tab_infos=new YAHOO.widget.Tab({
        label: intitules["l_onglet_infos"],
        content: '<div id="tab_infos">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_ss_plugins=new YAHOO.widget.Tab({
        label: intitules["l_onglet_ss_plugins"],
        content: '<div id="tab_ss_plugins">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_plugins_inclus=new YAHOO.widget.Tab({
        label: intitules["l_onglet_plugins_inclus"],
        content: '<div id="tab_plugins_inclus">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_alias=new YAHOO.widget.Tab({
        label: intitules["l_onglet_alias"],
        content: '<div id="tab_alias">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_alias_retour=new YAHOO.widget.Tab({
        label: intitules["l_onglet_alias_retour"],
        content: '<div id="tab_alias_retour">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_var_inc=new YAHOO.widget.Tab({
        label: intitules["l_onglet_var_inc"],
        content: '<div id="tab_var_inc">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_parametres=new YAHOO.widget.Tab({
        label: intitules["l_onglet_parametres"],
        //content: '<div id="container_param" style="position: absolute ; top: 80 ; left: 10 ; height: 100% ; width: 80% ">   </div>',
        content: '<div id="container_param" >   </div>',
        active: false
    });
    
    tab_retour=new YAHOO.widget.Tab({
        label: intitules["l_onglet_retour"],
        content: '<div id="tab_retour">Vous devez selectionner un plugin...</div>',
        active: false
    });
    
    tab_retour_alias=new YAHOO.widget.Tab({
        label: intitules["l_onglet_retour_alias"],
        content: '<div id="tab_retour_alias">Vous devez selectionner un plugin...</div>',
        active: false
    });

	tabView.addTab(tab_scripts);
	tabView.addTab(tab_plugins);
    tabView.addTab(tab_infos);
    tabView.addTab(tab_alias);
    tabView.addTab(tab_var_inc);
    tabView.addTab(tab_plugins_inclus);
    tabView.addTab(tab_parametres);
    tabView.addTab(tab_ss_plugins);
    tabView.addTab(tab_retour);
    tabView.addTab(tab_alias_retour);
    tabView.addTab(tab_retour_alias);
    
    tabView.appendTo('container');
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function get_scripts () {
    var sUrl = ws_path+"&operation=get_scripts";
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                tabView.set('activeIndex', 0);
                var html="<table>";
				for (var script in oResults.resultat) {
				    var ID_script=oResults.resultat[script]["ID_script"];
                    var nom=oResults.resultat[script]["nom"];
                    html+="<tr><td><a href='#' onClick='get_script(\""+ID_script+"\")'>"+nom+"</a><br></td></tr>";
				}
                html+="</table>"
                document.getElementById("tab_scripts").innerHTML=html;
                init_onglet_parametres();
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur de connexion");
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

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function init_onglet_parametres() {
    if (bool_onglet_parametres == 1) {
        return (true);
    }
    bool_onglet_parametres = 1;
    tabParam = new YAHOO.widget.TabView();
	 
	//tabView.addTab();
	
    tab_param_script=new YAHOO.widget.Tab({
        label: "script",
        content: '<div id="tab_param_script">...</div>',
        active: true
    });
    
    tab_param_PA=new YAHOO.widget.Tab({
        label: "PA",
        content: '<div id="tab_param_PA">...</div>',
        active: false
    });
    
    tab_param_plugin=new YAHOO.widget.Tab({
        label: "plugin",
        content: '<div id="tab_param_plugin">...</div>',
        active: false
    });
    
    tab_param_merge=new YAHOO.widget.Tab({
        label: "merge",
        content: '<div id="tab_param_merge">...</div>',
        active: false
    });
    
    tab_param_alias=new YAHOO.widget.Tab({
        label: "alias",
        content: '<div id="tab_param_alias">...</div>',
        active: false
    });
    
    tab_param_var_inc=new YAHOO.widget.Tab({
        label: "var_inc",
        content: '<div id="tab_param_var_inc">...</div>',
        active: false
    });
    
    tab_param_plugin_inclus=new YAHOO.widget.Tab({
        label: "plugins inclus",
        content: '<div id="tab_param_plugin_inclus">...</div>',
        active: false
    });
    
    tabParam.addTab(tab_param_script);
    tabParam.addTab(tab_param_PA);
    tabParam.addTab(tab_param_plugin);
    tabParam.addTab(tab_param_merge);
    tabParam.addTab(tab_param_alias);
    tabParam.addTab(tab_param_var_inc);
    tabParam.addTab(tab_param_plugin_inclus);
    
    tabParam.appendTo('container_param');
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function delete_historique () {
    var sUrl = ws_path+"&operation=delete_historique";
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                alert ("OK");
                get_scripts ();
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur de connexion");
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

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function get_script (ID_script) {
    ID_script_en_cours=ID_script;
    var sUrl = ws_path+"&operation=get_script&ID_script="+ID_script;
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                var html="<table>";
				for (var plugin in oResults.resultat) {
				    var a_appeler=oResults.resultat[plugin]["a_appeler"];
                    var nom=oResults.resultat[plugin]["nom"];
                    var ID_plugin=oResults.resultat[plugin]["ID_plugin"];
                    html+="<tr><td><a href='#' onClick='get_plugin(\""+ID_plugin+"\")'>"+nom+"</a></td></tr><br>";
				}
                html+="</table>"
                document.getElementById("tab_plugins").innerHTML=html;
                tabView.set('activeIndex', 1);
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur de connexion");
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

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function get_plugin (ID_plugin) {
    ID_plugin_en_cours=ID_plugin;
    var ID_script=ID_script_en_cours;
    var sUrl = ws_path+"&operation=get_plugin&ID_script="+ID_script+"&ID_plugin="+ID_plugin;
  	
  	///////////////// !!! CALLBACK !!!
    var callback = {
      	// SUCCES
        success: function(oResponse) {
            var oResults = eval("(" + oResponse.responseText + ")");
            if (oResults.succes != 1) {
				alert (oResults.erreur);
			} else {
                // paramètres
                var infos=oResults.resultat.infos;
                var ss_plugins=oResults.resultat.ss_plugins;
                var plugins_inclus=oResults.resultat.plugins_inclus;
                var alias=oResults.resultat.alias;
                var alias_retour=oResults.resultat.alias_retour;
                var var_inc=oResults.resultat.var_inc;
                
                var parent = infos.parent;
                var type_plugin = infos.type_plugin;
                var PA_init = infos.PA_init;
                var type_PA = infos.type_PA;
                var a_inclure = infos.a_inclure;
                var a_appeler = infos.a_appeler;
                var retour = infos.retour;
                var retour_alias = infos.retour_alias;
                var nom = infos.nom;
                var nom_parent = infos.nom_parent;
                var duree = infos.duree;
                var erreur = infos.erreur;
                
                var parametres_script_init=infos.parametres_script_init;
                var parametres_PA_init=infos.parametres_PA_init;
                var parametres_plugin_init=infos.parametres_plugin_init;
                var param_merge_init=infos.param_merge_init;
                var param_merge_alias=infos.param_merge_alias;
                var param_merge_var_inc=infos.param_merge_var_inc;
                var param_merge_plugin_inclus=infos.param_merge_plugin_inclus;
                
                nom_plugin_en_cours="Registre/profiles/defaut/plugins/plugins/"+nom;
                
                // infos
                html="<br><b>"+nom+"</b><br><br>";
                html+="<b>type_plugin</b> : "+type_plugin+"<br><br>";
                html+="<b>type_PA</b> : "+type_PA+"<br><br>";
                html+="<b>duree</b> : "+duree+"<br><br>";
                html+="<b>parent</b> : <a href='#' onClick='get_plugin(\""+parent+"\")'>"+nom_parent+"</a><br><br>";
                html+="<b>a_inclure</b> : "+a_inclure+"<br><br>";
                html+="<b>a_appeler</b> : "+a_appeler+"<br><br><br>";
                html+="<b>PA_init</b> : "+PA_init+"<br><br>";
                if (erreur != "") {
                    html+="<font color='red'><b>ERREUR : "+erreur+"</b></font><br><br>";
                }
                
                document.getElementById("tab_infos").innerHTML=html;
                
                // paramètres
                document.getElementById("tab_param_script").innerHTML=print_r(JSON.decode(parametres_script_init), 0, "br");
                document.getElementById("tab_param_PA").innerHTML=print_r(JSON.decode(parametres_PA_init), 0, "br");
                document.getElementById("tab_param_plugin").innerHTML=print_r(JSON.decode(parametres_plugin_init), 0, "br");
                document.getElementById("tab_param_merge").innerHTML=print_r(JSON.decode(param_merge_init), 0, "br");
                document.getElementById("tab_param_alias").innerHTML=print_r(JSON.decode(param_merge_alias), 0, "br");
                document.getElementById("tab_param_var_inc").innerHTML=print_r(JSON.decode(param_merge_var_inc), 0, "br");
                document.getElementById("tab_param_plugin_inclus").innerHTML=print_r(JSON.decode(param_merge_plugin_inclus), 0, "br");
                
                // ss_plugins
                var html_ss_plugins="";
                for (idx_ss_plugin in ss_plugins) {
                    var ID_ss_plugin=ss_plugins[idx_ss_plugin]["ID_plugin"];
                    html_ss_plugins+="<a href='#' onClick='get_plugin(\""+ID_ss_plugin+"\")'>"+ss_plugins[idx_ss_plugin]["nom"]+"</a><br/><br>";
                }
                document.getElementById("tab_ss_plugins").innerHTML=html_ss_plugins;
                
                // plugins_inclus
                var html_plugins_inclus="";
                for (idx_plugin_inclus in plugins_inclus) {
                    var ID_plugin_inclus=plugins_inclus[idx_plugin_inclus]["ID_plugin"];
                    html_plugins_inclus+="<a href='#' onClick='get_plugin(\""+ID_plugin_inclus+"\")'>"+plugins_inclus[idx_plugin_inclus]["nom"]+"</a><br/><br>";
                }
                document.getElementById("tab_plugins_inclus").innerHTML=html_plugins_inclus;
                
                // alias
                var html_alias="<table width='95%' border='1px'><tr><td><b>anciennes variables</b></td><td><b>nouvelles variables</b></td></tr>";
                for (idx_alias in alias) {
                    html_alias+="<tr><td>";
                    html_alias+=alias[idx_alias]["old_variable"];
                    html_alias+="</td><td>";
                    html_alias+=alias[idx_alias]["new_variable"];
                    html_alias+="</td></tr>";
                }
                html_alias+="</table>";
                document.getElementById("tab_alias").innerHTML=html_alias;
                
                // alias retour
                var html_alias="<table width='95%' border='1px'><tr><td><b>anciennes variables</b></td><td><b>nouvelles variables</b></td></tr>";
                for (idx_alias in alias_retour) {
                    html_alias+="<tr><td>";
                    html_alias+=alias_retour[idx_alias]["old_variable"];
                    html_alias+="</td><td>";
                    html_alias+=alias_retour[idx_alias]["new_variable"];
                    html_alias+="</td></tr>";
                }
                html_alias+="</table>";
                document.getElementById("tab_alias_retour").innerHTML=html_alias;
                
                // var_inc
                var html_alias="<table width='95%' border='1px'><tr><td><b>nom de la variable</b></td><td><b>chemin de la valeur de remplacement</b></td><td><b>valeur de remplacement</b></td></tr>";
                for (idx_alias in var_inc) {
                    html_alias+="<tr><td>.../";
                    html_alias+=var_inc[idx_alias]["nom_var"];
                    html_alias+="</td><td>";
                    html_alias+=var_inc[idx_alias]["chemin"];
                    html_alias+="</td><td>";
                    html_alias+=var_inc[idx_alias]["valeur"];
                    html_alias+="</td></tr>";
                }
                html_alias+="</table>";
                document.getElementById("tab_var_inc").innerHTML=html_alias;
               
                // retour
                var tmp=print_r(JSON.decode(retour), 0, "br");
                document.getElementById("tab_retour").innerHTML=tmp;
                
                // retour_alias
                var tmp=print_r(JSON.decode(retour_alias), 0, "br");
                document.getElementById("tab_retour_alias").innerHTML=tmp;
                
                // focus
                tabView.set('activeIndex', 2);
                
                
                
			}
        },
        
        // ECHEC
        failure: function(oResponse) {
            alert ("Erreur de connexion");
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

function goto_plugin () {
    url="bib.php?module=admin/registre&acces_direct="+nom_plugin_en_cours;
    window.open(url);
    return (true);
}

</script>