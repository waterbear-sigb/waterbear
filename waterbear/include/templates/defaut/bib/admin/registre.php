

<!-- DIV contenant l'abre -->
<div  style="position: absolute ; top: 10 ; left: 10 ; height: 500 ; width: 400 ; overflow:scroll">
<div id="arbre" style="position: absolute ; top: 0 ; left: 0 ; height: 5000 ; width: 4000 ">

</div>
</div>

<!-- Presse papier -->
<div id="presse_papier" style="position: absolute ; top: 550 ; left: 10 ; height: 200 ; width: 400 ; ">
<?PHP print (get_intitule("bib/admin/registre", "l_presse_papier", array()));  ?> <br>
<textarea cols="60" rows="1" wrap="OFF" readonly="readonly" id="tree_presse_papier"></textarea>
</div>

<!-- Formulaire -->
<div id="formulaire_noeud" style="position: absolute ; top: 10 ; left: 450 ; height: 200 ; width: 400 ">



<form action="test_registre.php" enctype="application/x-www-form-urlencoded">
	<!--chemin : <input type="text" readonly="readonly" name="type" id="champ_chemin"  size="70"/> <br> -->
	<?PHP print (get_intitule("bib/admin/registre", "l_chemin", array()));  ?> : <textarea cols="70" rows="1" wrap="OFF" readonly="readonly" id="champ_chemin"></textarea><br>
	<?PHP print (get_intitule("bib/admin/registre", "l_nom_noeud", array()));  ?> : <br><input type="text" value="" name="nom" id="champ_nom"  size="30"/> <br>
	
	<?PHP print (get_intitule("bib/admin/registre", "l_valeur", array()));  ?> : <br><textarea wrap="ON" name="valeur" id="champ_valeur" cols="30" rows="10"></textarea> <br>
	<?PHP print (get_intitule("bib/admin/registre", "l_description", array()));  ?> : <br><textarea wrap="ON" name="description" id="champ_description" cols="30" rows="10"></textarea> <br>
	<input type="hidden" name="ID" id="champ_ID"/>
</form>

</div>


