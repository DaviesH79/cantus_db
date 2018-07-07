<?php 
//header("Content-type: text/html; charset=iso-8859-1");
header("Content-type: text/html; charset=utf8_general_ci");

// connect to db and query based on user input
$servername = "localhost";
$username = "root";
$password = "";
$db = "manuscript2018";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_POST){
	$lib = $_POST['lib'];
	$lib = urldecode($lib);
	echo "inside getLibrary.php and lib=$lib";
	echo "<br></br>";
	if ($lib != ''){
		$manu = getManuscript($lib, $conn);
		$manu->data_seek(0);
		echo "num of rows of mans = " . $manu->num_rows;
		echo "<select name='manuscript' id='manuscript'>";
			while ($row = $manu->fetch_assoc()) {
				echo "<option value='" . $row['libSiglum'] . '|' . $row['msSiglum'] . "'>" . $row['msSiglum'] . "</option>";
			}
	}
	echo "<p>inside getLibrary</p>";
	echo "</select>";
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
	
	function getLibSiglum($library, $conn){
		//$library = test_input($library);
		$libSiglum = $conn->query("SELECT libSiglum FROM library where library like'$library'");
		//echo "result query = " . $libSiglum;
		$libSiglum->data_seek(0);
		$row = $libSiglum->fetch_assoc();
		$libSiglum = $row['libSiglum'];
		return $libSiglum;
	}
?>