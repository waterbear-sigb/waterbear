<div style="margin:10" width="90%">

<a href="#" onclick="go_mel_url('liste_paniers')"><?PHP print (get_intitule("bib/catalogue/mel", "l_liste_paniers", array()));  ?></a> <br /><br />
<a href="#" onclick="go_mel_url('recherche_par_lot')"><?PHP print (get_intitule("bib/catalogue/mel", "l_recherche_par_lot", array()));  ?></a> <br /><br />
<a href="#" onclick="go_mel_url('portail')"><?PHP print (get_intitule("bib/catalogue/mel", "l_portail", array()));  ?></a> <br /><br />


<?PHP print (get_intitule("bib/catalogue/mel", "l_importer", array()));  ?> : 
<select id="mel_liste_paniers">
</select>
<img onclick="importer_panier()" alt="<?PHP print (get_intitule("bib/catalogue/mel", "l_importer", array()));  ?>" title="<?PHP print (get_intitule("bib/catalogue/mel", "l_importer", array()));  ?>" src="IMG/icones/page_go.png"/>
<br />
<br />
<div id="mel_resultat">

</div>

















</div>