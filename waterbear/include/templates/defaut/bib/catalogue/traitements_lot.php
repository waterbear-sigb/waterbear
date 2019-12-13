<table width="100%">
<tr>
<td width="20%">
Type d'objet
</td>
<td>
<select id="type_obj" onchange="get_liste_traitements();">
<?PHP
foreach ($liste_objets as $tmp) {
    $intitule=$tmp["intitule"];
    $valeur=$tmp["valeur"];
    print ("<option value=\"$valeur\">$intitule</option>\n");
}


?>
</select>
</td>
</tr>

<tr>
<td width="20%">
Traitement
</td>
<td>
<select id="select_liste_traitements" onchange="get_formulaire();"></select>
</td>
</tr>

<tr>
<td width="20%">
Panier
</td>
<td>
<div style="width: 80%;">
<input type="text"  id="input_panier" />
<div id="autocomplete_input_panier"></div>
</div>
</td>
</tr>

<tr>
<td colspan="2">
<br />
<div id="div_aide"></div>
<br />
</td>
</tr>

<tr>
<td colspan="2">
<br />
<div id="div_formulaire"></div>
<br />
</td>
</tr>

<tr>
<td colspan="2" align="center">
<br />
<input type="submit" value="Valider" name="valider" onclick="valide_formulaire(1);"/>
<br />
</td>
</tr>


</table>

<br />
<div id="div_resume"></div>

<br />
<div id="div_erreurs"></div>
