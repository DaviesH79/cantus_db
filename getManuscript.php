<?php 
//header("Content-type: text/html; charset=iso-8859-1");
header("Content-type: text/html; charset=utf8_general_ci");
include ("getDbConn.php");
	// get a db connection
	$db = new dbConn();
	$conn = $db->getDbConn();

//TODO: figure out how to get rid bad characters
if ($_POST){
	$libSig = $_POST['libSig'];
	echo "inside getManuscript.php and libSig=$libSig";
	echo "<br></br>";
	if ($libSig != ''){
		$manu = getManuscript($libSig, $conn);
		$manu->data_seek(0);
		echo "num of rows of mans = " . $manu->num_rows;
		echo "<select name='manuscript' id='manuscript'>";
		echo "<option disabled='disabled' selected='selected'>Select Manuscript</option>";
			while ($row = $manu->fetch_assoc()) {
				echo "<option value='" . $row['libSiglum'] . '|' . $row['msSiglum'] . "'>" . $row['msSiglum'] . "</option>";
			}
	}
	echo "</select>";
}
	function getManuscript($libSig, $conn){
		//echo "lib = " . $lib;
		//$libSiglum = getLibSiglum($lib, $conn);
		//echo "<br></br>";
		//echo "libSig = " . $libSiglum;
		$manuscripts = $conn->query("SELECT msSiglum, libSiglum FROM manuscript where libSiglum='$libSig'");
		return $manuscripts;
		//echo " after query ";
	}
	
	$db->closeConn($conn);
?>