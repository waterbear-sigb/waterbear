<script type="text/javascript" src="js/yui/container/container_core-min.js"></script> 
<script type="text/javascript" src="js/yui/menu/menu.js"></script> 

<SCRIPT language="javascript">
function menu_action_clic (a, b, url) { 
	//alert(url);
	// JAVASCRIPT
	if (url.substring(0,3)=="js/") {
	  	url=url.substring(3);
	  	eval (url);
	  	return(true);
	}
	// FORCE NOUVELLE FENETRE
	if (url.substring(0,3)=="nv/") {
	  	url=url.substring(3);
	  	window.open(url);
	  	return(true);
	}
	// COCHE NOUVELLE FENETRE
	if (document.getElementById("bool_open_new").checked==true) {
	  	document.getElementById("bool_open_new").checked=false;
		window.open(url);
	  	return(true);
	}
	// LIEN NORMAL
	window.location.href=url;
}
</SCRIPT>
