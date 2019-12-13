<html>
<head>
<script language="javascript">
function init() {
    window.print();
}


</script>

<?PHP affiche_template ("div/include_js_et_css.php", array()); ?>

</head>
<body onload="init()">
<table width="100%">
<tr>
<td width="40%">
<?PHP print ($bloc_livraison)  ?>
</td>

<td width="20%">&nbsp;</td>
<td width="40%">&nbsp;</td>


</tr>

<td width="40%">&nbsp;</td>
<td width="20%">&nbsp;</td>

<td width="40%">
<?PHP print ($bloc_fournisseur)  ?>
</td>

</table>

<br />
<br />

<table width="100%">
<tr>
<td width="100%">
<?PHP print ($bloc_lignes_commande)  ?>
</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>
<?PHP print ($bloc_somme)  ?>
</td>
</tr>
</table>



</body>
</html>