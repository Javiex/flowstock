<?php
class Elemento{
	public function totalElementosZona($id_zona, $id_periodo){
		$query = "SELECT COUNT(*) as total FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona WHERE lz.id_zona = $id_zona AND lr.id_periodo = $id_periodo";
		$total = getFn($query, 'one');
		return $total;
	}
	public function estadoZonaPotencial($estado, $id_zona_potencial){
		$query= "UPDATE la_zonas_potenciales SET estado= $estado WHERE id_zona_potencial= $id_zona_potencial";
		$rows= postFn($query, 'update');
		return $rows;
	}
	public function actualizarZonaPotencial($id_zona_potencial, $direccion, $referencia, $latitud, $longitud){
		$query= "UPDATE la_zonas_potenciales SET direccion = \"$direccion\", referencia = \"$referencia\", latitud = \"$latitud\", longitud = \"$longitud\" WHERE id_zona_potencial= $id_zona_potencial";
		$rows= postFn($query, 'update');
		return $rows;
	}
	public function setOrigen($elemento_id, $id_origen){
		$query= "UPDATE la_reportes SET id_reporte_origen = $id_origen WHERE id_reporte= $elemento_id";
		$rows= postFn($query, 'update');
		return $rows;
	}
	public function editElemento($zona, $latitud, $longitud, $categoria, $formato, $tipo_elemento, $lado, $marca, $tipo_anuncio, $linea_producto, $direccion, $referencia, $iluminacion, $orientacion, $angulo, $carriles, $flujo_vehicular, $flujo_peatonal, $velocidad_trafico, $obstruccion, $calidad_iluminacion, $duracion_mirada, $dominacion, $saturacion, $id_distrito, $elemento_id){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_reportes SET fecha= fecha, id_categoria= $categoria, id_formato= $formato, id_tipo_elemento= $tipo_elemento, id_lado= $lado, id_marca= $marca, id_tipo_anuncio= $tipo_anuncio, id_linea_producto= $linea_producto, direccion= \"$direccion\", referencia= \"$referencia\", id_iluminacion= $iluminacion, id_orientacion= $orientacion, id_angulo= $angulo, id_carril= $carriles, id_flujo_vehicular= $flujo_vehicular, id_flujo_peatonal= $flujo_peatonal, id_velocidad_transito= $velocidad_trafico, id_obstruccion= $obstruccion, id_calidad_iluminacion= $calidad_iluminacion, id_duracion_mirada= $duracion_mirada, latitud= \"$latitud\", longitud= \"$longitud\", id_dominacion= $dominacion, id_saturacion= $saturacion WHERE id_reporte= $elemento_id");
		$dbh->execute();
		$id= $con->lastInsertId();
		return $id;
	}
	public function setZonaPotencial($zona, $distrito, $latitud, $longitud, $direccion, $referencia, $id_galeria){
		$con= getConnection();
		$dbh= $con->prepare("INSERT INTO la_zonas_potenciales VALUES (NULL, $zona, $distrito, \"$latitud\", \"$longitud\", $id_galeria, \"$direccion\", \"$referencia\")");
		$dbh->execute();
		$id= $con->lastInsertId();
		return $id;
	}
	public function setElemento($zona, $latitud, $longitud, $categoria, $formato, $tipo_elemento, $lado, $marca, $tipo_anuncio, $linea_producto, $direccion, $referencia, $iluminacion, $orientacion, $angulo, $carriles, $flujo_vehicular, $flujo_peatonal, $velocidad_trafico, $obstruccion, $calidad_iluminacion, $duracion_mirada, $dominacion, $saturacion, $id_galeria, $id_distrito, $id_periodo){
		$con= getConnection();
		$dbh= $con->prepare("INSERT INTO la_reportes VALUES (NULL, $zona, CURRENT_TIMESTAMP, \"$latitud\", \"$longitud\", $categoria, $formato, $tipo_elemento, $lado, $marca, $tipo_anuncio, $linea_producto, \"$direccion\", \"$referencia\", $iluminacion, $orientacion, $angulo, $carriles, $flujo_vehicular, $flujo_peatonal, $velocidad_trafico, $obstruccion, $calidad_iluminacion, $duracion_mirada, $dominacion, $saturacion, 1, '', 0, 0, $id_galeria, '', $id_distrito, 1, $id_periodo, NULL)");
		$dbh->execute();
		$id= $con->lastInsertId();
		return $id;
	}
	public function actualizarSupervision($elemento_id, $observaciones= "", $estado){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_reportes SET fecha= fecha, aprobado_supervisor = $estado, observacion_supervisor= '".$observaciones."', aprobado_auditor=0, observacion_auditor='' WHERE id_reporte= $elemento_id");
		$dbh->execute();
	}
	public function actualizarAuditoria($elemento_id, $observaciones= "", $estado){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_reportes SET fecha= fecha, aprobado_auditor = $estado, observacion_auditor= '".$observaciones."' WHERE id_reporte= $elemento_id");
		$dbh->execute();
	}
	public function actualizarFotoSupervision($foto_id, $action){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_fotos SET fecha= fecha, estado = $action WHERE id_foto= $foto_id");
		$dbh->execute();
	}
	public function setGaleriaToElemento($id_galeria, $id_elemento){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_reportes SET id_galeria=$id_galeria WHERE id_reporte = $id_elemento");
		$dbh->execute();
	}
	public function setGaleriaToZonaPotencial($id_galeria, $id_zona_potencial){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_zonas_potenciales SET id_galeria=$id_galeria WHERE id_zona_potencial = $id_zona_potencial");
		$dbh->execute();
	}
	public function getElementosByZona($id_zona){
		$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado WHERE lr.id_zona = $id_zona ORDER BY direccion";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getElementosPorPeriodo($id_periodo, $limit, $page){
		$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, te.icono as tipo_elemento_icono, sp.nombre as provincia, m.nombre as marca, m.bg as marca_bg, m.logo as marca_logo, l.nombre as lado, lr.latitud, lr.longitud, m.marker FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado WHERE lr.aprobado_auditor= 1 AND lr.id_periodo = $id_periodo ORDER BY lr.id_reporte DESC LIMIT $limit OFFSET $page";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getElementosOrigen($id_periodo, $id_categoria, $id_formato, $id_lado, $id_sys_provincia, $id_sys_distrito, $id_zona, $id_tipo_elemento, $limit, $page){
		$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, te.icono as tipo_elemento_icono, sp.nombre as provincia, m.nombre as marca, m.bg as marca_bg, m.logo as marca_logo, l.nombre as lado, lr.latitud, lr.longitud, m.marker FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado WHERE lr.aprobado_supervisor= 1 AND lr.id_periodo = $id_periodo AND lz.id_zona = $id_zona AND lr.id_formato = $id_formato AND lr.id_lado = $id_lado AND lr.id_tipo_elemento = $id_tipo_elemento ORDER BY lr.direccion ASC LIMIT $limit OFFSET $page";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getTodosElementosPorPeriodo($id_periodo, $limit, $page){
		$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, te.icono as tipo_elemento_icono, sp.nombre as provincia, m.nombre as marca, m.bg as marca_bg, m.logo as marca_logo, l.nombre as lado, lr.latitud, lr.longitud, m.marker FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado WHERE lr.aprobado_supervisor= 1 AND lr.id_periodo = $id_periodo AND lr.id_reporte_origen IS NULL ORDER BY lr.id_reporte DESC LIMIT $limit OFFSET $page";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getElementosPorPeriodoFiltro($id_periodo, $limit, $page, $extra){
		$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, te.icono as tipo_elemento_icono, te.precio as alquiler_mensual, te.ancho, te.alto, lalipro.nombre as linea_producto, latian.nombre as tipo_anuncio, sp.nombre as provincia, sdis.nombre as distrito, m.nombre as marca, m.bg as marca_bg, lafor.nombre as formato, m.logo as marca_logo, l.nombre as lado, lr.latitud, lr.longitud, m.marker FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN sys_distrito sdis ON sdis.id_sys_distrito = lr.id_sys_distrito INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_tipo_anuncio latian ON latian.id_tipo_anuncio = lr.id_tipo_anuncio INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_formato lafor ON lafor.id_formato = lr.id_formato INNER JOIN la_lado l ON l.id_lado = lr.id_lado LEFT JOIN la_linea_producto lalipro ON lalipro.id_linea_producto = lr.id_linea_producto WHERE lr.aprobado_auditor= 1 AND lr.id_periodo = $id_periodo $extra ORDER BY lr.id_reporte DESC LIMIT $limit OFFSET $page";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getTotalElementosPorPeriodo($id_periodo, $extra= ""){
		$query= "SELECT COUNT(*) as total FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado WHERE lr.aprobado_auditor= 1 AND lr.id_periodo = $id_periodo $extra";
		$response= getFn($query, 'one');
		return $response->total;
	}
	public function getZonaPotencial(){
		$query= "SELECT lzp.id_zona_potencial, lzp.zona_id, lzp.direccion, lzp.referencia, lzp.latitud, lzp.longitud, sp.nombre as provincia, lzp.id_galeria, sd.nombre as distrito, lzp.estado, CASE WHEN lzp.estado = '1' THEN 'btn-default' WHEN lzp.estado = '2' THEN 'btn-success' ELSE 'btn-danger' END as clase_estado FROM la_zonas_potenciales lzp INNER JOIN la_zona lz ON lz.id_zona = lzp.zona_id INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia INNER JOIN sys_distrito sd ON sd.id_sys_distrito = lzp.id_sys_distrito ORDER BY lzp.direccion";
		$response= getFn($query, 'all');
		return $response;
	}
	public function filtrarZonaPotencial($where){
		$query= "SELECT lzp.id_zona_potencial, lzp.zona_id, lzp.direccion, lzp.referencia, lzp.latitud, lzp.longitud, sp.nombre as provincia, lzp.id_galeria, sd.nombre as distrito, lzp.estado, CASE WHEN lzp.estado = '1' THEN 'btn-default' WHEN lzp.estado = '2' THEN 'btn-success' ELSE 'btn-danger' END as clase_estado FROM la_zonas_potenciales lzp INNER JOIN la_zona lz ON lz.id_zona = lzp.zona_id INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia INNER JOIN sys_distrito sd ON sd.id_sys_distrito = lzp.id_sys_distrito WHERE $where ORDER BY lzp.direccion";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getZonaPotencialByZona($id_zona){
		$query= "SELECT lzp.id_zona_potencial, lzp.zona_id, lzp.direccion, lzp.referencia, lzp.latitud, lzp.longitud, sp.nombre as provincia, lzp.id_galeria, sd.nombre as distrito FROM la_zonas_potenciales lzp INNER JOIN la_zona lz ON lz.id_zona = lzp.zona_id INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia INNER JOIN sys_distrito sd ON sd.id_sys_distrito = lzp.id_sys_distrito WHERE lzp.zona_id = $id_zona ORDER BY lzp.direccion";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getAvailableElementosFromPeriodo($id_periodo, $id_zona, $order){
		// $query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado WHERE lr.id_zona = $id_zona AND lr.id_periodo=$id_periodo AND lr.estado = 1 ORDER BY $order";
		$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, lp.nombre as periodo, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, ir.state as estadoInc
		FROM la_reportes lr
		INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona
		INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento
		INNER JOIN la_marca m ON m.id_marca = lr.id_marca
		INNER JOIN la_lado l ON l.id_lado = lr.id_lado
		LEFT JOIN la_incidente_reporte ir ON ir.id_elemento = lr.id_reporte
		WHERE lr.id_zona = $id_zona AND lr.id_periodo=$id_periodo AND lr.estado = 1
		GROUP BY lr.id_reporte
		ORDER BY $order";
		$response= getFn($query, 'all');
		return $response;
	}
	public function getAvailableElementosFromPeriodoAuditar($id_periodo, $order, $estado){
		/*
			$estado===0 -> por supervisar
			$estado===1 -> supervisados
		 */
		if(empty($estado)){
			$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lr.id_periodo=$id_periodo AND lr.aprobado_supervisor = 1 AND aprobado_auditor = 0 ORDER BY $order";
		}else{
			$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lr.id_periodo=$id_periodo AND lr.aprobado_supervisor = 1 AND aprobado_auditor != 0 ORDER BY $order";
		}
		$response= getFn($query, 'all');
		return $response;
	}
	public function getAvailableElementosFromPeriodoSupervisar($id_periodo, $order, $estado){
		/*
			$estado===0 -> por supervisar
			$estado===1 -> supervisados
		 */
		if(empty($estado)){
			$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lr.id_periodo=$id_periodo AND lr.aprobado_supervisor = 0 ORDER BY $order";
		}else{
			$query= "SELECT lr.id_reporte, lr.id_zona, lr.direccion, lr.aprobado_auditor, lr.aprobado_supervisor, te.nombre as tipo_elemento, m.nombre as marca, m.logo as marca_logo, l.nombre as lado, sp.nombre AS provincia FROM la_reportes lr INNER JOIN la_zona lz ON lz.id_zona = lr.id_zona INNER JOIN la_periodo lp ON lr.id_periodo = lp.id_periodo INNER JOIN la_tipo_elemento te ON te.id_tipo_elemento = lr.id_tipo_elemento INNER JOIN la_marca m ON m.id_marca = lr.id_marca INNER JOIN la_lado l ON l.id_lado = lr.id_lado INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lr.id_periodo=$id_periodo AND lr.aprobado_supervisor != 0 ORDER BY $order";
		}
		$response= getFn($query, 'all');
		return $response;
	}
	public function excluir($id_elemento){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_reportes SET estado = 2 WHERE id_reporte= $id_elemento");
		$dbh->execute();
	}
	public function reusar($id_elemento){
		$con= getConnection();
		$dbh= $con->prepare("UPDATE la_reportes SET estado = 3 WHERE id_reporte= $id_elemento");
		$dbh->execute();
	}
	public function getZonaPotencialById($id_zona_potencial){
		$query= "SELECT lzp.id_zona_potencial, lzp.zona_id, lzp.direccion, lzp.referencia, lzp.latitud, lzp.longitud, sp.nombre as provincia, lzp.id_galeria, sd.nombre as distrito FROM la_zonas_potenciales lzp INNER JOIN la_zona lz ON lz.id_zona = lzp.zona_id INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia INNER JOIN sys_distrito sd ON sd.id_sys_distrito = lzp.id_sys_distrito WHERE lzp.id_zona_potencial = $id_zona_potencial";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getPuntos($id_elemento){
		$query= "SELECT lvt.nota*lvt.peso + carril.nota*carril.peso + flujo_vehicular.nota*flujo_vehicular.peso + flujo_peatonal.nota*flujo_peatonal.peso + obstruccion.nota*obstruccion.peso + calidad_iluminacion.nota*calidad_iluminacion.peso + duracion_mirada.nota*duracion_mirada.peso + dominacion.nota*dominacion.peso + saturacion.nota*saturacion.peso AS punto FROM la_reportes lr INNER JOIN la_velocidad_transito lvt ON lvt.id_velocidad_transito = lr.id_velocidad_transito INNER JOIN la_iluminacion iluminacion ON iluminacion.id_iluminacion = lr.id_iluminacion INNER JOIN la_carril carril ON carril.id_carril = lr.id_carril INNER JOIN la_flujo_vehicular flujo_vehicular ON flujo_vehicular.id_flujo_vehicular = lr.id_flujo_vehicular INNER JOIN la_flujo_peatonal flujo_peatonal ON flujo_peatonal.id_flujo_peatonal = lr.id_flujo_peatonal INNER JOIN la_obstruccion obstruccion ON obstruccion.id_obstruccion = lr.id_obstruccion INNER JOIN la_calidad_iluminacion calidad_iluminacion ON calidad_iluminacion.id_calidad_iluminacion = lr.id_calidad_iluminacion AND calidad_iluminacion.tipo_calidad = iluminacion.tipo_calidad INNER JOIN la_duracion_mirada duracion_mirada ON duracion_mirada.id_duracion_mirada = lr.id_duracion_mirada INNER JOIN la_dominacion dominacion ON dominacion.id_dominacion = lr.id_dominacion INNER JOIN la_saturacion saturacion ON saturacion.id_saturacion = lr.id_saturacion WHERE lr.id_reporte = $id_elemento";
		$response= getFn($query, 'one');
		$punto= $response->punto;
		$query= "SELECT * FROM la_puntaje WHERE min<=$punto AND $punto<=max";
		$response= getFn($query, 'one');
		$response->punto= $punto;
		return $response;
	}
	public function getCalificacion($id_elemento){
		$query= "SELECT lvt.nota*lvt.peso as velocidad_transito ,carril.nota*carril.peso as carril, flujo_vehicular.nota*flujo_vehicular.peso as flujo_vehicular, flujo_peatonal.nota*flujo_peatonal.peso as flujo_peatonal, obstruccion.nota*obstruccion.peso as obstruccion, calidad_iluminacion.nota*calidad_iluminacion.peso as calidad_iluminacion, duracion_mirada.nota*duracion_mirada.peso as duracion_mirada, dominacion.nota*dominacion.peso as dominacion, saturacion.nota*saturacion.peso as saturacion FROM la_reportes lr INNER JOIN la_velocidad_transito lvt ON lvt.id_velocidad_transito = lr.id_velocidad_transito INNER JOIN la_iluminacion iluminacion ON iluminacion.id_iluminacion = lr.id_iluminacion INNER JOIN la_carril carril ON carril.id_carril = lr.id_carril INNER JOIN la_flujo_vehicular flujo_vehicular ON flujo_vehicular.id_flujo_vehicular = lr.id_flujo_vehicular INNER JOIN la_flujo_peatonal flujo_peatonal ON flujo_peatonal.id_flujo_peatonal = lr.id_flujo_peatonal INNER JOIN la_obstruccion obstruccion ON obstruccion.id_obstruccion = lr.id_obstruccion INNER JOIN la_calidad_iluminacion calidad_iluminacion ON calidad_iluminacion.id_calidad_iluminacion = lr.id_calidad_iluminacion AND calidad_iluminacion.tipo_calidad = iluminacion.tipo_calidad INNER JOIN la_duracion_mirada duracion_mirada ON duracion_mirada.id_duracion_mirada = lr.id_duracion_mirada INNER JOIN la_dominacion dominacion ON dominacion.id_dominacion = lr.id_dominacion INNER JOIN la_saturacion saturacion ON saturacion.id_saturacion = lr.id_saturacion WHERE lr.id_reporte = $id_elemento";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getElementosById($id_elemento){
		$query= "SELECT lr.id_reporte, lr.latitud, lr.longitud, lr.direccion, lr.referencia, categoria.nombre AS categoria, formato.nombre AS formato, tipo_elemento.nombre AS tipo_elemento, lado.nombre AS lado, marca.nombre AS marca, marca.logo AS marca_logo, tipo_anuncio.nombre AS tipo_anuncio, linea_producto.nombre AS linea_producto, iluminacion.nombre AS iluminacion, orientacion.nombre AS orientacion, angulo.nombre AS angulo, carril.nombre AS carril, flujo_vehicular.nombre AS flujo_vehicular, flujo_peatonal.nombre AS flujo_peatonal, velocidad_transito.nombre AS velocidad_transito, obstruccion.nombre AS obstruccion, calidad_iluminacion.nombre AS calidad_iluminacion, duracion_mirada.nombre AS duracion_mirada, dominacion.nombre AS dominacion, saturacion.nombre AS saturacion, sp.nombre AS provincia, lr.observacion_supervisor, lr.observacion_auditor, lr.id_periodo, lr.id_zona FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia INNER JOIN la_categoria categoria ON categoria.id_categoria = lr.id_categoria INNER JOIN la_formato formato ON formato.id_formato = lr.id_formato INNER JOIN la_tipo_elemento tipo_elemento ON tipo_elemento.id_tipo_elemento = lr.id_tipo_elemento AND tipo_elemento.id_formato = lr.id_formato INNER JOIN la_lado lado ON lado.id_lado = lr.id_lado INNER JOIN la_marca marca ON marca.id_marca = lr.id_marca INNER JOIN la_tipo_anuncio tipo_anuncio ON tipo_anuncio.id_tipo_anuncio = lr.id_tipo_anuncio LEFT JOIN la_linea_producto linea_producto ON linea_producto.id_linea_producto = lr.id_linea_producto INNER JOIN la_iluminacion iluminacion ON iluminacion.id_iluminacion = lr.id_iluminacion INNER JOIN la_orientacion orientacion ON orientacion.id_orientacion = lr.id_orientacion INNER JOIN la_angulo angulo ON angulo.id_angulo = lr.id_angulo INNER JOIN la_carril carril ON carril.id_carril = lr.id_carril INNER JOIN la_flujo_vehicular flujo_vehicular ON flujo_vehicular.id_flujo_vehicular = lr.id_flujo_vehicular INNER JOIN la_flujo_peatonal flujo_peatonal ON flujo_peatonal.id_flujo_peatonal = lr.id_flujo_peatonal INNER JOIN la_velocidad_transito velocidad_transito ON velocidad_transito.id_velocidad_transito = lr.id_velocidad_transito INNER JOIN la_obstruccion obstruccion ON obstruccion.id_obstruccion = lr.id_obstruccion INNER JOIN la_calidad_iluminacion calidad_iluminacion ON calidad_iluminacion.id_calidad_iluminacion = lr.id_calidad_iluminacion AND calidad_iluminacion.tipo_calidad = iluminacion.tipo_calidad INNER JOIN la_duracion_mirada duracion_mirada ON duracion_mirada.id_duracion_mirada = lr.id_duracion_mirada INNER JOIN la_dominacion dominacion ON dominacion.id_dominacion = lr.id_dominacion INNER JOIN la_saturacion saturacion ON saturacion.id_saturacion = lr.id_saturacion WHERE lr.id_reporte = $id_elemento";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getElementoBrutoById($id_elemento){
		$query= "SELECT lr.*, sd.id_sys_provincia FROM la_reportes lr INNER JOIN sys_distrito sd ON lr.id_sys_distrito = sd.id_sys_distrito WHERE lr.id_reporte = $id_elemento";
		$response= getFn($query, 'one');
		return $response;
	}
	public function getTipoElemento($extra = ""){
		$query= "SELECT * FROM la_tipo_elemento ".$extra;
		$response = getFn($query, 'all');
		return $response;
	}
	public function getParametros(){
		$response= [];
		$query= "SELECT * FROM la_categoria";
		$response["categorias"]= getFn($query, 'all');
		$query= "SELECT * FROM la_tipo_elemento";
		$response["tipo_elementos"]= getFn($query, 'all');
		$query= "SELECT * FROM la_lado";
		$response["lados"]= getFn($query, 'all');
		$query= "SELECT * FROM la_marca";
		$response["marcas"]= getFn($query, 'all');
		$query= "SELECT * FROM la_tipo_anuncio";
		$response["tipo_anuncios"]= getFn($query, 'all');
		$query= "SELECT * FROM la_linea_producto";
		$response["linea_productos"]= getFn($query, 'all');
		$query= "SELECT * FROM la_orientacion";
		$response["orientaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_angulo";
		$response["angulos"]= getFn($query, 'all');
		$query= "SELECT * FROM la_carril";
		$response["carriles"]= getFn($query, 'all');
		$query= "SELECT * FROM la_flujo_vehicular";
		$response["flujos_vehicular"]= getFn($query, 'all');
		$query= "SELECT * FROM la_flujo_peatonal";
		$response["flujos_peatonal"]= getFn($query, 'all');
		$query= "SELECT * FROM la_velocidad_transito";
		$response["velocidades_transito"]= getFn($query, 'all');
		$query= "SELECT * FROM la_obstruccion";
		$response["obstrucciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_iluminacion";
		$response["iluminaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_calidad_iluminacion WHERE tipo_calidad = 1";
		$response["iluminacion_iluminadas"]= getFn($query, 'all');
		$query= "SELECT * FROM la_calidad_iluminacion WHERE tipo_calidad = 2";
		$response["iluminacion_no_iluminadas"]= getFn($query, 'all');
		$query= "SELECT * FROM la_duracion_mirada";
		$response["duraciones_mirada"]= getFn($query, 'all');
		$query= "SELECT * FROM la_dominacion";
		$response["dominaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_saturacion";
		$response["saturaciones"]= getFn($query, 'all');
		return $response;
	}
	public function getParametrosRegistro($id_sys_provincia){
		$response= [];
		$query= "SELECT * FROM la_categoria";
		$response["categorias"]= getFn($query, 'all');
		$query= "SELECT * FROM la_tipo_elemento";
		$response["tipo_elementos"]= getFn($query, 'all');
		$query= "SELECT * FROM la_lado";
		$response["lados"]= getFn($query, 'all');
		$query= "SELECT * FROM la_marca";
		$response["marcas"]= getFn($query, 'all');
		$query= "SELECT * FROM la_tipo_anuncio";
		$response["tipo_anuncios"]= getFn($query, 'all');
		$query= "SELECT * FROM la_linea_producto";
		$response["linea_productos"]= getFn($query, 'all');
		$query= "SELECT * FROM la_orientacion";
		$response["orientaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_angulo";
		$response["angulos"]= getFn($query, 'all');
		$query= "SELECT * FROM la_carril";
		$response["carriles"]= getFn($query, 'all');
		$query= "SELECT * FROM la_flujo_vehicular";
		$response["flujos_vehicular"]= getFn($query, 'all');
		$query= "SELECT * FROM la_flujo_peatonal";
		$response["flujos_peatonal"]= getFn($query, 'all');
		$query= "SELECT * FROM la_velocidad_transito";
		$response["velocidades_transito"]= getFn($query, 'all');
		$query= "SELECT * FROM la_obstruccion";
		$response["obstrucciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_iluminacion";
		$response["iluminaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_calidad_iluminacion WHERE tipo_calidad = 1";
		$response["iluminacion_iluminadas"]= getFn($query, 'all');
		$query= "SELECT * FROM la_calidad_iluminacion WHERE tipo_calidad = 2";
		$response["iluminacion_no_iluminadas"]= getFn($query, 'all');
		$query= "SELECT * FROM la_duracion_mirada";
		$response["duraciones_mirada"]= getFn($query, 'all');
		$query= "SELECT * FROM la_dominacion";
		$response["dominaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM la_saturacion";
		$response["saturaciones"]= getFn($query, 'all');
		$query= "SELECT * FROM sys_distrito WHERE id_sys_provincia = $id_sys_provincia";
		$response["distritos"]= getFn($query, 'all');
		return $response;
	}
}
?>
