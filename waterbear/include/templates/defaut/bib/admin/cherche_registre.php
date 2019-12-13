
<br />
<p>Vous pouvez utiliser le signe '%' comme troncature droite ou gauche</p>
<form>
<table>
<tr>
<td><?PHP print (get_intitule("", "bib/admin/cherche_registre/l_nom_noeud", array())); ?> : </td>
<td><input type="text" id="nom_noeud" name="nom_noeud" value="<?PHP print ($nom_noeud);  ?>"/></td>
</tr>
<tr>
<td><?PHP print (get_intitule("", "bib/admin/cherche_registre/l_valeur_noeud", array())); ?> : </td>
<td><input type="text" id="valeur_noeud" name="valeur_noeud" value="<?PHP print ($valeur_noeud) ; ?>" /></td>
</tr>
<tr><td colspan="2"><input type="button" value="OK" onclick="valide_form()"/></td></tr>

</table>
</form>

<div id="div_table"></div>




