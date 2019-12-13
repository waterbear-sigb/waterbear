

<?php

$module=$_REQUEST["module"];

if ($erreur != "") {
    print ("<p><b>$erreur</b></p><br><br>\n");
}

$liste_postes=get_registre("system/postes");
$liste_langues=get_registre("system/langues");
$IP=$_SERVER["REMOTE_ADDR"];
$langue_defaut=$GLOBALS["tvs_global"]["conf"]["ini"]["code_langue_defaut"];
$bool_poste_ip_uniquement=$GLOBALS["tvs_global"]["conf"]["ini"]["bool_poste_ip_uniquement"];
$poste_IP="";

// poste obligatoire SAUF si aucun poste défini dans le registre
$html_select_poste="<select  style='width:100%' size='0' name='poste'><option></option>";
if (is_array($liste_postes) && count($liste_postes)>0){ // si au moins un poste défini, on rend le champ obligatoire
    $html_select_poste="<select required style='width:100%' size='0' name='poste'><option></option>";
}
$html_select_langue="<select   style='width:100%' size='0' name='langue'><option></option>";



foreach ($liste_postes as $idx => $poste) {
    $selected="";
    if ($poste["IP"] == $IP) {
        $poste_IP=$idx;
        $selected="selected";
    }
    $html_select_poste.="<option value=\"$idx\" $selected >$idx</option>\n";
}
$html_select_poste.="</select>";

foreach ($liste_langues as $idx => $langue) {
    $selected="";
    if ($idx == $langue_defaut) {
        $selected="selected";
    }
    $html_select_langue.="<option value=\"$idx\" $selected >$langue</option>\n";
}
$html_select_langue.="</select>";


?>
<html>
<head>
<title><?PHP print (get_intitule ("bib/menus/authentification", "titre_page", array()));  ?></title>
<style type="text/css">
* {font-family: sans-serif;}
</style>

<?PHP
$agent = $_SERVER['HTTP_USER_AGENT'];
if(preg_match('/Firefox/i',$agent)) {
    // on ne fait rien
} else {
    //print ("<script language='javascript'>alert(\"".utf8_encode("ATTENTION : � l'heure actuelle, Waterbear est con�u pour fonctionner avec Firefox. Si vous utilisez un autre navigateur vous risquez de rencontrer des bugs. ")."\");</script>");
}

?>

</head>
<body>
<div style="margin-left: auto; margin-right: auto; margin-top:  10%; background-color: #86A7E8; padding:  10px; border-width: 2px; border-color: black; border-style: solid; width: 300px; ">
<form action="bib.php" method="POST">
<input type="hidden" value="<?PHP print($module);  ?>" name="module" />
<table>
<tr>
<td><?PHP print (get_intitule ("bib/menus/authentification", "login", array()));  ?></td>
<td><input style="width:100%" type="text" name="login" /></td>
</tr>
<tr>
<td><?PHP print (get_intitule ("bib/menus/authentification", "mdp", array()));  ?></td>
<td><input  style="width:100%" type="password" name="mdp" /></td>
</tr>
<tr>
<td><?PHP print (get_intitule ("bib/menus/authentification", "poste", array()));  ?></td>
<td><?PHP
if ($bool_poste_ip_uniquement == "1") {
    if ($poste_IP == "") {
        print ("l'adresse IP de votre poste n'est pas reconnue");
    } else {
        print ("$poste_IP ($IP) ");
        print ("<input type='hidden' value=\"$poste_IP\" name=\"poste\" />");
    }
} else {
    print ($html_select_poste);  
}
 
 ?></td>
</tr>
<tr>
<tr>
<td><?PHP print (get_intitule ("bib/menus/authentification", "langue", array()));  ?></td>
<td><?PHP print ($html_select_langue); ?></td>
</tr>
<tr>

<td colspan="2" align="center"><br /><input type="submit" value="OK" name="valider" /></td>
</tr>
</table> 
</form>
</div>
</body>
</html>