




<!--CHOIX DE L'OBJET =============================== -->

<div  id="choix_objet" style="position: absolute ; top: 20 ; left: 10 ; height: 100 ; width: 100% ">
<?PHP print (get_intitule("bib/admin/objets", "l_objets", array()));  ?> : 
<select ID="combo_objets" onChange="affiche_objet()">
<option value="chargement">chargement...</option>
</select>
<!--
<img src="IMG/icones/accept.png"  alt="<?PHP print (get_intitule("bib/admin/objets", "bt_select_objet", array()));  ?>" title="<?PHP print (get_intitule("bib/admin/objets", "bt_select_objet", array()));  ?>" onClick="affiche_objet()"/>&nbsp;&nbsp;
<img src="IMG/icones/delete.png"  alt="<?PHP print (get_intitule("bib/admin/objets", "bt_suppr_objet", array()));  ?>" title="<?PHP print (get_intitule("bib/admin/objets", "bt_suppr_objet", array()));  ?>"  onClick="confirm_supprimer_objet()"/>
-->
&nbsp; &nbsp; &nbsp; &nbsp; 
<?PHP print (get_intitule("bib/admin/objets", "l_creer_nouvel_objet", array()));  ?> : 
<input type="text" ID="field_new_objet"/>
<img src="IMG/icones/add.png"  alt="<?PHP print (get_intitule("bib/admin/objets", "bt_crea_objet", array()));  ?>" title="<?PHP print (get_intitule("bib/admin/objets", "bt_crea_objet", array()));  ?>" onClick="create_objet()"/>
<!-- Vider l'objet -->
<span id="empty_objet">

</span>

</div>




<!--ONGLETS=============================== -->

<div id="container" style="position: absolute ; top: 80 ; left: 10 ; height: 100% ; width: 80% "></div>

