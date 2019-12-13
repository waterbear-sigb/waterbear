<!--  Template "javascript" de la page -->

<script language="javascript">
    
    function init () {
        var erreurs_chargement = "<?PHP print ($erreurs);  ?>";
         if (erreurs_chargement != "") {
            alert (erreurs_chargement);
        }
        formulator = new <?PHP print ($GLOBALS["affiche_page"]["parametres"]["type_formulator_js"]); ?>(document.getElementById("test"), "formulator");
        formulator.set_ws_path ("<?PHP print ($GLOBALS["affiche_page"]["parametres"]["page_ws"]);  ?>");
        formulator.set_id_operation ("<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ID_operation"]); ?>");
        formulator.set_classe_css ("<?PHP print($GLOBALS["affiche_page"]["parametres"]["classe_formulator_css"]); ?>");
        formulator.set_id_notice ("<?PHP print($_REQUEST["ID_notice"]); ?>");
        formulator.set_appel ("<?PHP print($_REQUEST["id_appel"]); ?>");
        formulator.liste_masques=eval("(<?PHP print ("$liste_masques");   ?>)"); // JSON
        formulator.actions_fin=eval("(<?PHP print ("$actions_fin");   ?>)"); // JSON
        formulator.masque_actuel="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["masque_defaut"]); ?>";
        formulator.masque_defaut="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["masque_defaut"]); ?>";
        
        // Le masque par défaut peut également être transmis par l'url (cas en particulier si on utilise la redirection)'
        
        <?PHP
        if ($_REQUEST["masque"] != "") {
        ?>
        formulator.masque_actuel="<?PHP print ($_REQUEST["masque"]); ?>";
        formulator.masque_defaut="<?PHP print ($_REQUEST["masque"]); ?>";
        
        <?PHP    
        }
        
        ?>
        
        formulator.auto_grille="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["auto_grille"]); ?>";
        formulator.init();
    }
    
    function callback_appel (id_appel, id_notice) {
        formulator.callback_appel (id_appel, id_notice);
    }
    
    function teste_fermeture () {
        //alert (formulator.bool_modif);
        if (formulator.bool_modif==1) {
            return ("Voulez-vous vraiment quitter ?");
        }
    }
    
    window.onbeforeunload=teste_fermeture;
    
        
    </script>
    
<!--  Fin du template "javascript" de la page -->