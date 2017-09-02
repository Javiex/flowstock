<?php
class Incidente{

	public function getIncidenteAll() {
		$query = "SELECT * FROM la_incidente";
		$response= getFn($query, 'all');
		return $response;
	}

	public function setIncidente() {
		$query = "INSERT INTO la_incidente VALUES (NULL, $name, 1 )";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$id = $con->lastInsertId();
		return $id;
	}

	public function setIncidenteByElemento($id_elemento, $id_incidente, $detail) {
		$query= "INSERT INTO la_incidente_reporte VALUES (NULL, $id_elemento, $id_incidente, \"$detail\", 0, '', 0, '', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$id = $con->lastInsertId();
		return $id;
	}

	public function editIncidenteByElemento($id_incidente_reporte, $id_elemento, $id_incidente, $detail){
		$query="UPDATE la_incidente_reporte SET
		id_elemento=$id_elemento,
		id_incidente=$id_incidente,
		detail=$detail
		WHERE id_incidente_reporte = $id_incidente_reporte";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
	}

	public function actualizarSupervision($id_incidente_reporte, $supervisor_state, $supervisor_detail){
		// $query="UPDATE la_incidente_reporte SET
		// supervisor_state=$supervisor_state, supervisor_detail=$supervisor_detail,
		// id_proceso=$id_proceso,
		// state=$state
		// WHERE id_incidente_reporte = $id_incidente_reporte";
		$query="UPDATE la_incidente_reporte SET supervisor_state=$supervisor_state, supervisor_detail='$supervisor_detail'
		WHERE id_incidente_reporte = $id_incidente_reporte";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
	}

	public function actualizarAuditoria($id_incidente_reporte, $auditor_state, $auditor_detail){
		$query="UPDATE la_incidente_reporte SET
		auditor_state=$auditor_state,
		auditor_detail='$auditor_detail'
		WHERE id_incidente_reporte = $id_incidente_reporte";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
	}

	public function getIncidenteByElemento($id_elemento) {
		$query="SELECT r.*, i.name FROM la_incidente_reporte r INNER JOIN la_incidente i ON i.id_incidente = r.id_incidente WHERE id_elemento= $id_elemento";
		$response= getFn($query, 'all');
		return $response;
	}

	public function getAvailableIncidentesFromPeriodoSupervisar($id_periodo, $order, $state){
		/*
		$estado===0 -> por supervisar
		$estado===1 -> supervisados
		*/
		if(empty($state)){
			$query="SELECT li.*, lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia
			FROM la_incidente_reporte li
			INNER JOIN la_reportes lr ON lr.id_reporte = li.id_elemento
			INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona
			INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo
			INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento
			INNER JOIN la_marca m ON m.id_marca = lr.id_marca
			INNER JOIN la_lado l ON l.id_lado = lr.id_lado
			INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia
			WHERE lr.id_periodo=$id_periodo AND (li.supervisor_state = 0 OR li.state = 1 ) GROUP BY $order ORDER BY $order";
		}else{
			$query="SELECT li.*, lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia
			FROM la_incidente_reporte li
			INNER JOIN la_reportes lr ON lr.id_reporte = li.id_elemento
			INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona
			INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo
			INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento
			INNER JOIN la_marca m ON m.id_marca = lr.id_marca
			INNER JOIN la_lado l ON l.id_lado = lr.id_lado
			INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia
			WHERE lr.id_periodo=$id_periodo AND li.supervisor_state != 0 AND li.state != 1 GROUP BY $order ORDER BY $order";
		}
		$response= getFn($query, 'all');
		return $response;
	}

	public function actualizarAprobacionElementoxIncidente($id_elemento, $state){
			$query="UPDATE la_incidente_reporte SET state=$state
		WHERE id_elemento = $id_elemento";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
	}

	public function updateIncidenteByElemento($id_incidente_reporte, $id_incidente, $detail){
		$query="UPDATE la_incidente_reporte SET id_incidente=$id_incidente,
		detail=\"$detail\"
		WHERE id_incidente_reporte = $id_incidente_reporte";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
	}

	public function getIncidenciasCliente($id_periodo){
		$query="SELECT li.*, lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia
			FROM la_incidente_reporte li
			INNER JOIN la_reportes lr ON lr.id_reporte = li.id_elemento
			INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona
			INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo
			INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento
			INNER JOIN la_marca m ON m.id_marca = lr.id_marca
			INNER JOIN la_lado l ON l.id_lado = lr.id_lado
			INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia
			WHERE lr.id_periodo=$id_periodo AND li.state = 2  GROUP BY li.id_elemento ORDER BY li.id_elemento DESC";

			$response= getFn($query, 'all');
			return $response;
	}

}
?>
