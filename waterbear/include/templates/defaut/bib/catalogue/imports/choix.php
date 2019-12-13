<div style="margin:10" width="90%">

<p class="titre5"><?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_titre_page", array()));   ?></p>

<form id="formulaire" action="a_preciser_par_js" enctype="multipart/form-data" method="POST">
<input type="hidden" value="<?PHP  print($GLOBALS["affiche_page"]["parametres"]["ID_operation"]);  ?>" name="ID_operation" />

<table>
<tr>
<td>
<?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_type_objets", array()));   ?> : 
</td>
<td>
<select name="filtre" id="filtre" onchange="get_formulaire();">
<?PHP tmpl_affiche_liste($GLOBALS["affiche_page"]["parametres"]["liste_filtres"])  ?>
</select>
</td>
</tr>
<tr>
<td>
<?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_telecharger_fichier", array()));   ?> : 
</td>
<td>
<input type="file" size="20" name="fichier" />
<input type="hidden" id="import_options" name="import_options" />
</td>
</tr>

<tr>
<td>
<?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_interactif", array()));   ?> : 
</td>
<td>
  


    
    <select name="interactif">
        <option value="1"><?PHP print(get_intitule("div" ,"oui", array()));   ?></option>
        <option value="0" selected="selected"><?PHP print(get_intitule("div" ,"non", array()));   ?></option>
    </select>
</td>
</tr>

<tr>
<td>
<?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_verif", array()));   ?> : 
</td>
<td>

    
     <select name="bool_verif">
        <option value="1"><?PHP print(get_intitule("div" ,"oui", array()));   ?></option>
        <option value="2"><?PHP print(get_intitule("div" ,"diviseur_fichier", array()));   ?></option>
        <option value="0" selected="selected"><?PHP print(get_intitule("div" ,"non", array()));   ?></option>
    </select>
</td>
</tr>

<tr>
<td>
<?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_nom_panier", array()));   ?> : 
</td>
<td>
<div>
<input type="text" name="panier" id="autocomplete_panier"/>
<div id="autocomplete_conteneur"></div>
</div>
</td>
</tr>
<tr><td colspan="2" id="div_aide"></td></tr>
<tr><td id="div_options" colspan="2"></td></tr>




<tr>
<td>
<br/>
<input type="button" onclick="valide_formulaire()" value="<?PHP print(get_intitule("bib/catalogue/imports/choix" ,"l_bouton_valider", array()));   ?>" name="valider" />
</td>
<td>
&nbsp; 
</td>
</tr>
</table>




</form>

























</div>