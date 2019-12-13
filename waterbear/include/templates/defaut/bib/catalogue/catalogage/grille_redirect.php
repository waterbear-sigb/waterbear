<?php

if ($erreur != "") {
    die($erreur);
}




?>

<script language="javascript">
<?PHP
$str_id_appel="";
if ($id_appel != "") {
    $str_id_appel="&id_appel=".$id_appel;
}
?>
window.location.href="bib.php?module=<?PHP  print($grille) ?>&masque=<?PHP  print($masque) ?>&ID_notice=<?PHP  print($ID_notice) ?><?PHP  print($str_id_appel) ?>";
</script>