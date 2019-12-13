
<p>Collez dans la zone ci-dessous les noeuds à importer dans le registre. Il s'agit d'un long texte ressemblant à ceci : [{"type":"supprimer_noeud","chemin":.....,"description":""}] obtenu grâce à l'outil 'exporter un noeud du registre'<br />
Pour exporter un noeud, allez dans le registre, positionnez-vous sur le noeud à exporter et cliquez sur l'icône 'exporter ce noeud (pour sauvegarde)'.<br />
Il est possible de copier une branche du registre d'un site vers un autre </p>
<p><font color="red">ATTENTION : N'utilisez cet utilitaire que si vous êtes sûr de ce que vous faites. Mal utilisé, il peut rendre votre site inutilisable.</font></p>

<form action="bib.php" method="POST">
<input type="hidden" name="module" value="<?PHP print($_REQUEST["module"]);  ?>" />
<textarea name="chaine" style="width: 700px; height: 500px;"></textarea>
<br /><br />
<input type="submit" value="Valider" name="valider" />


</form>