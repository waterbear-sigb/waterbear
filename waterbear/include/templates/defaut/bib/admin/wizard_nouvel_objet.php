<form action="bib.php" method="POST">
<input type="hidden" value="admin/wizard_nouvel_objet" name="module" />
<table width="95%" border="1px">
<tr>
<td width="50%">Nom de l'objet</td>
<td><input type="text" name="nom_obj" /></td>
</tr>
<tr>
<td width="50%">BIB catalogage</td>
<td><input type="checkbox" value="1" checked="checked" name="catalogage_bib" /></td>
</tr>

<tr>
<td width="50%">BIB_WS catalogage</td>
<td><input type="checkbox" value="1" checked="checked" name="catalogage_bib_ws" /></td>
</tr>

<tr>
<td width="50%">Langues catalogage</td>
<td><input type="checkbox" value="1" checked="checked" name="catalogage_bib_langues" /></td>
</tr>

<tr>
<td width="50%">Plugins grille catalogage</td>
<td><input type="checkbox" value="1" checked="checked" name="catalogage_plugins_grilles" /></td>
</tr>

<tr>
<td width="50%">Plugins DB catalogage</td>
<td><input type="checkbox" value="1" checked="checked" name="catalogage_plugins_db" /></td>
</tr>

<tr>
<td width="50%">plugins formatage catalogage</td>
<td><input type="checkbox" value="1" checked="checked" name="catalogage_plugins_formatage" /></td>
</tr>

<tr>
<td width="50%">BIB recherche</td>
<td><input type="checkbox" value="1" checked="checked" name="recherche_bib" /></td>
</tr>

<tr>
<td width="50%">BIB_WS recherche</td>
<td><input type="checkbox" value="1" checked="checked" name="recherche_bib_ws" /></td>
</tr>

<tr>
<td width="50%">plugins recherche</td>
<td><input type="checkbox" value="1" checked="checked" name="recherche_plugins" /></td>
</tr>

<tr>
<td width="50%">menus contextuels</td>
<td><input type="checkbox" value="1" checked="checked" name="menus_contextuels" /></td>
</tr>

<tr>
<td width="50%">BIB_WS autocomplete</td>
<td><input type="checkbox" value="1" checked="checked" name="autocomplete_bib_ws" /></td>
</tr>




<td width="50%">&nbsp;</td>
<td><input type="submit" value="Creer l'objet" /></td>
</tr>

</table>


</form>