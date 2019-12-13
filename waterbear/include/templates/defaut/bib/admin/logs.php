

<?php
$log_path_short=$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path_short"];

print ($a_afficher."<br><br>");

print ("<table>");
foreach ($liste_fichiers as $fichier) {
    if (substr ($fichier, 0, 1) == ".") {
        continue;
    }
    $chemin_complet="$log_path_short/$fichier";
    print ("<tr><td><a href='$chemin_complet' target='blank'> $fichier </a> </td><td>  <img src='IMG/icones/cross.png' onclick=\"window.location.href='bib.php?module=admin/logs&delete_log=$fichier';\"/></td></tr> \n");
}
print ("</table>");



?>
<br />
<a href="bib.php?module=admin/logs&delete_log=tout">Effacer tout</a>