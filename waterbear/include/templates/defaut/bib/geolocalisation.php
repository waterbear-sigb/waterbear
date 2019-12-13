<!DOCTYPE html>
<html>
<head>
<title><?PHP print (get_intitule ("", $GLOBALS["affiche_page"]["parametres"]["titre_page"], array()));?></title>
<link rel="icon" type="image/png" href="<?PHP print($GLOBALS["affiche_page"]["parametres"]["favicon"]) ?>" />

<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0; padding: 0 }
  #map_canvas { height: 100% }
</style>

<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?sensor=false">
</script>

<script type="text/javascript">
var map;

  function init() {
    init_map();
    var points=window.opener.get_points(window);
  }
  
  function init_map () {
    var latlng = new google.maps.LatLng(<?PHP print ($GLOBALS["affiche_page"]["parametres"]["coordonnees_centre"]);?>);
    var myOptions = {
      zoom: <?PHP print ($GLOBALS["affiche_page"]["parametres"]["zoom"]);?>,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }
  
  function affiche_points (points) {
    //alert (points);
    var liste_coo=points.split(",");
    liste_coo.push("bidon"); // on rajoute un élément bidonàla fin
    var nb_lecteurs=1;
    for (idx_coo in liste_coo) {
        idx_coo=parseInt(idx_coo);
        var size=<?PHP print ($GLOBALS["affiche_page"]["parametres"]["taille_icone"]);?>;
        var coo=liste_coo[idx_coo].trim();
        var coo2=liste_coo[idx_coo+1].trim();
        if (coo == coo2) {
            nb_lecteurs++;
            continue;
        }

        size=size+(nb_lecteurs*<?PHP print ($GLOBALS["affiche_page"]["parametres"]["coef_icone"]);?>);

        var lat_long=coo.split(" ");
        if (lat_long.length == 2) {
            var lat=lat_long[0];
            var longi=lat_long[1];
            var myLatlng = new google.maps.LatLng(lat,longi);
            var icone = new google.maps.MarkerImage ("<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_icone"]);?>", new google.maps.Size(size, size), new google.maps.Point(0,0), new google.maps.Point(size/2,size/2), new google.maps.Size(size, size));
            var marker = new google.maps.Marker({
                position: myLatlng,
                title: nb_lecteurs+" personnes",
                icon: icone
            });
            marker.setMap(map);
       } else {
           // alert ("pb : "+lat_long.length+" elements");
       }
       nb_lecteurs=1;
    }
  }

</script>
</head>



<body onload="init()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>