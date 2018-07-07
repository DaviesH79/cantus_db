<?php
header("Content-type: text/html; charset=utf8_general_ci");
include ("getDbConn.php");
	// get a db connection
	$db = new dbConn();
	$conn = $db->getDbConn();

if ($_POST) {
	$chant = $_POST['chant'];  // Storing Selected Value In Variable
		$chant_explode = explode('|', $chant);
		$leaves = getLeaves($chant_explode[0], $chant_explode[1], $chant_explode[2], $conn);
		if ($leaves == null){
			echo "Oh no! Something went wrong... Holly wrote some bad code!";
		}
		$leaves->data_seek(0);
		$row = $leaves->fetch_assoc();
		echo $row['leafNumber'] . " row from leaf in select";
		//echo "num of rows of leaves = " . $leaves->num_rows;
		echo "<option disabled='disabled' selected='selected'>Select Leaf</option>";
		while ($row = $leaves->fetch_assoc()) {
			echo "<option value='" . $row['leafNumber'] . '|' . $row['libSiglum'] . '|' . $row['msSiglum'] . "'>" . $row['leafNumber'] . "</option>";
		}
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
	
	function createLeafTemp($conn){
		$leafTemp = $conn->query("create temporary table tempLeaf (`libSiglum` varchar(10), `msSiglum` varchar(10), `leafNumber` varchar(12))");
		if ($leafTemp === false){
			echo "leaftemp creation FAILED";
		}
	}
	$db->closeConn($conn);
?>