<!--  Récapitulatif du fichier chargé et splitage du fichier  -->

<p class="titre5"><?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"l_titre_page", array()));   ?></p>

<?PHP print(get_intitule("bib/catalogue/imports/formulaire" ,"l_recap_telechargement", array("taille"=>$taille)));   ?>.<br />
<br />


<!--  Formulaire  -->
<?PHP affiche_template($GLOBALS["affiche_page"]["template"]["tmpl_formulaire"], array()); ?>

