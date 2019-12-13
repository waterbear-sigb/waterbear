
<table width="95%"><tr>
<td width="100%">
Liste : 
<select name="nom_liste" id="nom_liste" onchange="get_liste('','','','');">
<?PHP
$listes=$GLOBALS["affiche_page"]["parametres"]["listes"];

foreach ($listes as $intitule => $def) {
    $valeur=$def["chemin_registre"];
    print ("<option value=\"$valeur\">$intitule</option>");
}

?>
</select>
<img src='IMG/icones/application_double.png' onClick='open_liste();'/>
</td>


</tr></table>
<br />
<br />
<div id="affiche_liste">

</div>
<div id="affiche_defaut">

</div>

<div id="affiche_help">
    <br/><br/>
<?PHP

$help=$GLOBALS["affiche_page"]["parametres"]["help"];
print ($help);

?>
</div>


