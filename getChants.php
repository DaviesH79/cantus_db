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
			echo "msSiglum = $man_explode[1]; library = $man_explode[0]";
			$chants = getChants($msSiglum, $libSiglum, $conn);
		if ($chants != null){
			$chants->data_seek(0);
			$row = $chants->fetch_assoc();
			echo "num of rows of chants = " . $chants->num_rows;
			echo "<option disabled='disabled' selected='selected'>Select Chant</option>";
			while ($row = $chants->fetch_assoc()) {
				echo "<option value='" . $row['chantID'] . '|' . $row['libSiglum'] . '|' . $row['msSiglum'] . "'>" . $row['msIncipit'] . '(' . $row['chantID'] . ')' . "</option>";
			}
		}
		
	}

		function getChants($msSiglum, $libSiglum, $conn){
		//echo "msSiglum = " . $msSiglum;
		//echo "SELECT libSiglum, msSiglum, leafnumber, imagelink FROM leaf where libSiglum='$libSiglum' and msSiglum='$msSiglum' and imagelink is not null";
		// use msSiglm and libSiglum to see if leaf has image, if images is empty, spit out message there are no chant
		// with images in this manuscript
		
		// if not empty, then select the chants with libSiglum and msSiglum from chant and display chantID
		// get only the chants from the Manuscript that have images
			//echo "images is not empty" . $images->num_rows;
			//echo "<br></br>";
			$chants = $conn->query("SELECT chantID, libSiglum, msSiglum, msIncipit from chant where libSiglum='$libSiglum' and msSiglum='$msSiglum'");
			$chants->data_seek(0);
			$row = $chants->fetch_assoc();
			//echo $row['chantID'] . "row frm chant";
			//echo "<br></br>";
			//echo "chants is not empty" . $chants->num_rows;
			return $chants;
	}
	
			// use to add empty option to enable onchange for dropdown with only one value
			//echo "<option value="" disabled selected style=" . 'display:none;' . ">Label</option>";
	$db->closeConn($conn);
?>