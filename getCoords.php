<?php
header("Content-type: text/html; charset=utf8_general_ci");
include ("getDbConn.php");
	// get a db connection
	$db = new dbConn();
	$conn = $db->getDbConn();

if ($_POST){
		$manuscript = $_POST['manuscript'];  // Storing Selected Value In Variable
		$manuscript = urldecode($manuscript);
		$man_explode = explode('|', $manuscript);
		$libSiglum = $man_explode[0];
		$msSiglum = $man_explode[1];
		
		$coords = getProvCoords($libSiglum, $msSiglum, $conn);
		$coords->data_seek(0);
		$row = $coords->fetch_assoc();
			$long = number_format($row['longitude'],6);
			$lat = number_format($row['latitude'], 6);
			$gpsCoords = array(
				"longitude" => $long,
				"latitude" => $lat
			);
			$provCoords[] = $gpsCoords;
 			echo json_encode($provCoords); // return json object to jquery function
	}
	function getProvCoords($libSiglum, $msSiglum, $conn){
			$coords = $conn->query("SELECT longitude, latitude FROM provenance where provenanceID=(SELECT distinct provenanceID FROM section where libSiglum='$libSiglum' and msSiglum='$msSiglum')");
			if ($coords === null){
				echo "query yielded no results";
			}
			return $coords;
		}	

 ?>