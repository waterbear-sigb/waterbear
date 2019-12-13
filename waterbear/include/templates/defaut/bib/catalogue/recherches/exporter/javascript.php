<!--  Template "javascript" de la page -->
<script language="javascript">

function lance_requete (requete) {
    var tableau = window.opener.recherchator.formulaire_2_array();
    var chaine = window.opener.recherchator.formulaire_2_json(tableau); 
    document.getElementById("param_recherche").value=chaine;
    //alert (chaine);
    document.getElementById("lance_requete").submit();
}







function init () {
    lance_requete();
}


</script>