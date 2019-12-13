<?php
/**
$type_obj="biblio";
$ID_notice=13453;
$plugin_traitement=$GLOBALS["affiche_page"]["parametres"]["plugin_traitement"];
$plugin_xml2marc=$GLOBALS["affiche_page"]["parametres"]["plugin_xml2marc"];

$boucle=100;
$notice_modif="";
$somme1=0;
$somme2=0;

$notice=get_objet_xml_by_id($type_obj, $ID_notice);
if ($notice=="") {
    $retour["resultat"]=0;
    $retour["erreur"]="Notice $ID_notice de type $type_obj inexistante";
    $output = $json->encode($retour);
    print($output);
    die ("");
}

$tvs_marcxml=new tvs_marcxml(array());
$tvs_marcxml->load_notice($notice);

    
$t1=microtime(true);
for ($i=0 ; $i< $boucle ; $i++) {
//$tmps1=microtime(true);    
    // On convertit la notice en array conforme à marcxml (gestion des datafields, des indicateurs...)
    $tmp=applique_plugin($plugin_xml2marc, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $array_marc=$tmp["resultat"]["notice"];
    $nb_encode=$tmp["resultat"]["nb_encode"];
    

} // fin de la boucle
$t2=microtime(true);


//$ratio1=$somme1/$boucle;
//$ratio2=$somme2/$boucle;
$diff=$t2-$t1;
//$ratio3=$diff/$boucle;
$ratio=$diff/$boucle;
$nb_encode_glob=$nb_encode*$boucle;

print ("$boucle notices traitees en $diff secondes  : $ratio sec. par notice<br><br>");
print ("$nb_encode encodages pour chaque notice soit $nb_encode_glob encodages au total <br><br>");
//print ("$boucle notices traitees en $diff secondes <br><br>xml2marc : $ratio1 unimarciso : $ratio2 GLOBAL : $ratio3 par notice<br><br>");

//$notice_xml=$notice_modif->saveXML();
//print($notice_modif);
print_r($array_marc);

//print_r($_SESSION);
**/


$lim=1000;
//$chaine="Loup dÂecouvre un livre magique dans son grenier qui permet de voyager dans le temps. Ainsi va-t-il traverser les Âepoques, des dinosaures Áa Jules CÂesar.";
$chaine="ça va mémé et pèpère à l'heure ?";
$plugin_encodage=array("nom_plugin"=>"catalogue/marcxml/encodage/iso5426", "parametres"=>array("sens"=>"ab", "utf8_encode"=>"0", "utf8_decode"=>"1"));


print ("$chaine");
$tmps1=microtime(true); 
for ($idx=0;$idx<=$lim;$idx++) {
    $tmp=applique_plugin($plugin_encodage, array("chaine"=>$chaine));
    if ($tmp["succes"] != 1) {
        $output = $json->encode($tmp);
        print($output);
        die("");
    }
    $chaine2=$tmp["resultat"]["chaine"];  
}
$tmps2=microtime(true); 
$diff=$tmps2-$tmps1;
$ratio=$diff/$lim;

print ("<br><br>$chaine2");

print ("<br><br> $lim en $diff sec. soit $ratio par element");


?>