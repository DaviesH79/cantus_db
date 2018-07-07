<?php
header("Content-type: text/html; charset=utf8_general_ci");
include ("getDbConn.php");
	// get a db connection
	$db = new dbConn();
	$conn = $db->getDbConn();

if ($_POST) {
	$leaf = $_POST['leaf'];  // Storing Selected Value In Variable
	$leaf_explode = explode('|', $leaf);
		
			$image = getLeafImage($leaf_explode[0], $leaf_explode[1], $leaf_explode[2], $conn);
			$image->data_seek(0);
			$row = $image->fetch_assoc();
			if (strpos($row['imageLink'], '.jpg')){
				echo "<img src=" . $row['imageLink'] . " alt=" . 'Click to View Image' . " style=" . 'width:500px;height:600px;' . " id=".'image'." /><br></br>
				<a href=" . $row['imageLink'] . " target=" . '_blank' . ">Click Here to View Image</a>";
			}
			else if ($row['imageLink'] != null){
				echo "<a href=" . $row['imageLink'] . " target=" . '_blank' . ">Click Here to View Image</a>";
			}
			else echo "OOPs... There is no image for this Leaf...";

}

function getLeafImage($leafNumber, $libSiglum, $msSiglum, $conn){ 
		$image = $conn->query("SELECT imageLink from leaf where libSiglum='$libSiglum' and msSiglum='$msSiglum' and leafNumber='$leafNumber'");
		//echo "image row numbers = " . $image->num_rows;
		if ($image === false){
			echo "image query failed";
		}
		return $image;
	}
$db->closeConn($conn);
?>