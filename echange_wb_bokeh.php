<?PHP

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Export notices Waterbear vers Bokeh
// ce script PHP doit être lancé selon la fréquence voulue pour assurer les échanges de données entre Waterbear et le catalogue en ligne pour le public (OPAC)
// ce dernier logiciel peut être Bokeh ou un autre logiciel
// Il faut IMPERATIVEMENT renseigner les informations contenues dans la rubrique "A PARAMETRER"
// Le script se connecte d'abord à waterbear pour récupérer les notices, puis écrit le fichier de notices dans un répertoire de Bokeh
// On peut faire des exports incrémentiels (on n'exporte que les notices cataloguées depuis le dernier export) ou totaux (la totalité des notices)
// pour cela, mettre "total" ou "incrementiel" à la valeur $type_export
// A NOTER : les suppressions de notices ne sont exportées que lors de l'export total
// Il est recommandé de faire plusieurs exports incrémentiels par jour, et un export total par semaine (mais vous êtes libres de paramétrer la fréquence voulue).
// Si vous voulez gérer à la fois des exports incrémentiels et totaux, vous devez dupliquer ce fichier. Dans l'un vous mettrez $type_export="incrementiel"; 
// et dans l'autre $type_export="total";
// le fichier écrit s'appelle côté Bokeh s'appellera "incrementiel.pan" ou "total.pan"
// généralement, il faut écrire les fichiers de notices dans le répertoire cosmogramme/fichiers/transferts/ Cela se paramètre dans Cosmogramme (voir la doc de Bokeh pour plus d'infos)
// Pour exécuter ce fichier, il faut passer par PHP. Sous Linux, vous utiliserez la commande "php echange_wb_bokeh.php". Vous pouvez aussi choisir de logger le résultat du script 
// Pour vérifier de temps en temps que tout se passe bien. Dans ce cas, on fera par exemple "php echange_wb_bokeh.php >> echange.log"
// Pour éxécuter périodiquement le script, vous pouvez utiliser CRON sous Linux ou le planificateur de tâches sous Windows

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// A PARAMETRER
$url_waterbear="http://url/de/waterbear"; // indiquer ici l'url d'installation de waterbear (où se trouve le fichier bib.php). PAS de "/" à la fin
$login_waterbear="xxx"; // login d'utilisateur waterbear
$mdp_waterbear="xxx"; // mot de passe utilisateur waterbear
$chemin_bokeh="/home/www/bokeh/cosmogramme/fichiers/transferts"; // emplacement où il faudra copier le fichier unimarc. Le répertoire ne doit PAS avoir un "/" à la fin
$type_export=""; // mettre "total" ou "incrementiel"

$url_waterbear="http://moccam-en-ligne.fr/wb_install"; // indiquer ici l'url d'installation de waterbear (où se trouve le fichier bib.php)
$login_waterbear="superadmin"; // login d'utilisateur waterbear
$mdp_waterbear="superadmin"; // mot de passe utilisateur waterbear
$chemin_bokeh="/home/moccam/BIN"; // emplacement où il faudra copier le fichier unimarc. Le répertoire ne doit PAS avoir un "/" à la fin
$type_export="total"; // mettre "total" ou "incrementiel"

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    
$ctx = stream_context_create(array(
  'http' => array(
      'timeout' => 60*60*5
      )
  )
); 
  
$login_waterbear=urlencode($login_waterbear);
$mdp_waterbear=urlencode($mdp_waterbear);
    
// 1) on récupère les données
$url="$url_waterbear/bib_ws.php?module=externe/afi/export_$type_export&login=$login_waterbear&mdp=$mdp_waterbear";

print ("Export des donnees depuis Waterbear ".date ("d/m/Y ")."\n");
print ("$url \n");

$reponse=file_get_contents($url, 0, $ctx);

    
if (strpos($reponse, "@WB_ERREUR@") !== false) {
    print ("ERREUR \n $reponse \n");
}
    
if ($reponse == "") {
    print ("aucune notice \n");
}
    
// 2) on crée le fichier
$chemin="$chemin_bokeh/$type_export.pan";
$echec="";
$mode="a";
if ($type_export == "total") {
    $mode="w+";
}

print ("ecriture de $chemin \n");
@$file=fopen($chemin, $mode) OR $echec.="fopen ";
@fwrite($file, $reponse) OR $echec.="fwrite ";
@fclose($file) OR $echec.="fclose ";
if ($echec != "") {
    print ("Echec durant les operations $echec \n");
} 
  


?>