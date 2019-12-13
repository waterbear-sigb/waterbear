
<table width="95%"><tr>
<td width="50%">
Liste : 
<select name="nom_liste" id="nom_liste" onchange="get_liste('','','','');">
<?PHP
$listes=$GLOBALS["affiche_page"]["parametres"]["listes"];
if ($chemin_liste != "") {
    $listes=array($element=>$chemin_liste);
}
foreach ($listes as $intitule => $valeur) {
    print ("<option value=\"$valeur\">$intitule</option>");
}

?>
</select>
<img src='IMG/icones/application_double.png' onClick='open_liste();'/>
</td>
<td width="50%">
Langue : 
<select name="langue" id="langue" onchange="get_liste('','','','');">
<?PHP
$listes=$GLOBALS["affiche_page"]["parametres"]["langues"];
$langue_defaut=$GLOBALS["affiche_page"]["parametres"]["langue_defaut"];
foreach ($listes as $intitule => $valeur) {
    $selected="";
    if ($valeur == $langue_defaut) {
        $selected=" selected ";
    }
    print ("<option value=\"$valeur\" $selected >$intitule</option>");
}
?>
</select>
</td>

</tr></table>
<br />
<br />
<div id="affiche_liste">

</div>


