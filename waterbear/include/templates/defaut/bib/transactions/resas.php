<table  class="formulaire">
<tr>
<td class="formulaire_intitule">Document : </td>
<td  class="formulaire_champ">

<div  style="width: 50%; position: absolute;" class="yui-ac">
<input type="text"  id="champ_doc" class="yui-ac-input" value="<?PHP print($_REQUEST["ID_doc"]);  ?>" />

<div id="conteneur_doc" class="yui-ac-container"></div>
</div>
</td>

</tr>
<tr>
<td  class="formulaire_intitule">Lecteur : </td>
<td  class="formulaire_champ">
<div  style="width: 50%; position: absolute;" class="yui-ac">
<input type="text"  id="champ_lecteur" class="yui-ac-input" value="<?PHP print($_REQUEST["ID_lecteur"]);  ?>" />

<div id="conteneur_lecteur" class="yui-ac-container"></div>
</div>
</td>

</tr>

<tr>
<td  class="formulaire_intitule">Bib. : </td>
<td  class="formulaire_champ">
<select id="bib"><?PHP print($liste_bibs); ?></select>
</td>

</tr>

<tr>
<td colspan="2">
<input type="button" value="Reserver" onclick="valide_reservation('non');"/>
</td>
</tr>
</table>