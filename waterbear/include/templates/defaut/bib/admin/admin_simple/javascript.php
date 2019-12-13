<!--  Pour les onglets -->

<script type="text/javascript">

var tabview;
var structure_parametres=eval("(<?PHP  print ($structure_parametres); ?>)");
var erreurs="<?PHP  print ($erreurs); ?>";
var liste_onglets = new Object();

function init () {
    init_onglets();
    if (erreurs != "") {
        alert (erreurs);
    }
    
}

function init_onglets() {
	tabView = new YAHOO.widget.TabView();
	 
     for (idx_onglet in structure_parametres) {
        var titre_onglet=structure_parametres[idx_onglet]["titre"];
        var rubriques=structure_parametres[idx_onglet]["rubriques"];
        var html_rubrique="";
        for (idx_rubrique in rubriques) {
            var titre_rubrique=rubriques[idx_rubrique]["titre"];
            var description_rubrique=rubriques[idx_rubrique]["description"];
            var lien_rubrique=rubriques[idx_rubrique]["lien"];
            html_rubrique+="<b><a href='bib.php?module=admin/registre&acces_direct=Registre/"+lien_rubrique+"' target='_blank'>"+titre_rubrique+"</a></b><br>"+description_rubrique+"<br><hr /><br>";
        }
        liste_onglets[idx_onglet]=new YAHOO.widget.Tab({
            label: titre_onglet,
            content: html_rubrique,
            active: true
        });
        tabView.addTab(liste_onglets[idx_onglet]);
     }
     

    tabView.appendTo('container');
}



</script>