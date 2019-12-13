<?PHP
function tvs_log ($log, $action, $parametres) {
  	if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"][$log]["bool"] != 1) {
	    return (1);
	}
	$log_path=$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"]."/".$GLOBALS["tvs_global"]["conf"]["ini"]["log"][$log]["fichier"];
    $tab=$GLOBALS["tvs_global"]["conf"]["ini"]["log"][$log]["tab"];
    
    
  	$date=date("d/m/Y H:i:s");
  	$IP=$_SERVER["REMOTE_ADDR"];
    if (is_array($parametres)) {
        $tolog=implode(" *** ",$parametres);
     } else {
        $tolog=$parametres;
     }
  	$tolog="$date \t $IP \t $log \t $action \t $tab $tolog \n";
  	$fichier=fopen($log_path,"at");
  	fwrite($fichier,$tolog);
  	fclose($fichier);
  	
}

function tvs_log_txt ($log, $parametres) {
  	if ($GLOBALS["tvs_global"]["conf"]["ini"]["log"][$log]["bool"] != 1) {
	    return (1);
	}
	$log_path=$GLOBALS["tvs_global"]["conf"]["ini"]["log"]["log_path"]."/".$GLOBALS["tvs_global"]["conf"]["ini"]["log"][$log]["fichier"];
  	$date=date("d/m/Y H:i:s");
  	$IP=$_SERVER["REMOTE_ADDR"];
  	$tolog=implode("\n",$parametres);
  	$tolog="\n$date - $IP \n$tolog .\n ----------------------------------------------------------------- \n";
  	$fichier=fopen($log_path,"at");
  	fwrite($fichier,$tolog);
  	fclose($fichier);
  	
}


function dbg_log ($param) {
    if (is_array($param)) {
        $param=var_export($param, true);
    }
    tvs_log ("dbg", "DEBUG", array($param));
}



?>