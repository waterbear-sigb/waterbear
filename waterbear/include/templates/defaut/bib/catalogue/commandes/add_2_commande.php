<div style="margin:10" width="90%">

<table class="add_2_commande_table">
<tr class="add_2_commande_label">
<td class="add_2_commande_label" colspan="2">
<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_ajouter_notices", array()));  ?>  <br />
</td>
</tr>


<tr class="add_2_commande_autocomplete">
<td class="add_2_commande_autocomplete">
<div>
<input type="text" name="panier" id="autocomplete_panier" value="<?PHP  print ($_REQUEST["panier"]) ?>"/>
<div id="autocomplete_conteneur_panier"></div>
</div>
</td>
<td class="add_2_commande_icones">


 <img src="<?PHP print($GLOBALS["affiche_page"]["parametres"]["icone_rechercher_panier_url"]); ?>" onclick="open_recherche_panier ();" alt="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_icone_rechercher_panier", array()));  ?>" title="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_icone_rechercher_panier", array()));  ?>"/>

</td>
</tr>



<tr class="add_2_commande_label">
<td class="add_2_commande_label" colspan="2">
<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_a_la_commande", array()));  ?><br />
</tr>
</tr>
<tr class="add_2_commande_autocomplete">
<td class="add_2_commande_autocomplete">
<div>
<input type="text" name="commande" id="autocomplete_commande" value="<?PHP  print ($_REQUEST["ID_commande"]) ?>"/>
<div id="autocomplete_conteneur_commande"></div>
</div>
</td>
<td class="add_2_commande_icones">

<img src="<?PHP print($GLOBALS["affiche_page"]["parametres"]["icone_rechercher_panier_url"]); ?>" onclick="open_recherche_commande ();" alt="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_icone_rechercher_commande", array()));  ?>" title="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_icone_rechercher_commande", array()));  ?>"/>
<img src="<?PHP print($GLOBALS["affiche_page"]["parametres"]["icone_nouvelle_commande_url"]); ?>" onclick="open_crea_commande ();" alt="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_icone_crea_commande", array()));  ?>" title="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_icone_crea_commande", array()));  ?>"/>

</td>
</tr>
<tr>
<td colspan="2">
<input type="submit" value="<?PHP print (get_intitule("bib/catalogue/commandes/add_2_commande", "l_valide_commande", array()));  ?>" name="valider" onclick="valide_commande()"/>
</td>
</tr>
</table>














</div>