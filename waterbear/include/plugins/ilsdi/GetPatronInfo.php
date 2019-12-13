<?php
function plugin_ilsdi_GetPatronInfo ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $patronId=$_REQUEST["patronId"];
    
    $plugin_get_prets=$parametres["plugin_get_prets"];
    $plugin_get_resas=$parametres["plugin_get_resas"];
    $plugin_get_nom_bib=$parametres["plugin_get_nom_bib"];
    
    $lecteur=array();
    
    // 1) on récupère les infos lecteur
    $ligne_lecteur=get_objet_by_id("lecteur", $patronId);
    $lecteur["patronId"]=$patronId;
    $lecteur["lastName"]=$ligne_lecteur["a_nom"];
    $lecteur["firstName"]=$ligne_lecteur["a_prenom"];
    $lecteur["loans"]=array();
    $lecteur["holds"]=array();
    
    // 2) on récupère les prêts
    $tmp=applique_plugin($plugin_get_prets, array("ID_lecteur"=>$patronId));
    if ($tmp["succes"] != 1) {
        // ??
    }

    $nb_prets=$tmp["resultat"]["nb_notices"];
    $prets=$tmp["resultat"]["notices"];
    
    foreach ($prets as $pret) {
        $ID_biblio=$pret["a_id_biblio"];
        $ID_exe=$pret["a_id_exe"];
        $date_retour_prevu=$pret["a_date_retour_prevu"];
        $titre=$pret["a_titre_biblio"];
        $auteur=$pret["a_auteur_biblio"];
        $bib=$pret["a_bib_exe"];
        $tmp=applique_plugin($plugin_get_nom_bib, array("texte"=>$bib));
        $bib=$tmp["resultat"]["texte"];
        array_push($lecteur["loans"], array("bibId"=>$ID_biblio, "itemId"=>$ID_exe, "title"=>$titre, "author"=>$auteur, "locationLabel"=>$bib, "dueDate"=>$date_retour_prevu));
    }
    
    // 3) On récupère les résas
    $tmp=applique_plugin($plugin_get_resas, array("ID_lecteur"=>$patronId));
    if ($tmp["succes"] != 1) {
        // ??
    }
    $nb_resas=$tmp["resultat"]["nb_notices"];
    $resas=$tmp["resultat"]["notices"];
    
    foreach ($resas as $resa) {
        $ID_biblio=$resa["a_id_biblio"];
        $ID_exe=$resa["a_id_exe_affecte"]; // uniquement si la résa a été affectée
        $titre=$resa["a_titre_biblio"];
        $auteur=$resa["a_auteur_biblio"];
        $bib=$resa["a_bib_destination"];
        $tmp=applique_plugin($plugin_get_nom_bib, array("texte"=>$bib));
        $bib=$tmp["resultat"]["texte"];
        $priority="1"; // TMP !!!
        array_push($lecteur["holds"], array("bibId"=>$ID_biblio, "itemId"=>$ID_exe, "title"=>$titre, "author"=>$auteur, "locationLabel"=>$bib, "priority"=>$priority));
    }
    
    // 4) On génère le XML
    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml.="<GetPatronInfo>\n";
    $xml.="<patronId>".$lecteur["patronId"]."</patronId>\n";
    $xml.="<lastName>".$lecteur["lastName"]."</lastName>\n";
    $xml.="<firstName>".$lecteur["firstName"]."</firstName>\n";
    $xml.="<loans>\n";
    foreach ($lecteur["loans"] as $loan) {
         $xml.="<loan>\n";
         $xml.="<bibId>".$loan["bibId"]."</bibId>\n";
         $xml.="<itemId>".$loan["itemId"]."</itemId>\n";
         $xml.="<title>".$loan["title"]."</title>\n";
         $xml.="<author>".$loan["author"]."</author>\n";
         $xml.="<locationLabel>".$loan["locationLabel"]."</locationLabel>\n";
         $xml.="<dueDate>".$loan["dueDate"]."</dueDate>\n";
         $xml.="</loan>\n";
    }
    $xml.="</loans>\n";
    $xml.="<holds>\n";
    foreach ($lecteur["holds"] as $hold) {
         $xml.="<hold>\n";
         $xml.="<bibId>".$hold["bibId"]."</bibId>\n";
         $xml.="<itemId>".$hold["itemId"]."</itemId>\n";
         $xml.="<title>".$hold["title"]."</title>\n";
         $xml.="<author>".$hold["author"]."</author>\n";
         $xml.="<locationLabel>".$hold["locationLabel"]."</locationLabel>\n";
         $xml.="<priority>".$hold["priority"]."</priority>\n";
         $xml.="</hold>\n";
    }
    $xml.="</holds>\n";
    $xml.="</GetPatronInfo>\n";
   
   
    $retour["resultat"]["xml"]=$xml;
    return ($retour);
    
}
?>