<?php
class Galeria{
	public function getGaleriaByElemento($id_elemento){
		$query= "SELECT lf.*, CASE WHEN lf.estado = 1 THEN '' WHEN lf.estado = 2 THEN 'fa fa-check' WHEN lf.estado = 3 THEN 'fa fa-close' END as estado_nombre FROM la_reportes lr INNER JOIN la_fotos lf ON lf.id_galeria = lr.id_galeria WHERE lr.id_reporte=$id_elemento";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getGaleriaClienteByElemento($id_elemento){
		$query= "SELECT lf.*, CASE WHEN lf.estado = 1 THEN '' WHEN lf.estado = 2 THEN 'fa fa-check' WHEN lf.estado = 3 THEN 'fa fa-close' END as estado_nombre FROM la_reportes lr INNER JOIN la_fotos lf ON lf.id_galeria = lr.id_galeria WHERE lf.estado=2 AND lr.id_reporte=$id_elemento";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getGaleriaByZonaPotencial($id_zona_potencial){
		$query= "SELECT lf.*, CASE WHEN lf.estado = 1 THEN '' WHEN lf.estado = 2 THEN 'fa fa-check' WHEN lf.estado = 3 THEN 'fa fa-close' END as estado_nombre FROM la_zonas_potenciales lr INNER JOIN la_fotos lf ON lf.id_galeria = lr.id_galeria WHERE lr.id_zona_potencial=$id_zona_potencial";
		$response= getFn($query, 'all');
		return $response;
	}
	public function setGaleria(){
		$con= getConnection();
		$dbh= $con->prepare("INSERT INTO la_galeria VALUES (NULL, CURRENT_TIMESTAMP)");
		$dbh->execute();
		$id_galeria= $con->lastInsertId();
		return $id_galeria;
	}
	public function setFoto($galeria_id, $fuente){
		$con= getConnection();
		$dbh= $con->prepare("INSERT INTO la_fotos VALUES (NULL, $galeria_id, \"$fuente\", 1, CURRENT_TIMESTAMP, 1)");
		$dbh->execute();
	}
}
?>