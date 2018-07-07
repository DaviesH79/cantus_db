<!DOCTYPE HTML>  
<?php
header("Content-type: text/html; charset=utf8_general_ci; application/json");
include ("getDbConn.php");
	// get a db connection
	$db = new dbConn();
	$conn = $db->getDbConn();
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<style>
.error {color: #FF0000;}
#map {
    height: 400px;
    width: 65%;
	margin-left: auto;
	margin-right: auto;
    }

    </style>
</style>
</head>
<body>  
<h1>Welcome to the Cantus Database</h1>
<h1>"View a Chant Image and Origin"</h1>
<h2 >Select a Library, Manuscript, and Chant to view it's leaf image and origin.</h2>
<?php
	// query to get all libraries
	createLibTemp($conn);
	$fillLib = $conn->query("INSERT INTO libTemp (libSiglum) SELECT distinct libSiglum FROM leaf WHERE imageLink !='null'"); 
	$result = $conn->query("SELECT library, city, libSiglum FROM library JOIN libTemp using (libSiglum)");
?>
<h3>Please select a Library</h3>
<!-- populate the library select drop down -->
<form action="" method="post" id="form">
<select method='post' name='library' id='library' onchange="getManuscript();">
<option disabled="disabled" selected="selected">Select Library</option>
<?php 
	$result->data_seek(0);
	while ($row = $result->fetch_assoc()) {
		echo "<option value='" . $row['libSiglum'] . "'>" . $row['library'] . ", " . $row['city'] . "</option>";
	}
?>
</select>
<script type="text/javascript">
function getManuscript(){
	var libSig = $('#library').val();
	$.ajax({
		type:"POST",
		url:"getManuscript.php",
		data:{
			libSig:libSig
		},
		success: function(response)
		{
			$("#manuscript").html(response);
		}
	});
}
</script>
<!-- Start populating Manuscript dropdown -->
<h3>Please select a Manuscript</h3>
<select method='post' name='manuscript' id='manuscript' onchange="getChants(); getCoords()">
</select>
<script type="text/javascript">
function getChants(){
	var man = $('#manuscript').val();
	$.ajax({
		type:"POST",
		url:"getChants.php",
		data:{
			manuscript:man
		},
		success: function(response)
		{
			$("#chant").html(response);
		}
	});
}
</script>
<script type="text/javascript">
function getCoords(){
	var man = $('#manuscript').val();
	$.ajax({
		type:"POST",
		dataType: "json",
		url:"getCoords.php",
		data:{
			manuscript:man
		},
		success: function(provCoords)
		{
			console.log(provCoords[0].latitude);
			var lat = parseFloat(provCoords[0].latitude);
			var lng = parseFloat(provCoords[0].longitude);
			//var coords = jQuery.parseJSON(provCoords);
			initMap(lng,lat);
		}
	});
}

</script>
<!-- Start populating Chant dropdown with chants that only have images -->
<h3>Please select a Chant</h3>
<select method='post' name='chant' id='chant' onchange="getLeaves();">
</select>
<script type="text/javascript">
function getLeaves(){
	var chant = $('#chant').val();
	$.ajax({
		type:"POST",
		url:"getLeaves.php",
		data:{
			chant:chant
		},
		success: function(response)
		{
			$("#leaf").html(response);
		}
	});
}
</script>
<h3>Please select a Leaf</h3>
<select method='post' name='leaf' id='leaf' onchange="getImage();">
</select>
<script type="text/javascript">
function getImage(){
	var leaf = $('#leaf').val();
	$.ajax({
		type:"POST",
		//dataType: "json",
		url:"getImage.php",
		data:{
			leaf:leaf
		},
		success: function(response)
		{
			$("#image").html(response);
		}
	});
function getImageLink(){
	
}
}
</script>
<p id="image"/>
</form>
<h3>Provenance Origin</h3>
    <div id="map"></div>
    <script>
      function initMap($longitude, $latitude) {
		  if (($longitude == null && $latitude == null)) {
			$longitude = -78.8719488;
			$latitude = 38.435092;
		  }
		  
        var uluru = {lat: $latitude, lng: $longitude};
		console.log(uluru);
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAG_KSt4joetRDUisInTq4sn-T8mZRVE04&callback=initMap">
    </script>
</body>
</html>
<?php
// table to get only libSiglums of only chants with images
	function createLibTemp($conn){
		$libTemp = $conn->query("create temporary table libTemp (`libSiglum` varchar(10))");
		if ($libTemp === false){
			echo "libTemp creation FAILED";
		}
	}
$db->closeConn($conn);
?>