<div style="margin:10" width="90%">

<table class="reception_commande_table" >
<tr>
<td class="reception_commande_td_intitule">
<?PHP print(get_intitule("", $GLOBALS["affiche_page"]["parametres"]["label_nom_commande"], array()));  ?> : <?PHP print($_REQUEST["ID_commande"]); ?>
</td>
</tr>
<tr><td id="aide_saisie"><?PHP print($GLOBALS["affiche_page"]["parametres"]["aide_saisie"]); ?></td></tr>

<tr>
<td class="reception_commande_td_cab">
<br />
<form action="xxx.php" method="get" enctype="text/plain" onsubmit="return submit_cab()">
<div >
<input type="text" name="commande" id="input_cab" />
<div id="autocomplete_input_cab"></div>
</div>
<br />

</form>

</td>
</tr>

<tr>
<td class="reception_commande_td_affichage">
<div id="zone_affichage">


</div>
</td>
</tr>



</table>



</div>