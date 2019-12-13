<?php

/**
 * plugin_catalogue_marcxml_get_EAN13()
 * 
 * @param mixed $parametres
 * @param [chaine] => la chaine
 * 
 * @return [EAN] => l'EAN au format standard
 */
function plugin_catalogue_marcxml_get_EAN13 ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["EAN"]="";
    
    $chaine=$parametres["chaine"];
    
    $clef="";
    
    $chaine=trim($chaine);
    $chaine=eregi_replace ("-","",$chaine);
    $chaine=eregi_replace ("_","",$chaine);
    $chaine=eregi_replace ("\.","",$chaine);
    $chaine=eregi_replace (" ","",$chaine);
    
    if (strlen($chaine)==10) { // ISBN 10 complet avec clef de controle
        $chaine=substr($chaine,0,9); // On enlève la clef de controle
        $chaine="978".$chaine;
        $clef=plugin_catalogue_marcxml_get_EAN13_get_clef($chaine);
        $chaine.=$clef;
        $retour["resultat"]["EAN"]=$chaine;
        return($retour);
    } elseif (strlen($chaine)==9) { // ISBN sans clef de controle
        $chaine="978".$chaine;
        $clef=plugin_catalogue_marcxml_get_EAN13_get_clef($chaine);
        $chaine.=$clef;
        $retour["resultat"]["EAN"]=$chaine;
        return($retour);
    } elseif (strlen($chaine)==13) {  // Code barres commercial
    	if (eregi ("[^0-9]",$chaine)) {
            return($retour);
    	}
    	$retour["resultat"]["EAN"]=$chaine;
        return($retour);
    } elseif (strlen($chaine)==12) {  // Code barres commercial sans clef de controle
    	if (eregi ("[^0-9]",$chaine)) {
        	return($retour);
    	}
    	$clef=plugin_catalogue_marcxml_get_EAN13_get_clef($chaine);
    	$chaine.=$clef;
    	$retour["resultat"]["EAN"]=$chaine;
        return($retour);
    } else { // ISBN erronné
        return($retour);
    }
}

function plugin_catalogue_marcxml_get_EAN13_get_clef($chaine) {
  	// Vérifie qu'il n'y a que des chiffres
  	if (eregi ("[^0-9]",$chaine)) {
    	return("");
	}
	
	// On récupère les 12 caractères
  	$car1=substr($chaine,0,1);
  	$car2=substr($chaine,1,1);
  	$car3=substr($chaine,2,1);
  	$car4=substr($chaine,3,1);
  	$car5=substr($chaine,4,1);
  	$car6=substr($chaine,5,1);
  	$car7=substr($chaine,6,1);
  	$car8=substr($chaine,7,1);
  	$car9=substr($chaine,8,1);
  	$car10=substr($chaine,9,1);
  	$car11=substr($chaine,10,1);
  	$car12=substr($chaine,11,1);
  	
  	// On multiplie les caractères paires par 3
  	$car2=$car2*3;
  	$car4=$car4*3;
  	$car6=$car6*3;
  	$car8=$car8*3;
  	$car10=$car10*3;
  	$car12=$car12*3;
  	
  	// On fait la somme de tout
  	$somme=$car1+$car2+$car3+$car4+$car5+$car6+$car7+$car8+$car9+$car10+$car11+$car12;
  	
  	// On complète jusqu'à un multiple de 10
  	for ($i=0;$i<10;$i++) {
	    if (($somme+$i)%10==0) {
		  	return($i);
		}
	}
}



?>