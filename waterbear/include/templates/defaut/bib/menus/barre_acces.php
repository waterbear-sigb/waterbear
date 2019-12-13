<br />

<table width="99%">
<tr>
<td width="95%">
&nbsp;
</td>
<td width="95%" class="fond_icones">



<?PHP

$liste_icones=$GLOBALS["affiche_page"]["parametres"]["icones_barre_laterale"];
$schedule_recherches=$GLOBALS["affiche_page"]["parametres"]["schedule_recherches"];
$liste_skins=$GLOBALS["affiche_page"]["parametres"]["skins"];
$skin_defaut=$GLOBALS["affiche_page"]["parametres"]["skin_defaut"];
if ($_SESSION["system"]["skin"] != "") {
    $skin_defaut=$_SESSION["system"]["skin"];
}

foreach ($liste_icones as $icone) {
    $img=$icone["img"];
    $lien=$icone["lien"];
    $aide=$icone["aide"];
    
    $img=add_skin($img);
    


?>

<img src="<?PHP print ($img);?>" title="<?PHP print ($aide);?>" onclick="menu_action_clic('', '', '<?PHP print ($lien)  ?>')"/> 
<br /><br />

<?PHP
} // fin du foreach

$img_deplie=add_skin("IMG/icones/application_side_contract.png");
print("<img src=\"$img_deplie\" onclick=\"barre_laterale('fermer');\"/>");
?>


<br /><br />

<?PHP

// Icones schedule_recherches
/**
foreach ($schedule_recherches["recherches"] as $recherche) {
    $lien=$recherche["lien"];
    $code=$recherche["code"];
    print ("<img id=\"$code\" onclick=\"menu_action_clic('', '', '$lien')\" src=\"\" title=''/><br/><br/> \n");
}
**/

?>

</td>
</tr>
</table>
<br />



<table  class="barre_acces_bouton">
<?PHP

$liste_boutons=$GLOBALS["affiche_page"]["parametres"]["boutons_barre_laterale"];

foreach ($liste_boutons as $bouton) {
    $clef=$bouton["clef"];
    $valeur=$bouton["valeur"];


?>


<table  class="barre_acces_bouton">
<tr>
<td class="barre_acces_bouton_vide_cote">&nbsp;</td>
<td class="barre_acces_bouton"><a href="#" class="barre_acces_bouton" onclick="menu_action_clic('', '', '<?PHP print ($valeur)  ?>')"><?PHP print ($clef)  ?></a></td>
<td class="barre_acces_bouton_vide_cote">&nbsp;</td>
</tr>
<tr>
<td class="barre_acces_bouton_vide_separation" colspan="3"></td>

</tr>

<?PHP
} // fin du foreach
?>
</table >



<br />




<table class="barre_acces_system">
<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
Utilisateur
</td>
<td class="barre_acces_system_valeur">

<a href="bib.php?reset_user=1&module=<?PHP print ($_REQUEST["module"]); ?>">
<?PHP print ($_SESSION["system"]["nom"])  ?>
</a>
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>

<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
Poste
</td>
<td class="barre_acces_system_valeur">
<?PHP print ($_SESSION["system"]["nom_poste"])  ?>
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>

<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
Biblio.
</td>
<td class="barre_acces_system_valeur">
<?PHP print ($_SESSION["system"]["bib"])  ?>
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>

<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
Langue
</td>
<td class="barre_acces_system_valeur">
<?PHP print ($_SESSION["system"]["langue"])  ?>
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>

<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
DB
</td>
<td class="barre_acces_system_valeur">
<?PHP print ($_SESSION["system"]["DB"])  ?>
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>

<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
Version
</td>
<td class="barre_acces_system_valeur">
<?PHP print ($GLOBALS["tvs_global"]["conf"]["version"]["version_soft"])  ?> (soft) <br /> <?PHP print (get_registre("system/version/ID"));  ?> (db)
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>

<?PHP if ($GLOBALS["tvs_global"]["conf"]["ini"]["mwb_bool_master"] == 1) {  ?>
<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
MetaWaterbear
</td>
<td class="barre_acces_system_valeur">
<b>Master</b>
</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>
<?PHP }  ?>

<tr>
<td class="barre_acces_system_vide">&nbsp;</td>
<td  class="barre_acces_system_clef">
Skin
</td>
<td class="barre_acces_system_valeur">
<select onchange="change_skin(this)">
<?PHP

foreach ($liste_skins as $code_skin => $valeur_skin) {
    $selected="";
    if ($code_skin == $skin_defaut) {
        $selected="selected";
    }
    print ("<option value='$code_skin' $selected>$valeur_skin</option> \n");
}
?>

</select>


</td>
<td class="barre_acces_system_vide">&nbsp;</td>
</tr>


</table>

