<?php
class dbConn {
	
	function openConn(){
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
		return $conn;
	}
	
	function closeConn($conn){
		mysqli_close($conn);
	}
	
	function getDbConn(){
		$conn = $this->openConn();
		return $conn;
	}
}
?>