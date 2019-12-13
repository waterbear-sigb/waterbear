<div style="margin:10" width="90%">

<table class="bulletinage_table" >
<tr>
<td class="bulletinage_intitule">
 <?PHP //print($_REQUEST["ID_commande"]); ?>
</td>
</tr>
<tr><td id="aide_saisie"><?PHP print($GLOBALS["affiche_page"]["parametres"]["aide_saisie_init"]); ?></td></tr>
<tr>
<td class="bulletinage_td_cab">

<form id="input_cab_form" action="xxx.php" method="get" enctype="text/plain" onsubmit="return submit_cab()">

<div>
<input type="text" name="bulletinage" id="input_cab" />
<div id="autocomplete_input_cab"></div>
</div>

<br />

</form>

</td>
</tr>

<tr>
<td class="bulletinage_td_affichage">
<div id="zone_affichage">


</div>
<br />
<br />

<div id="onglets_abos">


</div>


<div id="zone_fascicules">


</div>
<div id="zone_exemplaires">


</div>
</td>
</tr>



</table>



</div>