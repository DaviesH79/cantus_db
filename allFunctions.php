<?php
// file with all functions
<?php
function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	function getLibSiglum($library, $conn){
		//$library = test_input($library);
		$libSiglum = $conn->query("SELECT libSiglum FROM library where library='$library'");
		//echo "result query = " . $libSiglum;
		$libSiglum->data_seek(0);
		$row = $libSiglum->fetch_assoc();
		$libSiglum = $row['libSiglum'];
		return $libSiglum;
	}
	function getManuscript($lib, $conn){
		//echo "lib = " . $lib;
		$libSiglum = getLibSiglum($lib, $conn);
		//echo "<br></br>";
		//echo "libSig = " . $libSiglum;
		$manuscripts = $conn->query("SELECT msSiglum, libSiglum FROM manuscript where libSiglum='$libSiglum'");
		return $manuscripts;
		//echo " after query ";
	}
	
	function getChants($msSiglum, $libSiglum, $conn){
		//echo "msSiglum = " . $msSiglum;
		//echo "SELECT libSiglum, msSiglum, leafnumber, imagelink FROM leaf where libSiglum='$libSiglum' and msSiglum='$msSiglum' and imagelink is not null";
		// use msSiglm and libSiglum to see if leaf has image, if images is empty, spit out message there are no chant
		// with images in this manuscript
		$images = $conn->query("SELECT libSiglum, msSiglum, leafnumber, imagelink FROM leaf where libSiglum='$libSiglum' and msSiglum='$msSiglum' and imagelink is not null");
		if ($images->num_rows === 0){
			echo "<br></br>";?>
			<h3 style="color:red;">Oh No! This Manuscript contains no Chant Images</h3>
			<!--echo "Oh No! This Manuscript contains no Chant Images";-->
			<?php return null;
		}
		// if not empty, then select the chants with libSiglum and msSiglum from chant and display chantID
		// get only the chants from the Manuscript that have images
			//echo "images is not empty" . $images->num_rows;
			//echo "<br></br>";
			$chants = $conn->query("SELECT chantID, libSiglum, msSiglum from chant where libSiglum='$libSiglum' and msSiglum='$msSiglum'");
			$chants->data_seek(0);
			$row = $chants->fetch_assoc();
			//echo $row['chantID'] . "row frm chant";
			//echo "<br></br>";
			//echo "chants is not empty" . $chants->num_rows;
			return $chants;
	}
	
	function getLeaves($chantID, $libSiglum, $msSiglum,	$conn){
		//echo "<br></br>";
		//echo "create temporary table leaf1 as SELECT libSiglum, msSiglum, leafNumber from chant where libSiglum='$libSiglum' 
		//and msSiglum='$msSiglum' and chantID='$chantID";
		//echo "<br></br>";
		$drop = $conn->query("drop temporary table tempLeaf if exists");
		if ($drop === true){
			echo "temp table dropped";
		}
		
		createLeafTemp($conn);
		/*$leafTemp = $conn->query("create temporary table tempLeaf (`libSiglum` varchar(10), `msSiglum` varchar(10), `leafNumber` varchar(12))");
		if ($leafTemp === true){
			echo "leaftemp was created";
		}*/
		//$fillTemp = $conn->query("INSERT INTO tempLeaf (libSiglum, msSiglum, leafNumber) VALUES ('1', '2', '3')"); 
		$fillTemp = $conn->query("INSERT INTO tempLeaf (libSiglum, msSiglum, leafNumber) SELECT libSiglum, msSiglum, leafNumber FROM chant WHERE libSiglum='$libSiglum' 
		and msSiglum='$msSiglum' and chantID='$chantID'"); 
		//echo "<br></br>";
		if ($fillTemp === false){
			echo "insert FAILED";
		}
		//echo "<br></br>";
		$leaves = $conn->query("SELECT a.leafNumber, a.libSiglum, a.msSiglum from leaf a join tempLeaf b using(libSiglum, msSiglum)");
		$leaves->data_seek(0);
		//echo "SELECT leafNumber, imageLink from leaf join leaf1 using(libSiglum, msSiglum)";
		if ($leaves->num_rows === 0){
			echo "LEAVES is EMPTY";
			return null;
		}
		$row = $leaves->fetch_assoc();
		//echo "<br></br>";
		//echo $row['leafNumber'] . " row from leaves";
		return $leaves;
	}
	
	function getLeafImage($leafNumber, $libSiglum, $msSiglum, $conn){ 
		$image = $conn->query("SELECT imageLink from leaf where libSiglum='$libSiglum' and msSiglum='$msSiglum' and leafNumber='$leafNumber'");
		//echo "image row numbers = " . $image->num_rows;
		if ($image === false){
			echo "image query failed";
		}
		return $image;
	}
	
	function createLeafTemp($conn){
		$leafTemp = $conn->query("create temporary table tempLeaf (`libSiglum` varchar(10), `msSiglum` varchar(10), `leafNumber` varchar(12))");
		if ($leafTemp === false){
			echo "leaftemp creation FAILED";
		}
	}
	
	// table to get only libSiglums of only chants with images
	function createLibTemp($conn){
		$libTemp = $conn->query("create temporary table libTemp (`libSiglum` varchar(10))");
		if ($libTemp === false){
			echo "libTemp creation FAILED";
		}
	}
	
	function getProvCoords($libSiglum, $msSiglum, $conn){
		$coords = $conn-Query("SELECT longitude, latitude FROM provenance where provenaceID=(SELECT distinct provenaceID FROM section where libSiglum='$libSiglum' and msSiglum='$msSiglum')");
		$coords->data_seek(0);
		$row = $coords->fetch_assoc();
		$long = $row['longitude'];
		$lat = $row['latitude'];
		$gpsCoords = array($long, $lat);
		return $gpsCoords;
	}
	// this need to go somewhere else
		$coords = getProvCoords($libSiglum, $msSiglum, $conn);
		$coords->data_seek(0);
		$row = $coords->fetch_assoc();
		$longitude = $row['longitude'];
		$latitude = $row['latitude'];
		
		
	
?>