<?php
class Zona{
	public function getZonaById($id_zona){
		$query= "SELECT r.id_zona, CASE WHEN sp.latitud='' THEN 0 ELSE sp.latitud END as latitud, CASE WHEN sp.longitud='' THEN 0 ELSE sp.longitud END as longitud, sd.nombre as departamento, sp.nombre as provincia, sp.id_sys_provincia FROM la_zona r INNER JOIN sys_departamento sd ON r.id_sys_departamento = sd.id_sys_departamento INNER JOIN sys_provincia sp ON r.id_sys_provincia = sp.id_sys_provincia WHERE id_zona = $id_zona";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getZonaByUsuario($id_usuario){
		$query= "SELECT r.id_zona, sd.nombre as departamento, sp.latitud, sp.longitud, sp.nombre as provincia, CONCAT(sd.nombre, '-', sp.nombre) as nombre_zona FROM la_zona r INNER JOIN sys_departamento sd ON r.id_sys_departamento = sd.id_sys_departamento INNER JOIN sys_provincia sp ON r.id_sys_provincia = sp.id_sys_provincia WHERE r.id_usuario = $id_usuario ORDER BY fecha DESC";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getDistritosByZona($id_zona){
		$query= "SELECT sd.id_sys_distrito, sd.nombre as distrito FROM la_zona lz INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia INNER JOIN sys_distrito sd ON sd.id_sys_provincia = sp.id_sys_provincia WHERE lz.id_zona = $id_zona ORDER BY distrito DESC";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getLastPeriodo(){
		$query= "SELECT * FROM la_periodo ORDER BY id_periodo DESC LIMIT 1,1";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getPeriodoActual(){
		$query= "SELECT * FROM la_periodo ORDER BY id_periodo DESC LIMIT 0,1";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getProvinciaById($id_sys_provincia){
		$query= "SELECT * FROM sys_provincia WHERE id_sys_provincia = $id_sys_provincia";
		$response= getFn($query, 'one');
		return $response;
	}
}
?>