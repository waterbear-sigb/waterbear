<?PHP
// Cette fonction récupère un fichier uploadé et le copie en le renommant (clef stockée dans le registre)
// paramètre : clef du fichier (i.e. nom du champ "file" dans le formulaire HTML)
// retourne un tableau associatif :
// ["chemin"] => le chemin complet du fichier renommé
// ["taille"] => taille du fichier
// ["erreur"] => Si erreur
function upload_file ($clef_fichier) {
  	$retour=array();
	if (is_uploaded_file($_FILES[$clef_fichier]['tmp_name'])) {
  		$chemin_client=$_FILES['fichier']['name'];
  		$chemin_serveur=$_FILES['fichier']['tmp_name'];
  		$taille=$_FILES['fichier']['size'];
        $id_upload=get_compteur("id_upload");
  		if ($id_upload === false) {
		    return (array("erreur"=>get_intitule("erreurs/messages_erreur", "impossible_recuperer_compteur", array("compteur"=>"id_upload"))));
		}
        if (!move_uploaded_file($chemin_serveur, $GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"]."/upload_".$id_upload.".file")) {
		  	return (array("erreur"=>get_intitule("erreurs/messages_erreur", "impossible_deplacer_fichier", array("source"=>$chemin_serveur, "destination"=>$GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"]."/upload_".$id_upload.".file"))));
		}
		$retour["chemin"]=$GLOBALS["tvs_global"]["conf"]["ini"]["upload_path"]."/upload_".$id_upload.".file";
		$retour["taille"]=$taille;
		return($retour);
    } else {
        $retour["erreur"]=$_FILES[$clef_fichier]['error'];
    }
}

// cette fonction crée un fichier à downloader (généré de manière incrémentielle)
// Il retourne un pointeur vers le fichier [file] et le chemin complet du fichier [chemin] ainsi que son nom [nom] et éventuellement des erreurs [erreur]
// il peut recevoir optionnellement des paramètres : [extension] (par défaut txt) et [mode] (par défaut wt)

function download_file ($parametres) {
    $extension=$parametres["extension"];
    if ($extension=="") {
        $extension="txt";
    }
    $mode=$parametres["mode"];
    if ($mode=="") {
        $mode="wt";
    }
    $retour=array();
    $id_download=get_compteur("id_download");
    if ($id_upload === false) {
        return (array("erreur"=>get_intitule("erreurs/messages_erreur", "impossible_recuperer_compteur", array("compteur"=>"id_download"))));
    }
    $chemin_fichier=$GLOBALS["tvs_global"]["conf"]["ini"]["download_path"]."/download_".$id_download.".".$extension;
    $url=$GLOBALS["tvs_global"]["conf"]["ini"]["download_path_short"]."/download_".$id_download.".".$extension;
    $file=fopen($chemin_fichier, $mode);
    $retour["file"]=$file;
    $retour["chemin"]=$chemin_fichier;
    $retour["url"]=$url;
    
    return($retour);
}


















?>