<script language="javascript">
function init() {
    
}
</script>

<div id="div_fond_accueil" style="width: 100%; height: 100%; background-image: url('<?PHP  print($GLOBALS["affiche_page"]["parametres"]["url_img_fond"]); ?>'); background-attachment: fixed; background-position: center center; background-repeat: no-repeat; background-size: auto 100%">


<!-- Bloc 1 : Prêt inscription   -->
<div class="accueil2 arrondi transparent" style="position: absolute; top: 10px; left: 2%; height: 150px; width: 46%;"> 
   <p class="accueil_titre_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="menu_action_clic('', '', 'bib.php?module=transactions/prets/standard');">prêt - inscriptions </a></p>

   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="menu_action_clic('', '', 'bib.php?module=transactions/prets/standard');" class="accueil_titre_2">> faire du prêt</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/lecteur/unimarc_standard');" href="#" class="accueil_titre_2">> nouveau lecteur</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/lecteur/standard');" href="#" class="accueil_titre_2">> chercher un lecteur</a><br />
</div>

<!-- Bloc 2 : Catalogage   -->
<div class="accueil2 arrondi transparent" style="position: absolute; top: 10px; left: 52%; height: 150px; width: 46%;">
<p class="accueil_titre_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/biblio/unimarc_xs/livre');">catalogage </a></p>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/biblio/unimarc_xs/livre');">> livre</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/biblio/unimarc_xs/cd');">> CD</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/biblio/unimarc_xs/dvd');">> DVD</a><br />

</div>


<!-- Bloc 3 : Recherche   -->
<div class="accueil2 arrondi transparent" style="position: absolute; top: 180px; left: 2%; height: 150px; width: 46%;">
<p class="accueil_titre_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"  onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/biblio/simple');">chercher un document </a></p>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2"  onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/biblio/simple');">> recherche simple</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2"  onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/biblio/moyen');">> recherche avancée</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2"  onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/exemplaire/cab_lot');">> par lot de codes barres</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2"  onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/biblio/isbn_lot');">> par lot d'ISBN</a><br />

</div>

<!-- Bloc 4 : Revues   -->
<div class="accueil2 arrondi transparent" style="position: absolute; top: 180px; left: 52%; height: 150px; width: 46%;">
<p class="accueil_titre_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="bib.php?module=catalogue/periodiques/bulletinage/standard" >revues </a></p>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/periodiques/bulletinage/standard');">> réceptionner les revues</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/biblio/unimarc_xs/revue');">> nouvelle revue</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/biblio/revues');">> chercher une revue</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/abo/bulletinage_retards');">> Revues en retard</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/abo/reabonnements');">> Abonnements arrivant à échéance</a><br />
 

</div>

<!-- Bloc 5 : Traitements   -->
<div class="accueil2 arrondi transparent" style="position: absolute; top: 350px; left: 2%; height: 150px; width: 46%;">
<p class="accueil_titre_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="" >traitements </a></p>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=admin/messages_rappels_validation/defaut');">> lettres de rappels</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=admin/messages_resas_validation/defaut');">> lettres de réservations</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/exemplaire/docs_a_manipuler');">> réservations à récupérer en rayon</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/exemplaire/resas_a_remettre_en_rayon');">> réservations à remettre en rayon</a><br />
</div>

<!-- Bloc 6 : Acquisitions - importations   -->
<div class="accueil2 arrondi transparent" style="position: absolute; top: 350px; left: 52%; height: 150px; width: 46%;">
<p class="accueil_titre_1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/commande/unimarc_standard');">acquisitions - importations </a></p>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/catalogage/grilles/commande/unimarc_standard');">> nouvelle commande</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/recherches/formulaires/commande/standard');">> chercher une commande</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/imports/choix/biblio_standard');">> importations</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/retour_bdp/standard');">> retours BDP</a><br />
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="accueil_titre_2" onclick="menu_action_clic('', '', 'bib.php?module=catalogue/mel/standard');">> moccam-en-ligne</a><br />


</div>


</div>