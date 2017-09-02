<?php
if(!defined('web')) die ('Acceso Denegado');
function getConnection(){
	$connection= null;
	try {
		$db_password= "";
		$db_username= "root";
		$db_name= "flow_store";
		$db_server= "localhost";
		$connection= new PDO("mysql:host=".$db_server.";dbname=".$db_name.";charset=utf8", $db_username, $db_password);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
	return $connection;
}
?>
