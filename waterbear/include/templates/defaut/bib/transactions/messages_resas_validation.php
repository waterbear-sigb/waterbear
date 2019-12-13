
<form action="bib.php?module=<?PHP  print($GLOBALS["affiche_page"]["parametres"]["auto_page"]); ?>" method="POST" >
<table width="800px">

<tr>
<td width="500px"><?PHP print(get_intitule("bib/transactions/resas/lettres", "l_panier", array())); ?></td>
<td width="300px">
<div style="width: 100%;">
<input type="text"  id="input_panier" name="input_panier" />
<div id="autocomplete_input_panier"></div>
</div>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td >
<input type="submit" value="<?PHP print(get_intitule("bib/transactions/resas/lettres", "l_bouton", array())); ?>" />
</td></tr>
</table>
</form>