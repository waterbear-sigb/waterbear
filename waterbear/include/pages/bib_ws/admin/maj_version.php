<?php
// Mise à jour de la version de Waterbear
// le n° de version logicielle est indiqué dans conf/version.php
// le n° de version DB est indiqué dans le registre : system/version/ID


include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."fonctions/metawb.php");

print ("maj_version ".$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]." <br> \n");

$version_db=get_registre ("system/version/ID");
if ($version_db==$GLOBALS["tvs_global"]["conf"]["version"]["version_soft"]) {
    die ("version a jour : $version_db");
}

if ($version_db>$GLOBALS["tvs_global"]["conf"]["version"]["version_soft"]) {
    die ("ANOMALIE : la version de la base de donnees ($version_db) est superieure a celle du logiciel ".$GLOBALS["tvs_global"]["conf"]["version"]["version_soft"]);
}

$date=date("Y-m-d");

while ($version_db < $GLOBALS["tvs_global"]["conf"]["version"]["version_soft"]) {
    $version_db++;
    print (" <br> application de la mise a jour vers la version $version_db <br> \n");
    nettoie_registre();
    include_once ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/maj_version/maj".$version_db.".php");
    nettoie_registre();
    set_registre ("system/version/ID", $version_db, "version maj le $date");
    print ("<br>Mise a jour $version_db terminee <br><hr> \n");
}

print ("FIN des mises a jour \n");




?>