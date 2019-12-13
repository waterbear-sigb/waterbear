<?PHP
// Début SESSION
session_start();
$sid=session_id();
$sname=session_name();
setcookie($sname,$sid);

// SI RESET
if ($reset==1) {
  	//print ("<b>RESET</b><br>\n");
  	$_SESSION = array();
  	if (isset($_COOKIE[session_name()])) {
    	setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
	
	// On relance une nouvelle session
	session_start();
	$sid=session_id();
	$sname=session_name();
	setcookie($sname,$sid);
}





?>