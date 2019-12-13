Souhaitez-vous lancer les traitements ?

<form action="bib.php" method="post" >
<input type="hidden" value="<?PHP  print( $GLOBALS["affiche_page"]["parametres"]["auto_page"]);  ?>" name="module" />
<input type="hidden" value="1" name="bool_lance_traitements" />
<input type="submit" value="Lancer les traitements" />
</form>