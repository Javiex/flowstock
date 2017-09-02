<?php
class Partidos{

	public function listarPartidos(){

		$con= getConnection();

		$data= $con->prepare("SELECT * FROM pd_partido_politico");

		$data->execute();
		$partidos = $data->fetchAll();
		$con= null;

		return $partidos;

	}
	
}
?>