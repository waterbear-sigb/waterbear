<HTML>
<HEAD>
<title><?PHP  print (get_intitule("erreurs/erreur_page", "page_titre", array())) ?></title>
</HEAD>
<BODY>
<?PHP  print (get_intitule("erreurs/erreur_page", "message", array("page"=>$page, "message"=>$message))) ?>

</BODY>
</HTML>