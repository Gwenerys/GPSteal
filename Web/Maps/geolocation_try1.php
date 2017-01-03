<?php
	ini_set('display_errors','on');
	error_reporting(E_ALL);
	//echo "ceci est un test<br/>";
	class MyDB extends SQLite3
	{
		function __construct()
		{
		    $this->open('db/test.db');
		}
	}
	//$db = new SQLite3('db/dbtest.db');
	$db = new MyDB();

	//$db->exec('CREATE TABLE if not exists foo (bar STRING) ');
	//$db->exec("INSERT INTO foo (bar) VALUES ('Ceci est un test')");
	$voiture = "saint-maur";
	$reponse = $db->query("SELECT * FROM  `lat_long` WHERE voiture = '$voiture'");
	$localisation = $reponse->fetchArray();
	$lat = $localisation['lat'];
	//echo "<br/>";
	$long = $localisation['long'];
?>


<!DOCTYPE html>

<html>
  <head>
    <title>Geolocation</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
  	<h1 align=center><b> GPSteal localisation </b></h1>
    <div id="map">
    <script>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see the error "The Geolocation service
// failed.", it means you probably did not give permission for the browser to
// locate you.

function initMap() {
  	// My positionning !
  	var map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: 0, lng: 0},
		zoom: 10
	});
	
	// Pour transformer les coordonnées GPS en adresse
	var geocoder = new google.maps.Geocoder;
	
	/*Voiture*/
	var latCar = <?php echo $lat; ?>;
	var longCar = <?php echo $long; ?>;
	var posCar = {lat:latCar,lng:longCar};

	var carString = '<div id="content">'+
	  '<div id="Car">'+
	  '</div>'+
	  '<h3 id="firstHeading" class="firstHeading">The car was here the last time GPS coordinates were received : </h3>'+
	  '</div>';
	  
	var carWindow = new google.maps.InfoWindow();
    
    var imageCar = 'images/car.png';
      
	var markerCar = new google.maps.Marker({
		position: posCar,
		map: map,
		icon: imageCar
	});
	
	geocodeLatLng(geocoder, map, carWindow, posCar, carString, markerCar);
	markerCar.addListener('click', function() {
		carWindow.open(map, markerCar);
		//geocodeLatLng(geocoder, map, carWindow, posCar, carString, markerCar);
	});
	
	map.setCenter(posCar);
	
	/*Personne*/
	var personString = '<div id="content">'+
      '<div id="Me">'+
      '</div>'+
      '<h3 id="firstHeading" class="firstHeading">Your position : </h3>'+
      '</div>';
      
    var personWindow = new google.maps.InfoWindow();

  	// Try HTML5 geolocation.
  	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
		  var posPerson = {
			lat: position.coords.latitude,
			lng: position.coords.longitude
		  };

      //infoWindow.setPosition(pos);
      //infoWindow.setContent('Location found.');
      //map.setCenter(posCar);
      
      // Optionnel : 
      var imagePerson = 'images/person.png';
      
      var markerPerson = new google.maps.Marker({
		position: posPerson,
		map: map,
		icon: imagePerson
	  	});
	  
	  geocodeLatLng(geocoder, map, personWindow, posPerson, personString, markerPerson);
	  markerPerson.addListener('click', function() {
		personWindow.open(map, markerPerson);
		//geocodeLatLng(geocoder, map, personWindow, posPerson, personString, markerPerson);
	  });
  	
    }, function() {
      var infoWindow = new google.maps.InfoWindow({map: map});
      handleLocationError(true, infoWindow, map.getCenter());
    });
  } else {
    // Browser doesn't support Geolocation
    handleLocationError(false, infoWindow, map.getCenter());
  }
}

// Fonction d'erreur si le navigateur n'a pas autorisé la géolocalisation
function handleLocationError(browserHasGeolocation, infoWindow, pos) {
  infoWindow.setPosition(pos);
  infoWindow.setContent(browserHasGeolocation ?
                        'Error: The Geolocation service failed.' :
                        'Error: Your browser doesn\'t support geolocation.');
}

// Fonction de transformation des coordonnées GPS en adresse lisible
function geocodeLatLng(geocoder, map, Window, pos, str, marker){
	geocoder.geocode({'location': pos}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
		  if (results[1]) {
		    //carWindow.open(map, markerCar);
		    Window.setContent(str + '<h3>' + results[1].formatted_address + '</h3>');
		    //Window.open(map, marker);
		  } else {
		    Window.alert('No results found');
		  }
		} else {
		  Window.alert('Geocoder failed due to: ' + status);
		}
	  });
}
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAUBc6FxioMYwPuBVZIRsHzigXJbV95XNs&signed_in=true&callback=initMap"
        async defer>
    </script>
    </div>
    <div id="info">
		Hello ! <br/>
		Here are the informations about your car : <br/> <br/>
			Latitude : <?php echo $lat?> <br/>
			Longitude : <?php echo $long?> <br/> <br/>
			To know the approximate address of the location of your car, just click on the car symbol <br/>
			<img src="images/car.png" alt="Car symbol" style="width:30px;height:30px;">
		<br/><br/>
		The person symbol represents your position <br/>
		<img src="images/person.png" alt="Car symbol" style="width:30px;height:30px;">
    </div>
    <div id="button">
		<FORM>
		<INPUT TYPE="button" onClick="history.go(0)" VALUE="Refresh the page">
		</FORM>
    </div>
  </body>
</html>
