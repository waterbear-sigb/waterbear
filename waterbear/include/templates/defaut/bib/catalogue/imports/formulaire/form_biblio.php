<br />
<div id="onglets" class="yui-navset" style="width: 70%;"> 
    <ul class="yui-nav"> 
        <li class="selected"><a href="#tab1"><em><?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"onglet_simple", array()));   ?></em></a></li> 
        <li><a href="#tab2"><em><?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"onglet_avance", array()));   ?></em></a></li> 
    </ul>             
    <div class="yui-content"> 
        <div id="tab1"> <!-- ONGLET SIMPLE --------------------------------------------------------------------------------------------->
            <form action="a_modifier_par_js" method="POST" id="formulaire_simple">
            <input type="hidden" value="<?PHP print ($GLOBALS["affiche_page"]["parametres"]["ID_operation"]); ?>" name="ID_operation" />

            <p class='titre4'><?PHP print(get_intitule("bib/catalogue/imports/formulaire/form_biblio" ,"l_etape1_simple", array()));   ?></p>
            <select id= "filtre" name="filtre"><?PHP tmpl_affiche_liste($GLOBALS["affiche_page"]["parametres"]["liste_filtres"])  ?></select>
            <p class='titre4'><?PHP print(get_intitule("bib/catalogue/imports/formulaire/form_biblio" ,"l_etape2_simple", array()));   ?></p>
            <input type="button" onclick="split_fichier();" value="<?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"b_invite_split", array()));   ?>" /> : 
            <input type="text" size="5" maxlength="5" readonly="readonly" id="nb_notices" />
            <?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"l_notices", array()));   ?>.<br />
            <p class='titre4'><?PHP print(get_intitule("bib/catalogue/imports/formulaire/form_biblio" ,"l_etape3_simple", array()));   ?></p>
            <table>
            <tr>
                <td><?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"interactif", array()));   ?> : </td>
                <td><input type="radio" value="1" checked="checked" name="interactif" /><?PHP print(get_intitule("div" ,"oui", array()));   ?> &nbsp;&nbsp;
                    <input type="radio" value="0"  name="interactif" /><?PHP print(get_intitule("div" ,"non", array()));   ?>
                </td>
            </tr>
            </table>
            <p class='titre4'><?PHP print(get_intitule("bib/catalogue/imports/formulaire/form_biblio" ,"l_etape4_simple", array()));   ?></p>
            <input type="button" onclick="valide_simple()" value="<?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"b_importer", array()));   ?>" />
            </form>
        </div> 
        <div id="tab2"> <!-- ONGLET AVANCE --------------------------------------------------------------------------------------------->
        A FAIRE ...
        </div> 
    </div> 
</div> 




<script language="javascript">

function init_onglets () {
	tabView = new YAHOO.widget.TabView("onglets");
}


</script>