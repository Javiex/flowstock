<?php
session_start();

if(!defined('web')) die ('Acceso Denegado');

$app->get("/products", function() use($app){

	$oProductos= new Product();
	$response= $oProductos->getProducts();
	echoResponse($response);
});

$app->get("/products", function() use($app){

	$oProductos= new Product();
	$search = '1';
	$response= $oProductos->getProductByElemento($search);
	response($response);
});








/* USUARIOS */
$app->post("/login", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);

		$response= null;

		if(isset($todo->usuario))
			$usuario= strtolower($todo->usuario);
		if(isset($todo->clave))
			$clave= md5($todo->clave);

		if(!empty($usuario) && !empty($clave)){
			$query= "SELECT id_usuario, usuario, role FROM la_usuarios WHERE usuario LIKE '".$usuario."' AND clave LIKE '".$clave."'";
			$response= getFn($query, 'one');
		}
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->get("/usuarios", function() use($app){
	$query= "SELECT id_usuario, usuario, CASE WHEN correo IS NULL THEN 'Sin especificar' ELSE correo END as correo, CASE WHEN telefono = '' THEN 'Sin especificar' ELSE telefono END as telefono, nombre, apellido FROM la_usuarios ORDER BY id_usuario ASC";
	$response= getFn($query, 'all');
	response($response);
});
$app->post("/usuarios", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$id_usuario= $todo->id_usuario->id;

	$query= "SELECT id_usuario, usuario, CASE WHEN correo IS NULL THEN 'Sin especificar' ELSE correo END as correo, CASE WHEN telefono = '' THEN 'Sin especificar' ELSE telefono END as telefono, nombre, apellido FROM la_usuarios WHERE id_usuario= $id_usuario";
	$response= getFn($query, 'one');
	$usuario= $response["response"];

	$query= "SELECT lz.id_zona, lz.fecha, sd.nombre as departamento, sp.nombre as provincia FROM la_zona lz INNER JOIN sys_departamento sd ON lz.id_sys_departamento = sd.id_sys_departamento INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lz.id_usuario = $id_usuario";
	$response= getFN($query, 'all');
	$zonas= $response["response"];

	$response= array('usuario'=>$usuario, 'zonas'=>$zonas);
	response($response);
});
$app->get("/usuario/:id", function($id) use($app){
	$query= "SELECT id_usuario, usuario, CASE WHEN correo IS NULL THEN 'Sin especificar' ELSE correo END as correo FROM la_usuarios WHERE id_usuario'".$id."' ORDER BY id_usuario ASC";
	$response= getFn($query, 'one');
	response($response);
});
$app->post("/usuario/nuevo", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$usuario= strtolower($todo->usuario);
		$clave= md5($todo->clave);
		$correo= strtolower($todo->correo);
		$nombre= $todo->nombre;
		$apellido= $todo->apellido;
		$telefono= $todo->telefono;
		$rol= $todo->rol;

		if(empty($usuario) || empty($clave)){
			$response= array('code'=>300, 'response'=>'ERROR - Verifique los datos ingresados y vuelva a intentar.');
			response($response);
			die();
		}
		$query= "SELECT COUNT(*) as total FROM la_usuarios WHERE id_usuario LIKE '".$usuario."'";
		$response= getFn($query, 'one');
		$total= $response->response->total;

		if(!empty($total)){
			$response= array('code'=>300, 'response'=>'ERROR - El usuario ya está siendo usados.');
			response($response);
			die();
		}
		$con= getConnection();
		$dbh= $con->prepare("INSERT INTO la_usuarios VALUES (NULL, '".$usuario."', '".$clave."', '".$correo."', '".$nombre."', '".$apellido."', '".$telefono."', ".$rol.")");
		$dbh->execute();
		$response= array('code'=>200);
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/usuario/eliminar", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id= "";
		if(!isset($todo->id)){
			$response= array('code'=>"300", 'response'=>'Ocurrió un error al eliminar el usuario.');
			response($response);
			die();
		}else{
			$id= $todo->id;
		}
		$query= "DELETE FROM usuario_admin WHERE id=".$id;
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$response= array('code'=>"200");
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/usuario/actualizar", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id= $todo->edit_id;
		$clave= md5($todo->edit_clave);
		$query= "UPDATE la_usuarios SET clave='".$clave."' WHERE id_usuario=".$id;
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$response= array('code'=>"200");
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});

/* ZONAS POTENCIALES */
$app->put("/zonas-potenciales/aprobar", function() use($app){

	$params= $app->request->getBody();
	$params= json_decode($params);
	$estado= $params->estado;
	$id_zona_potencial= $params->id_zona_potencial;

	$oElemento= new Elemento();
	$rows= $oElemento->estadoZonaPotencial($estado, $id_zona_potencial);

	response($rows);
});
$app->put("/zonas-potenciales/:id_zona_potencial", function($id_zona_potencial) use($app){

	$params= $app->request->getBody();
	$params= json_decode($params);
	$direccion= $params->direccion;
	$referencia= $params->referencia;
	$latitud= $params->latitud;
	$longitud= $params->longitud;

	$oElemento= new Elemento();
	$rows= $oElemento->actualizarZonaPotencial($id_zona_potencial, $direccion, $referencia, $latitud, $longitud);

	response($rows);
});
$app->get("/zonas-potenciales", function() use($app){

	$zonasPotenciales= [];
	$oZona= new Zona();
	$oElemento= new Elemento();
	$zonasPotenciales= $oElemento->getZonaPotencial();

	response($zonasPotenciales);
});
$app->get("/zonas-potenciales/usuario/:id_usuario", function($id_usuario) use($app){

	$zonasPotenciales= [];
	$oZona= new Zona();
	$oElemento= new Elemento();
	$zonas= $oZona->getZonaByUsuario($id_usuario);
	foreach ($zonas as $zona) {
		$zonasPotencialesPorZona= $oElemento->getZonaPotencialByZona($zona["id_zona"]);
		$zonasPotenciales= array_merge($zonasPotenciales, $zonasPotencialesPorZona);
	}

	response($zonasPotenciales);
});
$app->get("/zonas-potenciales/:id_zona_potencial", function($id_zona_potencial) use($app){

	$oElemento= new Elemento();
	$response= $oElemento->getZonaPotencialById($id_zona_potencial);
	response($response);
});
$app->get("/zonas-potenciales/:id_usuario/parametros", function($id_usuario) use($app){

	$parametros= [];
	$parametros["zonas"]= [];
	$oZona= new Zona();
	$zonas= $oZona->getZonaByUsuario($id_usuario);
	foreach ($zonas as $zona) {
		$zonaParametro= [];
		$zonaParametro["id_zona"]= $zona["id_zona"];
		$zonaParametro["nombre_zona"]= $zona["nombre_zona"];
		$zonaParametro["latitud"]= $zona["latitud"];
		$zonaParametro["longitud"]= $zona["longitud"];
		$zonaDistritos= $oZona->getDistritosByZona($zona["id_zona"]);
		$zonaParametro["distritos"]= $zonaDistritos;
		array_push($parametros["zonas"], $zonaParametro);
	}

	response($parametros);
});

/* GALERIA */
$app->get("/galeria/todos", function() use($app){
	$query= "SELECT * FROM la_galeria ORDER BY fecha DESC";
	$response= getFn($query, 'all');
	response($response);
});
$app->get("/galeria/pagina/:page", function($page) use($app){
	$page= ($page-1)*20;
	$query= "SELECT * FROM la_galeria ORDER BY fecha DESC LIMIT 20 OFFSET ".$page;
	$response= getFn($query, 'all');
	response($response);
});
$app->get("/galeria/:id", function($id) use($app){
	$query= "SELECT id_usuario, usuario, CASE WHEN correo IS NULL THEN 'Sin especificar' ELSE correo END as correo FROM la_usuarios WHERE id_usuario'".$id."' ORDER BY id_usuario ASC";
	$response= getFn($query, 'one');
	response($response);
});
$app->post("/galeria/agregar", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$usuario= strtolower($todo->usuario);
		$clave= md5($todo->clave);
		$correo= strtolower($todo->correo);

		if(empty($usuario) || empty($clave)){
			$response= array('code'=>300, 'response'=>'ERROR - Verifique los datos ingresados y vuelva a intentar.');
			response($response);
			die();
		}
		$query= "SELECT COUNT(*) as total FROM usuario_admin WHERE usuario LIKE '".$usuario."' OR correo LIKE '".$correo."'";
		$response= getFn($query, 'one');
		$total= $response['response']->total;

		if(!empty($total)){
			$response= array('code'=>300, 'response'=>'ERROR - El usuario o contraseña ya están siendo usados.');
			response($response);
			die();
		}
		$con= getConnection();
		$dbh= $con->prepare("INSERT INTO usuario_admin (id, usuario, correo, clave, cod_soc) VALUES (NULL, '".$usuario."', '".$correo."', '".$clave."', NULL)");
		$dbh->execute();
		$response= array('code'=>200);
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/galeria/eliminar", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id= "";
		if(!isset($todo->id)){
			$response= array('code'=>"300", 'response'=>'Ocurrió un error al eliminar el usuario.');
			response($response);
			die();
		}else{
			$id= $todo->id;
		}
		$query= "DELETE FROM usuario_admin WHERE id=".$id;
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$response= array('code'=>"200");
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/galeria/editar", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id= $todo->edit_id;
		$clave= md5($todo->clave);
		$correo= strtolower($todo->correo);
		$query= "UPDATE usuario_admin SET correo='".$correo."', clave='".$clave."' WHERE id=".$id;
		if(empty($clave) AND empty($correo)){
			$response= array('code'=>"200", 'response'=>'No hay datos para actualizar.');
			response($response);
			die();
		}elseif(empty($clave) AND !empty($correo)){
			$query= "UPDATE usuario_admin SET correo='".$correo."' WHERE id=".$id;
		}elseif(empty($correo) AND !empty($clave)){
			$query= "UPDATE usuario_admin SET clave='".$clave."' WHERE id=".$id;
		}
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$response= array('code'=>"200");
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});

/* REPORTES */
$app->put("/elemento/origen", function() use($app){
	$params= $app->request->getBody();
	$params= json_decode($params);
	$id_elemento= $params->id_elemento;
	$id_origen= $params->id_origen;
	$oElemento= new Elemento();
	$response= $oElemento->setOrigen($id_elemento, $id_origen);
	response($response);
});
$app->get("/elemento/puntaje/:id_elemento", function($id_elemento) use($app){
	$oElemento= new Elemento();
	$puntaje= $oElemento->getPuntos($id_elemento);
	response($puntaje);
});
$app->get("/elemento/parametros", function() use($app){
	$oElemento= new Elemento();
	$id_sys_provincia= $_GET["id_sys_provincia"];
	$response= $oElemento->getParametrosRegistro($id_sys_provincia);
	response($response);
});
$app->put("/elemento/:id_elemento/excluir", function($id_elemento) use($app){
	$oElemento= new Elemento();
	$response= $oElemento->excluir($id_elemento);
	response($id_elemento);
});
$app->put("/elemento/:id_elemento/reusar", function($id_elemento) use($app){
	$oElemento= new Elemento();
	$response= $oElemento->reusar($id_elemento);
	response($id_elemento);
});
$app->get("/elemento/:id_elemento/bruto", function($id_elemento) use($app){
	$oElemento= new Elemento();
	$response= $oElemento->getElementoBrutoById($id_elemento);
	response($response);
});
$app->get("/zonas/:id_usuario", function($id_usuario) use($app){
	$oZona= new Zona();
	$response= $oZona->getZonaByUsuario($id_usuario);
	response($response);
});
$app->get("/zona/:id_zona/elementos", function($id_zona) use($app){
	$oElemento= new Elemento();
	$oZona= new Zona();

	$periodo= $oZona->getPeriodoActual();

	$response= $oElemento->getAvailableElementosFromPeriodo($periodo->id_periodo, $id_zona, "id_reporte DESC");

	response($response);
});
$app->get("/zona/:id_zona/elementos/last_periodo", function($id_zona) use($app){
	$oElemento= new Elemento();
	$oZona= new Zona();

	$periodo= $oZona->getLastPeriodo();

	$response= $oElemento->getAvailableElementosFromPeriodo($periodo->id_periodo, $id_zona, "direccion ASC");

	response($response);
});
$app->get("/elementos/supervisar", function() use($app){
	$supervisa= 0;
	if(isset($_GET["supervisa"])){
		$supervisa= $_GET["supervisa"];
	}
	$oElemento= new Elemento();
	$oZona= new Zona();

	$periodo= $oZona->getPeriodoActual();

	$response= $oElemento->getAvailableElementosFromPeriodoSupervisar($periodo->id_periodo, "lr.id_reporte DESC", $supervisa);

	response($response);
});
$app->get("/elementos/auditar", function() use($app){
	$audita= 0;
	if(isset($_GET["audita"])){
		$audita= $_GET["audita"];
	}
	$oElemento= new Elemento();
	$oZona= new Zona();

	$periodo= $oZona->getPeriodoActual();

	$response= $oElemento->getAvailableElementosFromPeriodoAuditar($periodo->id_periodo, "lr.id_reporte DESC", $audita);

	response($response);
});
$app->get("/periodo", function() use($app){
	$oZona= new Zona();

	$response= $oZona->getPeriodoActual();

	response($response);
});
$app->get("/zona/:id_zona", function($id_zona) use($app){
	$oZona= new Zona();

	$response= $oZona->getZonaById($id_zona);

	response($response);
});
$app->put("/elemento", function() use($app){
	try {
		$oElemento= new Elemento();
		$oGaleria= new Galeria();

		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id_reporte= $todo->id_reporte;
		$angulo= $todo->angulo;
		$calidad_iluminacion= $todo->calidad_iluminacion;
		$carril= $todo->carril;
		$categoria= $todo->categoria;
		$direccion= $todo->direccion;
		$dominacion= $todo->dominacion;
		$duracion_mirada= $todo->duracion_mirada;
		$flujo_peaton= $todo->flujo_peaton;
		$flujo_vehicular= $todo->flujo_vehicular;
		$iluminacion= $todo->iluminacion;
		$lado= $todo->lado;
		$latitud= $todo->latitud;
		if(isset($todo->linea_producto)){
			$linea_producto= $todo->linea_producto->id_linea_producto;
		}else{
			$linea_producto= 0;
		}
		$longitud= $todo->longitud;
		$marca= $todo->marca;
		$obstruccion= $todo->obstruccion;
		$orientacion= $todo->orientacion;
		$referencia= $todo->referencia;
		$saturacion= $todo->saturacion;
		$tipo_anuncio= $todo->tipo_anuncio;
		$tipo_elemento= $todo->tipo_elemento;
		$velocidad_trafico= $todo->velocidad_trafico;
		$zona= $todo->zona;
		$formato= $tipo_elemento->id_formato;
		$distrito= $todo->distrito;

		$elemento_id= $oElemento->editElemento($zona, $latitud, $longitud, $categoria->id_categoria, $formato, $tipo_elemento->id_tipo_elemento, $lado->id_lado, $marca->id_marca, $tipo_anuncio->id_tipo_anuncio, $linea_producto, $direccion, $referencia, $iluminacion->id_iluminacion, $orientacion->id_orientacion, $angulo->id_angulo, $carril->id_carril, $flujo_vehicular->id_flujo_vehicular, $flujo_peaton->id_flujo_peatonal, $velocidad_trafico->id_velocidad_transito, $obstruccion->id_obstruccion, $calidad_iluminacion->id_calidad_iluminacion, $duracion_mirada->id_duracion_mirada, $dominacion->id_dominacion, $saturacion->id_saturacion, $distrito->id_sys_distrito, $id_reporte);
		response($id_reporte);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elemento", function() use($app){
	try {

		$oElemento= new Elemento();
		$oGaleria= new Galeria();

		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id_galeria= 0;
		$angulo= $todo->angulo;
		$calidad_iluminacion= $todo->calidad_iluminacion;
		$carril= $todo->carril;
		$categoria= $todo->categoria;
		$direccion= $todo->direccion;
		$dominacion= $todo->dominacion;
		$duracion_mirada= $todo->duracion_mirada;
		$flujo_peaton= $todo->flujo_peaton;
		$flujo_vehicular= $todo->flujo_vehicular;
		$iluminacion= $todo->iluminacion;
		$lado= $todo->lado;
		$latitud= $todo->latitud;
		if(isset($todo->linea_producto)){
			$linea_producto= $todo->linea_producto->id_linea_producto;
		}else{
			$linea_producto= 0;
		}
		$longitud= $todo->longitud;
		$marca= $todo->marca;
		$obstruccion= $todo->obstruccion;
		$orientacion= $todo->orientacion;
		$referencia= $todo->referencia;
		$saturacion= $todo->saturacion;
		$tipo_anuncio= $todo->tipo_anuncio;
		$tipo_elemento= $todo->tipo_elemento;
		$velocidad_trafico= $todo->velocidad_trafico;
		$zona= $todo->zona;
		$formato= $tipo_elemento->id_formato;
		$periodo= $todo->periodo;
		$distrito= $todo->distrito;

		$elemento_id= $oElemento->setElemento($zona, $latitud, $longitud, $categoria->id_categoria, $formato, $tipo_elemento->id_tipo_elemento, $lado->id_lado, $marca->id_marca, $tipo_anuncio->id_tipo_anuncio, $linea_producto, $direccion, $referencia, $iluminacion->id_iluminacion, $orientacion->id_orientacion, $angulo->id_angulo, $carril->id_carril, $flujo_vehicular->id_flujo_vehicular, $flujo_peaton->id_flujo_peatonal, $velocidad_trafico->id_velocidad_transito, $obstruccion->id_obstruccion, $calidad_iluminacion->id_calidad_iluminacion, $duracion_mirada->id_duracion_mirada, $dominacion->id_dominacion, $saturacion->id_saturacion, $id_galeria, $distrito->id_sys_distrito, $periodo);

		response($elemento_id);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/zonas-potenciales", function() use($app){
	try {

		$oElemento= new Elemento();

		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id_galeria= 0;
		$zona= $todo->zona;
		$direccion= $todo->direccion;
		$referencia= $todo->referencia;
		$latitud= $todo->latitud;
		$longitud= $todo->longitud;
		$distrito= $todo->distrito;

		$elemento_id= $oElemento->setZonaPotencial($zona->id_zona, $distrito->id_sys_distrito, $latitud, $longitud, $direccion, $referencia, $id_galeria);

		response($elemento_id);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/reportes/formData", function() use($app){
	try {
		$formData= [];

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '001' AND id_son NOT LIKE '000' AND estado != 0";
		$categorias= getFn($query, 'all');
		$formData["categorias"]= $categorias;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '002' AND id_son NOT LIKE '000' AND estado != 0";
		$lados= getFn($query, 'all');
		$formData["lados"]= $lados;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '003' AND id_son NOT LIKE '000' AND estado != 0";
		$tipo_anuncio= getFn($query, 'all');
		$formData["tipo_anuncio"]= $tipo_anuncio;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '004' AND id_son NOT LIKE '000' AND estado != 0";
		$tipo_producto= getFn($query, 'all');
		$formData["tipo_producto"]= $tipo_producto;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '005' AND id_son NOT LIKE '000' AND estado != 0";
		$horario_toma= getFn($query, 'all');
		$formData["horario_toma"]= $horario_toma;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '006' AND id_son NOT LIKE '000' AND estado != 0";
		$iluminacion= getFn($query, 'all');
		$formData["iluminacion"]= $iluminacion;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '007' AND id_son NOT LIKE '000' AND estado != 0";
		$orientacion= getFn($query, 'all');
		$formData["orientacion"]= $orientacion;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '008' AND id_son NOT LIKE '000' AND estado != 0";
		$angulo= getFn($query, 'all');
		$formData["angulo"]= $angulo;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '009' AND id_son NOT LIKE '000' AND estado != 0";
		$carriles= getFn($query, 'all');
		$formData["carriles"]= $carriles;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '010' AND id_son NOT LIKE '000' AND estado != 0";
		$obs_vehicular= getFn($query, 'all');
		$formData["obs_vehicular"]= $obs_vehicular;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '011' AND id_son NOT LIKE '000' AND estado != 0";
		$obs_peaton= getFn($query, 'all');
		$formData["obs_peaton"]= $obs_peaton;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '012' AND id_son NOT LIKE '000' AND estado != 0";
		$velocidad_trafico= getFn($query, 'all');
		$formData["velocidad_trafico"]= $velocidad_trafico;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '013' AND id_son NOT LIKE '000' AND estado != 0";
		$obstruccion= getFn($query, 'all');
		$formData["obstruccion"]= $obstruccion;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '014' AND id_son NOT LIKE '000' AND estado != 0";
		$calidad_iluminacion= getFn($query, 'all');
		$formData["calidad_iluminacion"]= $calidad_iluminacion;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '015' AND id_son NOT LIKE '000' AND estado != 0";
		$dominacion_vs_elementos= getFn($query, 'all');
		$formData["dominacion_vs_elementos"]= $dominacion_vs_elementos;

		$query= "SELECT * from la_master_table WHERE id_parent LIKE '016' AND id_son NOT LIKE '000' AND estado != 0";
		$saturacion_desorden= getFn($query, 'all');
		$formData["saturacion_desorden"]= $saturacion_desorden;

		response($formData);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});

$app->get("/elementos/periodo/:id_periodo", function($id_periodo) use($app){

	$page= 0;
	$limit= 10000;

	$oElemento= new Elemento();
	$elementos= $oElemento->getTodosElementosPorPeriodo($id_periodo, $limit, $page);
	response($elementos);

});

$app->get("/elementos/origen", function() use($app){

	$page= 0;
	$limit= 10000;

	$id_periodo= $_GET["id_periodo"];
	$id_categoria= $_GET["id_categoria"];
	$id_formato= $_GET["id_formato"];
	$id_lado= $_GET["id_lado"];
	$id_sys_distrito= $_GET["id_sys_distrito"];
	$id_sys_provincia= $_GET["id_sys_provincia"];
	$id_tipo_elemento= $_GET["id_tipo_elemento"];
	$id_zona= $_GET["id_zona"];

	$oElemento= new Elemento();
	$elementos= $oElemento->getElementosOrigen($id_periodo, $id_categoria, $id_formato, $id_lado, $id_sys_provincia, $id_sys_distrito, $id_zona, $id_tipo_elemento, $limit, $page);
	response($elementos);

});

$app->post("/elemento/:id_elemento/galeria", function($id_elemento) use($app){
	try {

		$oGaleria= new Galeria();
		$oElemento= new Elemento();

		$elemento_id= $id_elemento;

		$galeria_id= $oGaleria->setGaleria();
		$oElemento->setGaleriaToElemento($galeria_id, $elemento_id);

		$target_dir = $_SERVER['DOCUMENT_ROOT']."/galeria/";
		for ($i=0; $i < 6; $i++) {
			if(!isset($_FILES["file-".$i])){
				continue;
			}else{
				$file= $_FILES["file-".$i];
				$fuente_origen= $file["name"];
				$fuente= basename($fuente_origen);
				$extension= pathinfo($fuente_origen, PATHINFO_EXTENSION);
				$fuente= md5($fuente.date("h:i:s")).".".$extension;
				$target_file = $target_dir . basename($fuente);

				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					$oGaleria->setFoto($galeria_id, $fuente);
				}
			}
		}

		response($galeria_id);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/zonas-potenciales/:id_zona_potencial/galeria", function($id_zona_potencial) use($app){
	try {

		$oGaleria= new Galeria();
		$oElemento= new Elemento();

		$id_zona_potencial= $id_zona_potencial;

		$galeria_id= $oGaleria->setGaleria();
		$oElemento->setGaleriaToZonaPotencial($galeria_id, $id_zona_potencial);

		$target_dir = $_SERVER['DOCUMENT_ROOT']."/galeria/";
		for ($i=0; $i <= 3; $i++) {
			if(!isset($_FILES["file-".$i])){
				continue;
			}else{
				$file= $_FILES["file-".$i];
				$fuente_origen= $file["name"];
				$fuente= basename($fuente_origen);
				$extension= pathinfo($fuente_origen, PATHINFO_EXTENSION);
				$fuente= md5($fuente.date("h:i:s")).".".$extension;
				$target_file = $target_dir . basename($fuente);

				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					$oGaleria->setFoto($galeria_id, $fuente);
				}
			}
		}

		response($galeria_id);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/galeria/:galeria_id", function($galeria_id) use($app){
	try {

		$oGaleria= new Galeria();

		$target_dir = $_SERVER['DOCUMENT_ROOT']."/galeria/";
		for ($i=0; $i <= 6; $i++) {
			if(!isset($_FILES["file-".$i])){
				continue;
			}else{
				$file= $_FILES["file-".$i];
				$fuente_origen= $file["name"];
				$fuente= basename($fuente_origen);
				$extension= pathinfo($fuente_origen, PATHINFO_EXTENSION);
				$fuente= md5($fuente.date("h:i:s")).".".$extension;
				$target_file = $target_dir . basename($fuente);

				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					$oGaleria->setFoto($galeria_id, $fuente);
				}
			}
		}

		response($galeria_id);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/galeria/todos", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$galeria_id= $todo->galeria_id;

		$query= "SELECT id_foto, elemento_id, nombre, estado, fecha, CASE WHEN horario=1 THEN 'Día' ELSE 'Noche' END as horario FROM la_fotos WHERE elemento_id = ".$galeria_id;
		$response= getFn($query, 'all');
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elementos/todos", function() use($app){
	try {
		$query= "SELECT lr.*, lz.id_usuario, lz.id_sys_departamento, lz.id_sys_provincia, sd.nombre as departamento, sp.nombre as provincia, lu.usuario FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_departamento sd ON sd.id_sys_departamento = lz.id_sys_departamento INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_usuarios lu ON lu.id_usuario = lz.id_usuario ORDER BY lr.id_reporte DESC";
		$response= getFn($query, 'all');
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elementos/todos/nodist", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$user_id= $todo->user_id;
		$query= "SELECT lr.*, lz.id_sys_departamento, lz.id_sys_provincia, sp.nombre as provincia FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_departamento sd ON sd.id_sys_departamento = lz.id_sys_departamento INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia WHERE lr.id_sys_distrito IS NULL AND lz.id_usuario=$user_id ORDER BY lr.id_reporte DESC";
		$response= getFn($query, 'all');
		$elementos= $response["response"];
		$new_response= array("response"=>[]);
		if(count($elementos)){
			foreach ($elementos as $item) {
				$item["distritos"]= [];
				$query= "SELECT * FROM sys_distrito WHERE id_sys_provincia = '".$item["id_sys_provincia"]."'";
				$response= getFn($query, 'all');
				$distritos= $response["response"];
				if(count($distritos)){
					$item["distritos"]= $distritos;
				}
				array_push($new_response["response"], $item);
			}
		}
		response($new_response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elementos/actualizar/dist", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$id_reporte= $todo->id_reporte;
		$id_distrito= $todo->id_distrito;
		$query= "UPDATE la_reportes SET id_sys_distrito=$id_distrito WHERE id_reporte= $id_reporte";
		$con= getConnection();
		$dbh= $con->prepare($query);
		$dbh->execute();
		$id_zona= $con->lastInsertId();
		$response= array('code'=>200);
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elementos/auditor/todos", function() use($app){
	try {
		$query= "SELECT lr.*, lz.id_usuario, lz.id_sys_departamento, lz.id_sys_provincia, sd.nombre as departamento, sp.nombre as provincia, lu.usuario FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_departamento sd ON sd.id_sys_departamento = lz.id_sys_departamento INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_usuarios lu ON lu.id_usuario = lz.id_usuario WHERE lr.aprobado_supervisor=1 ORDER BY lr.id_reporte DESC";
		$response= getFn($query, 'all');
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elementos/todosZona", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$zona_id= $todo->zona;
		$query= "SELECT lr.*, lz.id_usuario, lz.id_sys_departamento, lz.id_sys_provincia, sd.nombre as departamento, sp.nombre as provincia, lu.usuario FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_departamento sd ON sd.id_sys_departamento = lz.id_sys_departamento INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia INNER JOIN la_usuarios lu ON lu.id_usuario = lz.id_usuario WHERE lz.id_sys_provincia=$zona_id ORDER BY lr.id_reporte DESC";
		$response= getFn($query, 'all');
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->get("/galeria/elemento/:id_elemento", function($id_elemento) use($app){
	$oGaleria= new Galeria();

	$response= $oGaleria->getGaleriaByElemento($id_elemento);

	response($response);
});
$app->get("/galeria/zona_potencial/:id_zona_potencial", function($id_zona_potencial) use($app){
	$oGaleria= new Galeria();

	$response= $oGaleria->getGaleriaByZonaPotencial($id_zona_potencial);

	response($response);
});
$app->post("/elementos/galeria/:galeria_id", function($galeria_id) use($app){
	try {
		$con= getConnection();
		$id_galeria= $galeria_id;

		$target_dir = $_SERVER['DOCUMENT_ROOT']."/galeria/";
		for ($i=0; $i < 6; $i++) {
			if(!isset($_FILES["dia-".$i])){
				continue;
			}else{
				$file= $_FILES["dia-".$i];
				$fuente_origen= $file["name"];
				$fuente= basename($fuente_origen);
				$extension= pathinfo($fuente_origen, PATHINFO_EXTENSION);
				$fuente= md5($fuente.date("h:i:s")).".".$extension;
				$target_file = $target_dir . basename($fuente);

				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					$dbh= $con->prepare("INSERT INTO la_fotos VALUES (NULL, $id_galeria, \"$fuente\", 1, CURRENT_TIMESTAMP, 1)");
					$dbh->execute();
				}
			}
		}

		$response= array('code'=>200, 'response'=>$id_galeria);
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->post("/elementos/auditor/galeria", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$id_elemento= $todo->id_elemento;
	$query= "SELECT lf.*, CASE WHEN lf.estado = 1 THEN '' WHEN lf.estado = 2 THEN 'fa fa-check' WHEN lf.estado = 3 THEN 'fa fa-close' END as estado_nombre FROM la_reportes lr INNER JOIN la_fotos lf ON lf.elemento_id = lr.galeria_id WHERE lf.estado=2 AND lr.id_reporte=$id_elemento";
	$response= getFn($query, 'all');
	response($response);
});
$app->get("/elemento/:id_elemento", function($id_elemento) use($app){
	$oElemento= new Elemento();

	$response= $oElemento->getElementosById($id_elemento);

	response($response);
});
$app->post("/elementos/auditor/uno", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$id_elemento= $todo->id_elemento;

	$query= "SELECT lr.*, sp.nombre as provincia FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lr.aprobado_supervisor=1 AND lr.id_reporte=".$id_elemento;
	$response= getFn($query, 'one');
	response($response);
});
$app->post("/usuario/agregarZona", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$usuario= $todo->edit_id;
	$departamento= $todo->zona_departamento;
	$provincia= $todo->zona_provincia;

	$con= getConnection();
	$dbh= $con->prepare("INSERT INTO la_zona VALUES(NULL, '".$usuario."', ".$departamento.", ".$provincia.", CURRENT_TIMESTAMP)");
	$dbh->execute();
	$id_zona= $con->lastInsertId();
	$response= array('code'=>200);
	response($response);
});
$app->put("/elemento/rechazar", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$elemento_id= $todo->id_elemento;
	$observaciones= $todo->observaciones;

	$oElemento= new Elemento();
	if(isset($todo->auditor)){
		$oElemento->actualizarAuditoria($elemento_id, $observaciones, 2);
	}else{
		$oElemento->actualizarSupervision($elemento_id, $observaciones, 2);
	}
	$response= array('code'=>200);
	response($response);
});
$app->put("/elemento/aprobar", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$elemento_id= $todo->id_elemento;
	$observaciones= "";

	$oElemento= new Elemento();
	if(isset($todo->auditor)){
		$oElemento->actualizarAuditoria($elemento_id, $observaciones, 1);
	}else{
		$oElemento->actualizarSupervision($elemento_id, $observaciones, 1);
	}
	$response= array('code'=>200);
	response($response);
});
$app->post("/elementos/auditor/action", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$elemento_id= $todo->elemento_id;
	$action= $todo->action;
	$observaciones= $todo->observaciones;

	$con= getConnection();
	$dbh= $con->prepare("UPDATE la_reportes SET fecha= fecha, aprobado_auditor = $action, observaciones_auditor= '".$observaciones."' WHERE id_reporte= $elemento_id");
	$dbh->execute();
	$response= array('code'=>200);
	response($response);
});
$app->put("/elemento/foto/aprobar", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$foto_id= $todo->foto_id;
	$action= 2;

	$oElemento= new Elemento();
	$oElemento->actualizarFotoSupervision($foto_id, $action);

	$response= array('code'=>200);
	response($response);
});
$app->put("/elemento/foto/rechazar", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$foto_id= $todo->foto_id;
	$action= 3;

	$oElemento= new Elemento();
	$oElemento->actualizarFotoSupervision($foto_id, $action);

	$response= array('code'=>200);
	response($response);
});
$app->get("/departamentos", function() use($app){
	$query= "SELECT * FROM sys_departamento";
	$response= getFn($query, 'all');
	response($response);
});
$app->get("/provincias", function() use($app){
	$query= "SELECT * FROM sys_provincia ORDER BY nombre";
	$response= getFn($query, 'all');
	response($response);
});
$app->get("/usuario/:id_usuario/zonas", function($id_usuario) use($app){
	$query= "SELECT lz.id_zona, sd.nombre as departamento, sp.nombre as provincia FROM la_zona lz INNER JOIN sys_departamento sd ON lz.id_sys_departamento = sd.id_sys_departamento INNER JOIN sys_provincia sp ON lz.id_sys_provincia = sp.id_sys_provincia WHERE lz.id_usuario = $id_usuario";
	$response= getFN($query, 'all');
	response($response);
});
$app->get("/departamentos/:id_departamento", function($id_departamento) use($app){
	if(!isset($id_departamento)){
		response(array("response"=>[]));
		die();
	}
	$query= "SELECT * FROM sys_provincia WHERE id_sys_departamento = $id_departamento";
	$response= getFn($query, 'all');
	response($response);
});
$app->get("/fotos/todo", function() use($app){
	$query= "SELECT lg.id, CONCAT('http://detectaauditoria.pe/galeria/', lf.nombre) as foto FROM la_galeria lg INNER JOIN la_fotos lf ON lf.elemento_id = lg.id WHERE lg.id<=320 AND lg.id>=300 ORDER BY lg.id DESC";
	echo $query;
	$response= getFn($query, 'all');
	$fotos= $response["response"];
	foreach ($fotos as $item) {
		echo "<p>".$item["id"]."</p>";
		echo "<a target='_blank' href='".$item["foto"]."'><img width='400px' src='".$item["foto"]."'/></a>";
	}
	//var_dump($response["response"]);
	die();
	response($response);
});
$app->get("/fotos/:id_galeria", function($id_galeria) use($app){
	$query= "SELECT CONCAT('http://detectaauditoria.pe/galeria/', lf.nombre) as foto FROM la_fotos lf INNER JOIN la_galeria lg ON lf.elemento_id = lg.id WHERE lg.id = $id_galeria";
	$response= getFn($query, 'all');
	$fotos= $response["response"];
	foreach ($fotos as $item) {
		echo "<img width='400px' src='".$item["foto"]."'/>";
	}
	die();
	response($response);
});

/* CLIENTE */
$app->get("/entel/zonas-potenciales/:id_zona_potencial", function($id_zona_potencial) use($app){

	$oElemento= new Elemento();
	$oGaleria= new Galeria();

	$response= $oElemento->getZonaPotencialById($id_zona_potencial);
	$response->galeria = $oGaleria->getGaleriaByZonaPotencial($id_zona_potencial);

	response($response);
});
$app->get("/entel/zonas-potenciales", function() use($app){

	$zonasPotenciales= [];
	$oElemento= new Elemento();
	$zonasPotenciales= $oElemento->getZonaPotencial();

	response($zonasPotenciales);
});
$app->post("/entel/zonas-potenciales", function() use($app){

	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$page= 0;
	$limit= 20;
	if(isset($todo->page)){
		if($todo->page<0){
			$page= 0;
			$limit= 10000;
		}else{
			$page= $todo->page*20;
		}
	}

	$extra_where= "";
	if(isset($todo->provincia)){
		$provincia= $todo->provincia;
		$extra_where.="lz.id_sys_provincia = ".$provincia->id_sys_provincia;
	}
	$oElemento= new Elemento();
	$oZona= new Zona();
	$zona= $oZona->getProvinciaById($provincia->id_sys_provincia);

	$zonasPotenciales= $oElemento->filtrarZonaPotencial($extra_where);

	$response["sustentos"]= $zonasPotenciales;
	$response["zona"]= $zona;
	response($response);

});
$app->post("/entel/login", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);

		$response= null;

		if(isset($todo->usuario))
			$usuario= strtolower($todo->usuario);
		if(isset($todo->clave))
			$clave= md5($todo->clave);

		if(!empty($usuario) && !empty($clave)){
			$query= "SELECT id_usuario, usuario, role FROM la_usuarios WHERE usuario LIKE '".$usuario."' AND clave LIKE '".$clave."'";
			$response= getFn($query, 'one');
		}
		response($response);
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});
$app->get("/entel/parametros", function() use($app){

	$oParametros= new Parametros();
	$parametros= $oParametros->getParametros();
	response($parametros);

});
$app->get("/entel/elementos/periodo/:id_periodo", function($id_periodo) use($app){

	$page= 0;
	$limit= 20;
	if(isset($_GET["page"])){
		if($_GET["page"]<0){
			$page= 0;
			$limit= 10000;
		}else{
			$page= $_GET["page"]*20;
		}
	}
	$oElemento= new Elemento();
	$oGaleria= new Galeria();
	$elementos= $oElemento->getElementosPorPeriodo($id_periodo, $limit, $page);
	$response["elementos"]= [];
	foreach ($elementos as $elemento) {
		$galeria= $oGaleria->getGaleriaByElemento($elemento["id_reporte"]);
		$elemento["galeria"]= $galeria;
		array_push($response["elementos"], $elemento);
	}
	$response["page"]= $page;
	$response["total"]= $oElemento->getTotalElementosPorPeriodo($id_periodo);
	response($response);

});
$app->get("/entel/elemento/:id_elemento", function($id_elemento) use($app){

	$oElemento= new Elemento();
	$oGaleria= new Galeria();
	$elemento= $oElemento->getElementosById($id_elemento);
	$puntaje= $oElemento->getPuntos($id_elemento);
	$galeria= $oGaleria->getGaleriaClienteByElemento($elemento->id_reporte);
	$elemento->galeria= $galeria;
	$elemento->puntaje= $puntaje;
	$elemento->calificacion = $oElemento->getCalificacion($id_elemento);
	$elemento->total_zona= $oElemento->totalElementosZona($elemento->id_zona, $elemento->id_periodo);

	response($elemento);

});
$app->post("/entel/elementos/periodo/:id_periodo", function($id_periodo) use($app){

	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$page= 0;
	$limit= 20;
	if(isset($todo->page)){
		if($todo->page<0){
			$page= 0;
			$limit= 10000;
		}else{
			$page= $todo->page*20;
		}
	}

	$extra_where= "";
	if(isset($todo->marca) && count($todo->marca)){
		$marcas= $todo->marca;
		$extra_where.=" AND (";
		foreach ($marcas as $item) {
			$extra_where.=" lr.id_marca = ".$item->id_marca." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->provincia) && count($todo->provincia)){
		$provincia= $todo->provincia;
		$extra_where.=" AND (";
		foreach ($provincia as $item) {
			$extra_where.=" lz.id_sys_provincia = ".$item->id_sys_provincia." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->tipo_anuncio) && count($todo->tipo_anuncio)){
		$tipo_anuncio= $todo->tipo_anuncio;
		$extra_where.=" AND lr.id_tipo_anuncio = ".$tipo_anuncio->id_tipo_anuncio;
	}
	if(isset($todo->formato)){
		$formato= $todo->formato;
		$extra_where.=" AND lr.id_formato = ".$formato->id_formato;
	}
	if(isset($todo->linea_producto) && !empty($todo->linea_producto) && count($todo->linea_producto)){
		$linea_producto= $todo->linea_producto;
		$extra_where.=" AND (";
		foreach ($linea_producto as $item) {
			$extra_where.=" lr.id_linea_producto = ".$item->id_linea_producto." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->tipo_elemento) && count($todo->tipo_elemento)){
		$tipo_elemento= $todo->tipo_elemento;
		$extra_where.=" AND (";
		foreach ($tipo_elemento as $item) {
			$extra_where.=" lr.id_tipo_elemento = ".$item->id_tipo_elemento." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	$oElemento= new Elemento();
	$oGaleria= new Galeria();
	$elementos= $oElemento->getElementosPorPeriodoFiltro($id_periodo, $limit, $page, $extra_where);
	$response["elementos"]= [];
	foreach ($elementos as $elemento) {
		$galeria= $oGaleria->getGaleriaByElemento($elemento["id_reporte"]);
		$elemento["galeria"]= $galeria;
		array_push($response["elementos"], $elemento);
	}
	$response["total"]= $oElemento->getTotalElementosPorPeriodo($id_periodo, $extra_where);
	$response["page"]= $page;
	response($response);

});
$app->post("/entel/cartografia/elementos/periodo/:id_periodo", function($id_periodo) use($app){

	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$page= 0;
	$limit= 20;
	if(isset($todo->page)){
		if($todo->page<0){
			$page= 0;
			$limit= 10000;
		}else{
			$page= $todo->page*20;
		}
	}

	$extra_where= "";
	if(isset($todo->marca)){
		$marcas= $todo->marca;
		$extra_where.=" AND (";
		foreach ($marcas as $item) {
			$extra_where.=" lr.id_marca = ".$item->id_marca." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->provincia)){
		$provincia= $todo->provincia;
		$extra_where.=" AND lz.id_sys_provincia = ".$provincia->id_sys_provincia;
	}
	if(isset($todo->tipo_elemento)){
		$tipo_elemento= $todo->tipo_elemento;
		$extra_where.=" AND (";
		foreach ($tipo_elemento as $item) {
			$extra_where.=" lr.id_tipo_elemento = ".$item->id_tipo_elemento." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	$oElemento= new Elemento();
	$oGaleria= new Galeria();
	$oZona= new Zona();
	if(isset($provincia)){
		$zona= $oZona->getProvinciaById($provincia->id_sys_provincia);
	}
	else{
		$zona = null;
	}

	$elementos= $oElemento->getElementosPorPeriodoFiltro($id_periodo, $limit, $page, $extra_where);
	$response["elementos"]= [];
	foreach ($elementos as $elemento) {
		$galeria= $oGaleria->getGaleriaByElemento($elemento["id_reporte"]);
		$elemento["galeria"]= $galeria;
		array_push($response["elementos"], $elemento);
	}
	$response["total"]= $oElemento->getTotalElementosPorPeriodo($id_periodo, $extra_where);
	$response["page"]= $page;
	$response["zona"]= $zona;
	response($response);

});
$app->post("/entel/inversion/periodo/:id_periodo/jalados", function($id_periodo) use($app){

	$page= 0;
	$limit= 10000;
	$oElemento= new Elemento();
	$parametros= $oElemento->getParametros();
	$params= $app->request->getBody();
	$params= json_decode($params);

	$id_puntaje = 1;

	$totalMarcas = $parametros["marcas"];
	$extra_formato = "";
	if(isset($params->formato)){
		$extra_formato = "WHERE id_formato = ".$params->formato->id_formato;
	}
	$totalTipoElementos = $oElemento->getTipoElemento($extra_formato);
	$hay_marcas = 0;
	$hay_tipo_elementos = 0;
	if(isset($params->marca) && count($params->marca)){
		$totalMarcas = $params->marca;
		$hay_marcas = 1;
	}
	if(isset($params->tipo_elemento) && count($params->tipo_elemento)){
		$totalTipoElementos = $params->tipo_elemento;
		$hay_tipo_elementos = 1;
	}
	$resultados_periodos= [];

	$extra_tipo_anuncio = "";
	if(isset($params->tipo_anuncio)){
		$extra_tipo_anuncio = " AND lr.id_tipo_anuncio = ".$params->tipo_anuncio->id_tipo_anuncio;
		if(isset($params->linea_producto) && count($params->linea_producto) && !empty($params->linea_producto)){
			$extra_tipo_anuncio.=" AND (";
			foreach ($params->linea_producto as $item) {
				$extra_tipo_anuncio.=" lr.id_linea_producto = ".$item->id_linea_producto." OR";
			}
			$extra_tipo_anuncio= rtrim($extra_tipo_anuncio, "OR");
			$extra_tipo_anuncio.=")";
		}
	}

	$provincia_where= "";

	if(isset($params->provincia) && count($params->provincia)){
		$provincia= $params->provincia;
		$provincia_where.=" AND (";
		foreach ($provincia as $item) {
			$provincia_where.=" lz.id_sys_provincia=".$item->id_sys_provincia." OR";
		}
		$provincia_where= rtrim($provincia_where, "OR");
		$provincia_where.=")";
	}

	$oPeriodo = new Periodo();
	if($id_periodo == '0'){
		$periodos = $oPeriodo->getPeriodos();
	}else{
		$periodos = $oPeriodo->getPeriodoArray($id_periodo);
	}

	foreach ($periodos as $periodo) {
		$resultados= [];
		foreach ($totalMarcas as $marca) {
			$resultado= [];
			if($hay_marcas){
				$id_marca= $marca->id_marca;
				$marca_nombre= $marca->nombre;
				$marca_logo= $marca->logo;
				$marca_bg= $marca->bg;
			}else{
				$id_marca= $marca["id_marca"];
				$marca_nombre= $marca["nombre"];
				$marca_logo= $marca["logo"];
				$marca_bg= $marca["bg"];
			}

			$resultado["marca_id"]= $id_marca;
			$resultado["marca_nombre"]= $marca_nombre;
			$resultado["marca_logo"]= $marca_logo;
			$resultado["marca_bg"]= $marca_bg;
			$resultado["elementos"]= [];

			$caras= 0;
			$inversion= 0;
			$mts= 0;
			$calificacion= 0;

			foreach ($totalTipoElementos as $key => $tipo_elemento) {
				$id_formato= null;
				$id_tipo_elemento= null;
				$tipo_elemento_nombre= null;
				$tipo_elemento_precio= null;
				$tipo_elemento_mts= null;
				if($hay_tipo_elementos){
					$id_formato= $tipo_elemento->id_formato;
					$id_tipo_elemento= $tipo_elemento->id_tipo_elemento;
					$tipo_elemento_nombre= $tipo_elemento->nombre;
					$tipo_elemento_precio= $tipo_elemento->precio;
					$tipo_elemento_ancho= $tipo_elemento->ancho;
					$tipo_elemento_alto= $tipo_elemento->alto;
					$tipo_elemento_mts= $tipo_elemento->ancho."x".$tipo_elemento->alto;
				}else{
					$id_formato= $tipo_elemento["id_formato"];
					$id_tipo_elemento= $tipo_elemento["id_tipo_elemento"];
					$tipo_elemento_nombre= $tipo_elemento["nombre"];
					$tipo_elemento_precio= $tipo_elemento["precio"];
					$tipo_elemento_ancho= $tipo_elemento["ancho"];
					$tipo_elemento_alto= $tipo_elemento["alto"];
					$tipo_elemento_mts= $tipo_elemento["ancho"]."x".$tipo_elemento["alto"];
				}

				$extra_where=" AND lr.id_marca = $id_marca AND lr.id_formato = $id_formato AND lr.id_tipo_elemento = $id_tipo_elemento".$provincia_where.$extra_tipo_anuncio;

				$elementos= $oElemento->getElementosPorPeriodoFiltro($periodo["id_periodo"], $limit, $page, $extra_where);

				if(count($elementos)==0)
					continue;

				$punto= 0;
				$elementos_temp = $elementos;
				$elementos = [];
				foreach ($elementos_temp as $elemento) {
					$puntaje= $oElemento->getPuntos($elemento["id_reporte"]);
					if(!empty($id_puntaje)){
						if($puntaje->punto <= 30){
							$punto+= $puntaje->punto;
							array_push($elementos, $elemento);
						}
					}else{
						$punto+= $puntaje->punto;
						array_push($elementos, $elemento);
					}
				}

				if(count($elementos)==0)
					continue;

				$tipo_elemento_puntaje= number_format($punto/count($elementos), 2, '.', '');
				$tipo_elemento_inversion= count($elementos)*$tipo_elemento_precio;
				$inversion+= $tipo_elemento_inversion;
				$caras+= count($elementos);
				$tipo_elemento_elementos= count($elementos);
				$tipo_elemento_mts_total= count($elementos)*$tipo_elemento_ancho*$tipo_elemento_alto;
				$mts+= $tipo_elemento_mts_total;
				$calificacion+= $punto;
				$res= array("nombre"=>$tipo_elemento_nombre, "precio_unitario"=>$tipo_elemento_precio, "mts_unit"=>$tipo_elemento_mts, "puntaje"=>$tipo_elemento_puntaje, "inversion"=>$tipo_elemento_inversion, "elementos"=>$tipo_elemento_elementos, "mts_total"=>$tipo_elemento_mts_total);
				array_push($resultado["elementos"], $res);
			}

			$resultado["caras"]= $caras;
			$resultado["inversion"]= $inversion;
			$resultado["mts"]= $mts;
			if($caras){
				$resultado["calificacion"]= number_format($calificacion/$caras, 2, '.', '');
			}else{
				$resultado["calificacion"]= 0;
			}
			array_push($resultados, $resultado);
		}
		array_push($resultados_periodos, array("id_periodo"=>$periodo["id_periodo"], "periodo"=>$periodo["nombre"], "resultado"=>$resultados));
	}
	response($resultados_periodos);

});
$app->post("/entel/inversion/periodo/:id_periodo", function($id_periodo) use($app){

	$page= 0;
	$limit= 10000;
	$oElemento= new Elemento();
	$parametros= $oElemento->getParametros();
	$params= $app->request->getBody();
	$params= json_decode($params);

	$totalMarcas = $parametros["marcas"];
	$extra_formato = "";
	if(isset($params->formato)){
		$extra_formato = "WHERE id_formato = ".$params->formato->id_formato;
	}
	$totalTipoElementos = $oElemento->getTipoElemento($extra_formato);
	$hay_marcas = 0;
	$hay_tipo_elementos = 0;
	if(isset($params->marca) && count($params->marca)){
		$totalMarcas = $params->marca;
		$hay_marcas = 1;
	}
	if(isset($params->tipo_elemento) && count($params->tipo_elemento)){
		$totalTipoElementos = $params->tipo_elemento;
		$hay_tipo_elementos = 1;
	}
	$resultados_periodos= [];

	$extra_tipo_anuncio = "";
	if(isset($params->tipo_anuncio)){
		$extra_tipo_anuncio = " AND lr.id_tipo_anuncio = ".$params->tipo_anuncio->id_tipo_anuncio;
		if(isset($params->linea_producto) && count($params->linea_producto) && !empty($params->linea_producto)){
			$extra_tipo_anuncio.=" AND (";
			foreach ($params->linea_producto as $item) {
				$extra_tipo_anuncio.=" lr.id_linea_producto = ".$item->id_linea_producto." OR";
			}
			$extra_tipo_anuncio= rtrim($extra_tipo_anuncio, "OR");
			$extra_tipo_anuncio.=")";
		}
	}

	$provincia_where= "";

	if(isset($params->provincia) && count($params->provincia)){
		$provincia= $params->provincia;
		$provincia_where.=" AND (";
		foreach ($provincia as $item) {
			$provincia_where.=" lz.id_sys_provincia=".$item->id_sys_provincia." OR";
		}
		$provincia_where= rtrim($provincia_where, "OR");
		$provincia_where.=")";
	}

	$oPeriodo = new Periodo();
	if($id_periodo == '0'){
		$periodos = $oPeriodo->getPeriodos();
	}else{
		$periodos = $oPeriodo->getPeriodoArray($id_periodo);
	}

	foreach ($periodos as $periodo) {
		$resultados= [];
		foreach ($totalMarcas as $marca) {
			$resultado= [];
			if($hay_marcas){
				$id_marca= $marca->id_marca;
				$marca_nombre= $marca->nombre;
				$marca_logo= $marca->logo;
				$marca_bg= $marca->bg;
			}else{
				$id_marca= $marca["id_marca"];
				$marca_nombre= $marca["nombre"];
				$marca_logo= $marca["logo"];
				$marca_bg= $marca["bg"];
			}

			$resultado["marca_id"]= $id_marca;
			$resultado["marca_nombre"]= $marca_nombre;
			$resultado["marca_logo"]= $marca_logo;
			$resultado["marca_bg"]= $marca_bg;
			$resultado["elementos"]= [];

			$caras= 0;
			$inversion= 0;
			$mts= 0;
			$calificacion= 0;

			foreach ($totalTipoElementos as $key => $tipo_elemento) {
				$id_formato= null;
				$id_tipo_elemento= null;
				$tipo_elemento_nombre= null;
				$tipo_elemento_precio= null;
				$tipo_elemento_mts= null;
				if($hay_tipo_elementos){
					$id_formato= $tipo_elemento->id_formato;
					$id_tipo_elemento= $tipo_elemento->id_tipo_elemento;
					$tipo_elemento_nombre= $tipo_elemento->nombre;
					$tipo_elemento_precio= $tipo_elemento->precio;
					$tipo_elemento_ancho= $tipo_elemento->ancho;
					$tipo_elemento_alto= $tipo_elemento->alto;
					$tipo_elemento_mts= $tipo_elemento->ancho."x".$tipo_elemento->alto;
				}else{
					$id_formato= $tipo_elemento["id_formato"];
					$id_tipo_elemento= $tipo_elemento["id_tipo_elemento"];
					$tipo_elemento_nombre= $tipo_elemento["nombre"];
					$tipo_elemento_precio= $tipo_elemento["precio"];
					$tipo_elemento_ancho= $tipo_elemento["ancho"];
					$tipo_elemento_alto= $tipo_elemento["alto"];
					$tipo_elemento_mts= $tipo_elemento["ancho"]."x".$tipo_elemento["alto"];
				}

				$extra_where=" AND lr.id_marca = $id_marca AND lr.id_formato = $id_formato AND lr.id_tipo_elemento = $id_tipo_elemento".$provincia_where.$extra_tipo_anuncio;

				$elementos= $oElemento->getElementosPorPeriodoFiltro($periodo["id_periodo"], $limit, $page, $extra_where);

				if(count($elementos)==0)
					continue;

				$punto= 0;
				$elementos_temp = $elementos;
				$elementos = [];
				foreach ($elementos_temp as $elemento) {
					$puntaje= $oElemento->getPuntos($elemento["id_reporte"]);
					$punto+= $puntaje->punto;
					array_push($elementos, $elemento);
				}

				if(count($elementos)==0)
					continue;

				$tipo_elemento_puntaje= number_format($punto/count($elementos), 2, '.', '');
				$tipo_elemento_inversion= count($elementos)*$tipo_elemento_precio;
				$inversion+= $tipo_elemento_inversion;
				$caras+= count($elementos);
				$tipo_elemento_elementos= count($elementos);
				$tipo_elemento_mts_total= count($elementos)*$tipo_elemento_ancho*$tipo_elemento_alto;
				$mts+= $tipo_elemento_mts_total;
				$calificacion+= $punto;
				$res= array("nombre"=>$tipo_elemento_nombre, "precio_unitario"=>$tipo_elemento_precio, "mts_unit"=>$tipo_elemento_mts, "puntaje"=>$tipo_elemento_puntaje, "inversion"=>$tipo_elemento_inversion, "elementos"=>$tipo_elemento_elementos, "mts_total"=>$tipo_elemento_mts_total);
				array_push($resultado["elementos"], $res);
			}

			$resultado["caras"]= $caras;
			$resultado["inversion"]= $inversion;
			$resultado["mts"]= $mts;
			if($caras){
				$resultado["calificacion"]= number_format($calificacion/$caras, 2, '.', '');
			}else{
				$resultado["calificacion"]= 0;
			}
			array_push($resultados, $resultado);
		}
		array_push($resultados_periodos, array("id_periodo"=>$periodo["id_periodo"], "periodo"=>$periodo["nombre"], "resultado"=>$resultados));
	}
	response($resultados_periodos);

});
$app->get("/entel/inversion/periodo/:id_periodo", function($id_periodo) use($app){

	$page= 0;
	$limit= 10000;
	$oElemento= new Elemento();
	$parametros= $oElemento->getParametros();
	$resultados_periodos= [];
	$oPeriodo = new Periodo();
	if($id_periodo == '0'){
		$periodos = $oPeriodo->getPeriodos();
	}else{
		$periodos = $oPeriodo->getPeriodoArray($id_periodo);
	}

	foreach ($periodos as $periodo) {
		$resultados= [];
		foreach ($parametros["marcas"] as $marca) {
			$resultado= [];
			$id_marca= $marca["id_marca"];
			$marca_nombre= $marca["nombre"];
			$marca_logo= $marca["logo"];
			$marca_bg= $marca["bg"];

			$resultado["marca_id"]= $id_marca;
			$resultado["marca_nombre"]= $marca_nombre;
			$resultado["marca_logo"]= $marca_logo;
			$resultado["marca_bg"]= $marca_bg;
			$resultado["elementos"]= [];

			$caras= 0;
			$inversion= 0;
			$mts= 0;
			$calificacion= 0;

			foreach ($parametros["tipo_elementos"] as $tipo_elemento) {
				$id_formato= $tipo_elemento["id_formato"];
				$id_tipo_elemento= $tipo_elemento["id_tipo_elemento"];

				$extra_where=" AND lr.id_marca = $id_marca AND lr.id_formato = $id_formato AND lr.id_tipo_elemento = $id_tipo_elemento";

				$elementos= $oElemento->getElementosPorPeriodoFiltro($periodo["id_periodo"], $limit, $page, $extra_where);

				/*if(count($elementos)==0)
					continue;*/

				$tipo_elemento_nombre= $tipo_elemento["nombre"];
				$tipo_elemento_precio= $tipo_elemento["precio"];
				$tipo_elemento_mts= $tipo_elemento["ancho"]."x".$tipo_elemento["alto"];

				$punto= 0;
				foreach ($elementos as $elemento) {
					$puntaje= $oElemento->getPuntos($elemento["id_reporte"]);
					$punto+= $puntaje->punto;
				}
				if(count($elementos)>0){
					$tipo_elemento_puntaje= number_format($punto/count($elementos), 2, '.', '');
				}else{
					$tipo_elemento_puntaje = 0;
				}
				$tipo_elemento_inversion= count($elementos)*$tipo_elemento_precio;
				$inversion+= $tipo_elemento_inversion;
				$caras+= count($elementos);
				$tipo_elemento_elementos= count($elementos);
				$tipo_elemento_mts_total= count($elementos)*$tipo_elemento["ancho"]*$tipo_elemento["alto"];
				$mts+= $tipo_elemento_mts_total;
				$calificacion+= $punto;
				$res= array("nombre"=>$tipo_elemento_nombre, "precio_unitario"=>$tipo_elemento_precio, "mts_unit"=>$tipo_elemento_mts, "puntaje"=>$tipo_elemento_puntaje, "inversion"=>$tipo_elemento_inversion, "elementos"=>$tipo_elemento_elementos, "mts_total"=>$tipo_elemento_mts_total);
				array_push($resultado["elementos"], $res);
			}

			$resultado["caras"]= $caras;
			$resultado["inversion"]= $inversion;
			$resultado["mts"]= $mts;
			$resultado["calificacion"]= number_format($calificacion/$caras, 2, '.', '');
			array_push($resultados, $resultado);
		}
		array_push($resultados_periodos, array("id_periodo"=>$periodo["id_periodo"], "periodo"=>$periodo["nombre"], "resultado"=>$resultados));
	}
	response($resultados_periodos);

});

/**
 * EXCEL
 */
$app->post("/entel/elementos/periodo/:id_periodo/xls", function($id_periodo) use($app){

	$todo= $app->request->getBody();
	$todo= json_decode($todo);
	$page= 0;
	$limit= 10000;

	$extra_where= "";
	if(isset($todo->marca) && count($todo->marca)){
		$marcas= $todo->marca;
		$extra_where.=" AND (";
		foreach ($marcas as $item) {
			$extra_where.=" lr.id_marca = ".$item->id_marca." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->provincia) && count($todo->provincia)){
		$provincia= $todo->provincia;
		$extra_where.=" AND (";
		foreach ($provincia as $item) {
			$extra_where.=" lz.id_sys_provincia = ".$item->id_sys_provincia." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->tipo_anuncio) && count($todo->tipo_anuncio)){
		$tipo_anuncio= $todo->tipo_anuncio;
		$extra_where.=" AND lr.id_tipo_anuncio = ".$tipo_anuncio->id_tipo_anuncio;
	}
	if(isset($todo->formato)){
		$formato= $todo->formato;
		$extra_where.=" AND lr.id_formato = ".$formato->id_formato;
	}
	if(isset($todo->linea_producto) && count($todo->linea_producto)){
		$linea_producto= $todo->linea_producto;
		$extra_where.=" AND (";
		foreach ($linea_producto as $item) {
			$extra_where.=" lr.id_linea_producto = ".$item->id_linea_producto." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	if(isset($todo->tipo_elemento) && count($todo->tipo_elemento)){
		$tipo_elemento= $todo->tipo_elemento;
		$extra_where.=" AND (";
		foreach ($tipo_elemento as $item) {
			$extra_where.=" lr.id_tipo_elemento = ".$item->id_tipo_elemento." OR";
		}
		$extra_where= rtrim($extra_where, "OR");
		$extra_where.=")";
	}
	$oElemento= new Elemento();
	$oGaleria= new Galeria();
	$elementos= $oElemento->getElementosPorPeriodoFiltro($id_periodo, $limit, $page, $extra_where);
	$response["elementos"]= [];
	foreach ($elementos as $elemento) {
		$puntaje= $oElemento->getPuntos($elemento["id_reporte"]);
		$elemento["puntaje"]= $puntaje;
		array_push($response["elementos"], $elemento);
	}
	$total= $oElemento->getTotalElementosPorPeriodo($id_periodo, $extra_where);

	$main_path= $_SERVER['DOCUMENT_ROOT'];
	require_once($main_path.'/libs/excel/PHPExcel.php');
	$objPHPExcel = new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER['DOCUMENT_ROOT']."/templates/formato-elementos.xls");
	$objPHPExcel->getProperties()->setCreator("85grados");

	$celda= 4;
	$lista_elementos = $response["elementos"];

	foreach ($lista_elementos as $elemento) {
		$inversion = $elemento["ancho"]*$elemento["alto"]*45;
		$dimensiones = $elemento["ancho"]."x".$elemento["alto"];
		$celda ++;
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('C'.$celda, $total)
			->setCellValue('D'.$celda, $elemento["formato"]." - ".$elemento["tipo_elemento"])
			->setCellValue('E'.$celda, $elemento["tipo_anuncio"])
			->setCellValue('F'.$celda, $elemento["linea_producto"])
			->setCellValue('G'.$celda, $elemento["provincia"])
			->setCellValue('H'.$celda, $elemento["distrito"])
			->setCellValue('I'.$celda, $elemento["direccion"])
			->setCellValue('J'.$celda, $elemento["marca"])
			//->setCellValue('K'.$celda, 1)
			->setCellValue('K'.$celda, 1)
			->setCellValue('L'.$celda, $elemento["puntaje"]->punto)
			->setCellValue('M'.$celda, $dimensiones)
			->setCellValue('N'.$celda, $elemento["periodo"])
			->setCellValue('Q'.$celda, $elemento["alquiler_mensual"])
			->setCellValue('S'.$celda, $inversion);
		$total--;
	}
	$objPHPExcel->getActiveSheet()->setTitle('85grados');
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$nombre= md5("reporte".date('Y-m-d G:i:s'));
	$objWriter->save($main_path.'/files/'.$nombre.'.xls');
	response(array("url"=>"http://detectaauditoria.pe/files/".$nombre.".xls"));

});
$app->post("/entel/inversion/periodo/:id_periodo/xls", function($id_periodo) use($app){

	$page= 0;
	$limit= 10000;
	$oElemento= new Elemento();
	$parametros= $oElemento->getParametros();
	$params= $app->request->getBody();
	$params= json_decode($params);

	$totalMarcas = $parametros["marcas"];
	$extra_formato = "";
	if(isset($params->formato)){
		$extra_formato = "WHERE id_formato = ".$params->formato->id_formato;
	}
	$totalTipoElementos = $oElemento->getTipoElemento($extra_formato);
	$hay_marcas = 0;
	$hay_tipo_elementos = 0;
	if(isset($params->marca) && count($params->marca)){
		$totalMarcas = $params->marca;
		$hay_marcas = 1;
	}
	if(isset($params->tipo_elemento) && count($params->tipo_elemento)){
		$totalTipoElementos = $params->tipo_elemento;
		$hay_tipo_elementos = 1;
	}
	$resultados_periodos= [];

	$extra_tipo_anuncio = "";
	if(isset($params->tipo_anuncio)){
		$extra_tipo_anuncio = " AND lr.id_tipo_anuncio = ".$params->tipo_anuncio->id_tipo_anuncio;
		if(isset($params->linea_producto) && count($params->linea_producto) && !empty($params->linea_producto)){
			$extra_tipo_anuncio.=" AND (";
			foreach ($params->linea_producto as $item) {
				$extra_tipo_anuncio.=" lr.id_linea_producto = ".$item->id_linea_producto." OR";
			}
			$extra_tipo_anuncio= rtrim($extra_tipo_anuncio, "OR");
			$extra_tipo_anuncio.=")";
		}
	}

	$provincia_where= "";

	if(isset($params->provincia) && count($params->provincia)){
		$provincia= $params->provincia;
		$provincia_where.=" AND (";
		foreach ($provincia as $item) {
			$provincia_where.=" lz.id_sys_provincia=".$item->id_sys_provincia." OR";
		}
		$provincia_where= rtrim($provincia_where, "OR");
		$provincia_where.=")";
	}

	$oPeriodo = new Periodo();
	if($id_periodo == '0'){
		$periodos = $oPeriodo->getPeriodos();
	}else{
		$periodos = $oPeriodo->getPeriodoArray($id_periodo);
	}

	foreach ($periodos as $periodo) {
		$resultados= [];
		foreach ($totalMarcas as $marca) {
			$resultado= [];
			if($hay_marcas){
				$id_marca= $marca->id_marca;
				$marca_nombre= $marca->nombre;
				$marca_logo= $marca->logo;
				$marca_bg= $marca->bg;
			}else{
				$id_marca= $marca["id_marca"];
				$marca_nombre= $marca["nombre"];
				$marca_logo= $marca["logo"];
				$marca_bg= $marca["bg"];
			}

			$resultado["marca_id"]= $id_marca;
			$resultado["marca_nombre"]= $marca_nombre;
			$resultado["marca_logo"]= $marca_logo;
			$resultado["marca_bg"]= $marca_bg;
			$resultado["elementos"]= [];

			$caras= 0;
			$inversion= 0;
			$mts= 0;
			$calificacion= 0;

			foreach ($totalTipoElementos as $key => $tipo_elemento) {
				$id_formato= null;
				$id_tipo_elemento= null;
				$tipo_elemento_nombre= null;
				$tipo_elemento_precio= null;
				$tipo_elemento_mts= null;
				if($hay_tipo_elementos){
					$id_formato= $tipo_elemento->id_formato;
					$id_tipo_elemento= $tipo_elemento->id_tipo_elemento;
					$tipo_elemento_nombre= $tipo_elemento->nombre;
					$tipo_elemento_precio= $tipo_elemento->precio;
					$tipo_elemento_ancho= $tipo_elemento->ancho;
					$tipo_elemento_alto= $tipo_elemento->alto;
					$tipo_elemento_mts= $tipo_elemento->ancho."x".$tipo_elemento->alto;
				}else{
					$id_formato= $tipo_elemento["id_formato"];
					$id_tipo_elemento= $tipo_elemento["id_tipo_elemento"];
					$tipo_elemento_nombre= $tipo_elemento["nombre"];
					$tipo_elemento_precio= $tipo_elemento["precio"];
					$tipo_elemento_ancho= $tipo_elemento["ancho"];
					$tipo_elemento_alto= $tipo_elemento["alto"];
					$tipo_elemento_mts= $tipo_elemento["ancho"]."x".$tipo_elemento["alto"];
				}

				$extra_where=" AND lr.id_marca = $id_marca AND lr.id_formato = $id_formato AND lr.id_tipo_elemento = $id_tipo_elemento".$provincia_where.$extra_tipo_anuncio;

				$elementos= $oElemento->getElementosPorPeriodoFiltro($periodo["id_periodo"], $limit, $page, $extra_where);

				if(count($elementos)==0)
					continue;

				$punto= 0;
				$elementos_temp = $elementos;
				$elementos = [];
				foreach ($elementos_temp as $elemento) {
					$puntaje= $oElemento->getPuntos($elemento["id_reporte"]);
					$punto+= $puntaje->punto;
					array_push($elementos, $elemento);
				}

				if(count($elementos)==0)
					continue;

				$tipo_elemento_puntaje= number_format($punto/count($elementos), 2, '.', '');
				$tipo_elemento_inversion= count($elementos)*$tipo_elemento_precio;
				$inversion+= $tipo_elemento_inversion;
				$caras+= count($elementos);
				$tipo_elemento_elementos= count($elementos);
				$tipo_elemento_mts_total= count($elementos)*$tipo_elemento_ancho*$tipo_elemento_alto;
				$mts+= $tipo_elemento_mts_total;
				$calificacion+= $punto;
				$res= array("nombre"=>$tipo_elemento_nombre, "precio_unitario"=>$tipo_elemento_precio, "mts_unit"=>$tipo_elemento_mts, "puntaje"=>$tipo_elemento_puntaje, "inversion"=>$tipo_elemento_inversion, "elementos"=>$tipo_elemento_elementos, "mts_total"=>$tipo_elemento_mts_total);
				array_push($resultado["elementos"], $res);
			}

			$resultado["caras"]= $caras;
			$resultado["inversion"]= $inversion;
			$resultado["mts"]= $mts;
			if($caras){
				$resultado["calificacion"]= number_format($calificacion/$caras, 2, '.', '');
			}else{
				$resultado["calificacion"]= 0;
			}
			array_push($resultados, $resultado);
		}
		array_push($resultados_periodos, array("id_periodo"=>$periodo["id_periodo"], "periodo"=>$periodo["nombre"], "resultado"=>$resultados));
	}

	$periodos_final = [];
	$periodos_nuevo = [];
	foreach ($resultados_periodos as $periodo) {
		foreach ($periodo["resultado"] as $marca) {
			if(!isset($periodos_nuevo[$marca["marca_nombre"]])){
				$periodos_nuevo[$marca["marca_nombre"]] = [];
			}
			$marca["nombre_periodo"] = $periodo["periodo"];
			array_push($periodos_nuevo[$marca["marca_nombre"]], $marca);
		}
	}
	foreach ($periodos_nuevo as $periodo) {
		$elemento_formato = array(
			"marca_bg"=>$periodo[0]["marca_bg"],
			"marca_logo"=> $periodo[0]["marca_logo"],
      "marca_id"=> $periodo[0]["marca_id"],
      "marca_nombre"=> $periodo[0]["marca_nombre"],
      "elementos"=> [],
      "calificacion"=> [],
      "caras"=> [],
      "inversion"=> [],
      "mts"=> [],
      "nombre_periodo"=> []
		);
		foreach ($periodo as $k=>$marcas) {
			array_push($elemento_formato["caras"], $marcas["caras"]);
			array_push($elemento_formato["inversion"], $marcas["inversion"]);
			array_push($elemento_formato["mts"], $marcas["mts"]);
			array_push($elemento_formato["nombre_periodo"], $marcas["nombre_periodo"]);
			array_push($elemento_formato["calificacion"], $marcas["calificacion"]);
			foreach ($marcas["elementos"] as $elemento) {
				$temp_elemento = array(
          "elementos"=> 0,
          "inversion"=> 0,
          "mts_total"=> 0,
          "precio_unitario"=> 0,
          "puntaje"=> 0
        );
        if(!isset($elemento_formato["elementos"][$elemento["nombre"]])){
          $elemento_formato["elementos"][$elemento["nombre"]] = [];
          if($k == '1'){
          	array_push($elemento_formato["elementos"][$elemento["nombre"]], $temp_elemento);
          }
        }
        $temp_elemento = array(
          "elementos"=> $elemento["elementos"],
          "inversion"=> $elemento["inversion"],
          "mts_total"=> $elemento["mts_total"],
          "precio_unitario"=> $elemento["precio_unitario"],
          "puntaje"=> $elemento["puntaje"]
        );
        array_push($elemento_formato["elementos"][$elemento["nombre"]], $temp_elemento);
			}
		}
		array_push($periodos_final, $elemento_formato);
	}
	if(count($periodos_final[0]["caras"])>1){
		foreach ($periodos_final as $k=>$periodo) {
			foreach ($periodo["elementos"] as $k1=>$elemento) {
				if(count($elemento) == 1){
	        $temp_elemento = array(
	        	"elementos"=> 0,
	          "inversion"=> 0,
	          "mts_total"=> 0,
	          "precio_unitario"=> 0,
	          "puntaje"=> 0
	        );
	        array_push($periodos_final[$k]["elementos"][$k1], $temp_elemento);
	      }
			}
		}
	}

	$main_path= $_SERVER['DOCUMENT_ROOT'];
	require_once($main_path.'/libs/excel/PHPExcel.php');
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("85grados");

	$celda= 1;
	$columna= 'A';
	$initial= $columna;
	foreach($periodos_final as $k=> $marcas){
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, "Marca");

		$celda++;
		$columna= $initial;

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, "");
			$columna++;

		foreach ($marcas["nombre_periodo"] as $nombre_periodo) {
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, $nombre_periodo);
			$columna++;
		}
		if(count($marcas["nombre_periodo"])>1){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, "Diferencia");
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, "Variación");
			$columna++;
		}

		$celda++;
		$columna= $initial;

		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($columna.$celda, $marcas["marca_nombre"]);
		$columna++;
		foreach ($marcas["caras"] as $cara) {
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, $cara);
			$columna++;
		}
		if(count($marcas["caras"])>1){
			$diferencia = $marcas["caras"][1]-$marcas["caras"][0];
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, $diferencia);
			$columna++;
			$variacion = 0;
			if($marcas["caras"][0] == 0 && $marcas["caras"][1] !== 0){
				$variacion = 100;
			}else if($marcas["caras"][0] == 0 && $marcas["caras"][1] == 0){
				$variacion = 0;
			}else{
				$variacion = $marcas["caras"][1]*100 / $marcas["caras"][0] - 100;
			}
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, $variacion);
			$columna++;
		}

		$celda++;
		$columna= $initial;

		foreach ($marcas["elementos"] as $k => $elemento) {
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($columna.$celda, $k);
			$columna++;
			for ($i=0; $i < count($elemento); $i++) {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $elemento[$i]["elementos"]);
				$columna++;
			}
			if(count($elemento) > 1){
				$diferencia = $elemento[1]["elementos"]-$elemento[0]["elementos"];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $diferencia);
				$columna++;
				$variacion = 0;
				if($elemento[0]["elementos"] == 0 && $elemento[1]["elementos"] !== 0){
					$variacion = 100;
				}else if($elemento[0]["elementos"] == 0 && $elemento[1]["elementos"] == 0){
					$variacion = 0;
				}else{
					$variacion = $elemento[1]["elementos"]*100 / $elemento[0]["elementos"] - 100;
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $variacion);
				$columna++;
			}

			$columna++;

			for ($i=0; $i < count($elemento); $i++) {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $elemento[$i]["inversion"]);
				$columna++;
			}
			if(count($elemento) > 1){
				$diferencia = $elemento[1]["inversion"]-$elemento[0]["inversion"];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $diferencia);
				$columna++;
				$variacion = 0;
				if($elemento[0]["inversion"] == 0 && $elemento[1]["inversion"] !== 0){
					$variacion = 100;
				}else if($elemento[0]["inversion"] == 0 && $elemento[1]["inversion"] == 0){
					$variacion = 0;
				}else{
					$variacion = $elemento[1]["inversion"]*100 / $elemento[0]["inversion"] - 100;
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $variacion);
				$columna++;
			}

			$columna++;

			for ($i=0; $i < count($elemento); $i++) {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $elemento[$i]["mts_total"]);
				$columna++;
			}
			if(count($elemento) > 1){
				$diferencia = $elemento[1]["mts_total"]-$elemento[0]["mts_total"];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $diferencia);
				$columna++;
				$variacion = 0;
				if($elemento[0]["mts_total"] == 0 && $elemento[1]["mts_total"] !== 0){
					$variacion = 100;
				}else if($elemento[0]["mts_total"] == 0 && $elemento[1]["mts_total"] == 0){
					$variacion = 0;
				}else{
					$variacion = $elemento[1]["mts_total"]*100 / $elemento[0]["mts_total"] - 100;
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $variacion);
				$columna++;
			}

			$columna++;

			for ($i=0; $i < count($elemento); $i++) {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $elemento[$i]["puntaje"]);
				$columna++;
			}
			if(count($elemento) > 1){
				$diferencia = $elemento[1]["puntaje"]-$elemento[0]["puntaje"];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $diferencia);
				$columna++;
				$variacion = 0;
				if($elemento[0]["puntaje"] == 0 && $elemento[1]["puntaje"] !== 0){
					$variacion = 100;
				}else if($elemento[0]["puntaje"] == 0 && $elemento[1]["puntaje"] == 0){
					$variacion = 0;
				}else{
					$variacion = $elemento[1]["puntaje"]*100 / $elemento[0]["puntaje"] - 100;
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $variacion);
				$columna++;
			}

			$celda++;
			$columna= $initial;
		}

		$celda++;
		$celda++;
		$columna= $initial;

	}


	$objPHPExcel->getActiveSheet()->setTitle('85grados');
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$nombre= md5("reporte".date('Y-m-d G:i:s'));
	$objWriter->save($main_path.'/files/'.$nombre.'.xls');
	response(array('code'=>200,"url"=>"http://detectaauditoria.pe/files/".$nombre.".xls"));

});

$app->post("/inversion/cliente/todos/xls", function() use($app){
	try {
		$todo= $app->request->getBody();
		$todo= json_decode($todo);
		$json = file_get_contents($_SERVER['DOCUMENT_ROOT']."/params.json");
		$params = json_decode($json, true);

		$marcas= $params["marcas"];
		$ciudades= [];
		$tipo_anuncio= null;
		$formato= null;
		$linea_producto= [];
		$tipo_elementos_foreach= $params["tipo_elemento"];
		$extra_where= "";
		if(isset($todo->marcas)){
			$marcas= $todo->marcas;
		}
		if(isset($todo->ciudades)){
			$ciudades= $todo->ciudades;
			$extra_where.=" AND (";
			foreach ($ciudades as $item) {
				$extra_where.=" lz.id_sys_provincia=$item OR";
			}
			$extra_where= rtrim($extra_where, "OR");
			$extra_where.=")";
		}
		if(isset($todo->tipo_anuncio) AND !empty($todo->tipo_anuncio)){
			$tipo_anuncio= $todo->tipo_anuncio;
			$extra_where.=" AND lr.id_master_tipo_anuncio= $tipo_anuncio";
		}
		if(isset($todo->linea_producto)){
			$linea_producto= $todo->linea_producto;
			$extra_where.=" AND (";
			foreach ($linea_producto as $item) {
				$extra_where.=" lr.id_master_linea_producto=$item OR";
			}
			$extra_where= rtrim($extra_where, "OR");
			$extra_where.=")";
		}
		if(isset($todo->tipo_elemento)){
			$tipo_elementos_foreach= $todo->tipo_elemento;
		}

		$resultados= [];
		$marcas_reg= $params["marcas"];
		$tipo_elementos= $params["tipo_elemento"];

		$carriles= $params["carriles"];
		$flujo_vehicular= $params["flujo_vehicular"];
		$flujo_peatonal= $params["flujo_peatonal"];
		$duracion_mirada= $params["duracion_mirada"];
		$obstrucciones= $params["obstrucciones"];
		$calidad_iluminacion= $params["calidad_iluminacion"];
		$dominacion= $params["dominacion"];
		$saturacion= $params["saturacion"];

		foreach ($marcas as $marca) {
			if(is_array($marca)){
				$id_marca= $marca["id"];
				$resultado= [];
				$resultado["marca_id"]= $id_marca;
				$resultado["marca_nombre"]= $marca["nombre"];
				$resultado["marca_logo"]= $marca["logo"];
				$resultado["elementos"]= [];
				$resultado["marca_bg"]= $marca["bg"];
			}else{
				$id_marca= $marca;

				$resultado= [];
				$resultado["marca_id"]= $id_marca;
				$resultado["elementos"]= [];

				foreach ($marcas_reg as $value) {
					if($value["id"]== $marca){
						$resultado["marca_nombre"]= $value["nombre"];
						$resultado["marca_logo"]= $value["logo"];
						$resultado["marca_bg"]= $value["bg"];
						break;
					}
				}
			}

			$caras= 0;
			$inversion= 0;
			$mts= 0;
			$calificacion= 0;

			foreach ($tipo_elementos_foreach as $key=>$tipo_elemento) {
				if(is_array($tipo_elemento)){
					$formato= $key;
					if(isset($todo->formato) AND !empty($todo->formato)){
						if($todo->formato != $key){
							continue;
						}else{
							$formato= $todo->formato;
						}
					}
					foreach ($tipo_elemento as $item_te) {
						$id_tipo_elemento= $item_te["id"];
						$query= "SELECT * FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_departamento sd ON sd.id_sys_departamento = lz.id_sys_departamento INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia WHERE lr.id_marca=$id_marca AND lr.id_tipo_elemento=$id_tipo_elemento AND id_formato=$formato $extra_where AND lr.aprobado_auditor=1";
						$response= getFn($query, 'all');
						$items_te= $response["response"];
						if(count($items_te)==0)
							continue;
						$tipo_elemento_resultado= [];
						$tipo_elemento_resultado["nombre"]= $item_te["nombre"];
						$tipo_elemento_resultado["precio_unitario"]= $item_te["precio"];
						$tipo_elemento_resultado["mts_unit"]= $item_te["medidas"];
						$puntaje= 0;
						foreach ($items_te as $te) {
							foreach ($carriles as $k) {
								if($te["id_master_carriles"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
							foreach ($flujo_vehicular as $k) {
								if($te["id_master_obs_vehicular"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
							foreach ($flujo_peatonal as $k) {
								if($te["id_master_obs_peaton"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
							foreach ($obstrucciones as $k) {
								if($te["id_master_obstruccion"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
							$calidad_iluminacion_n= $calidad_iluminacion[1];
							if($te["id_master_iluminacion"]== 3){
								$calidad_iluminacion_n= $calidad_iluminacion[2];
							}
							foreach ($calidad_iluminacion_n as $k) {
								if($te["id_master_calidad_iluminacion"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
							foreach ($dominacion as $k) {
								if($te["id_master_dominacion_vs_elementos"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
							foreach ($saturacion as $k) {
								if($te["id_master_dominacion_vs_elementos"]==$k["id"]){
									$puntaje+= $k["peso"]*$k["nota"];
									break;
								}
							}
						}
						$tipo_elemento_resultado["puntaje"]= number_format($puntaje/count($items_te), 2, '.', '');
						$tipo_elemento_resultado["inversion"]= count($items_te)*$item_te["precio"];
						$inversion+= count($items_te)*$item_te["precio"];
						$medidas= explode("x", $item_te["medidas"]);
						$caras+= count($items_te);
						$tipo_elemento_resultado["elementos"]= count($items_te);
						$tipo_elemento_resultado["mts_total"]= count($items_te)*$medidas[0]*$medidas[1];
						$mts+= count($items_te)*$medidas[0]*$medidas[1];
						$calificacion+= $puntaje;

						array_push($resultado["elementos"], $tipo_elemento_resultado);
					}
				}else{
					if(isset($todo->formato) AND !empty($todo->formato)){
						$formato= $todo->formato;
						$extra_where.=" AND lr.id_formato= $formato";
					}
					$query= "SELECT * FROM la_reportes lr INNER JOIN la_zona lz ON lr.id_zona = lz.id_zona INNER JOIN sys_departamento sd ON sd.id_sys_departamento = lz.id_sys_departamento INNER JOIN sys_provincia sp ON sp.id_sys_provincia = lz.id_sys_provincia WHERE lr.id_marca=$id_marca AND lr.id_tipo_elemento=$tipo_elemento $extra_where AND lr.aprobado_auditor=1";
					$response= getFn($query, 'all');
					$items_te= $response["response"];
					if(count($items_te)==0)
						continue;
					$tipo_elemento_resultado= [];

					$item_te= null;
					foreach ($tipo_elementos as $k=>$te) {
						if($k==$formato){
							foreach ($te as $va) {
								if($va["id"]==$tipo_elemento){
									$item_te= $va;
									break;
								}
							}
						}
						if($item_te != null)
							break;
					}
					$tipo_elemento_resultado["nombre"]= $item_te["nombre"];
					$tipo_elemento_resultado["precio_unitario"]= $item_te["precio"];
					$tipo_elemento_resultado["mts_unit"]= $item_te["medidas"];
					$puntaje= 0;
					foreach ($items_te as $te) {
						foreach ($carriles as $k) {
							if($te["id_master_carriles"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
						foreach ($flujo_vehicular as $k) {
							if($te["id_master_obs_vehicular"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
						foreach ($flujo_peatonal as $k) {
							if($te["id_master_obs_peaton"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
						foreach ($obstrucciones as $k) {
							if($te["id_master_obstruccion"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
						$calidad_iluminacion_n= $calidad_iluminacion[1];
						if($te["id_master_iluminacion"]== 3){
							$calidad_iluminacion_n= $calidad_iluminacion[2];
						}
						foreach ($calidad_iluminacion_n as $k) {
							if($te["id_master_calidad_iluminacion"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
						foreach ($dominacion as $k) {
							if($te["id_master_dominacion_vs_elementos"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
						foreach ($saturacion as $k) {
							if($te["id_master_dominacion_vs_elementos"]==$k["id"]){
								$puntaje+= $k["peso"]*$k["nota"];
								break;
							}
						}
					}
					$tipo_elemento_resultado["puntaje"]= number_format($puntaje/count($items_te), 2, '.', '');
					$tipo_elemento_resultado["inversion"]= count($items_te)*$item_te["precio"];
					$inversion+= count($items_te)*$item_te["precio"];
					$medidas= explode("x", $item_te["medidas"]);
					$caras+= count($items_te);
					$tipo_elemento_resultado["elementos"]= count($items_te);
					$tipo_elemento_resultado["mts_total"]= count($items_te)*$medidas[0]*$medidas[1];
					$mts+= count($items_te)*$medidas[0]*$medidas[1];
					$calificacion+= $puntaje;

					array_push($resultado["elementos"], $tipo_elemento_resultado);
				}
			}
			$resultado["caras"]= $caras;
			$resultado["inversion"]= $inversion;
			$resultado["mts"]= $mts;
			if(!empty($calificacion)){
				$resultado["calificacion"]= number_format($calificacion/$caras, 2, '.', '');
			}else{
				$resultado["calificacion"]= 0;
			}
			array_push($resultados, $resultado);
		}
		$main_path= $_SERVER['DOCUMENT_ROOT'];
		require_once($main_path.'/libs/excel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("85grados");

		$celda= 1;
		$columna= 'A';
		$initial= $columna;
		foreach($resultados as $item){
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, "Marca");
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, "Caras");
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, "Inversión");
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, "Mt2");
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, "Calificación");

			$celda++;
			$columna= $initial;

			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $item["marca_nombre"]);
			$objPHPExcel->getActiveSheet()->getStyle($columna.$celda)->getFont()->setBold(true);
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $item["caras"]);
			$objPHPExcel->getActiveSheet()->getStyle($columna.$celda)->getFont()->setBold(true);
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $item["inversion"]);
			$objPHPExcel->getActiveSheet()->getStyle($columna.$celda)->getFont()->setBold(true);
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $item["mts"]);
			$objPHPExcel->getActiveSheet()->getStyle($columna.$celda)->getFont()->setBold(true);
			$columna++;
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $item["calificacion"]);
			$objPHPExcel->getActiveSheet()->getStyle($columna.$celda)->getFont()->setBold(true);

			$celda++;

			foreach ($item["elementos"] as $it) {
				$columna= $initial;
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($columna.$celda, $it["nombre"]);
				$columna++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($columna.$celda, $it["elementos"]);
				$columna++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($columna.$celda, $it["inversion"]);
				$columna++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($columna.$celda, $it["mts_total"]);
				$columna++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($columna.$celda, $it["puntaje"]);
				$celda++;
			}

			$columna++;
			$columna++;
			$initial= $columna++;
			$columna= $initial;
			$celda= 1;
		}
		$objPHPExcel->getActiveSheet()->setTitle('85grados');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$nombre= md5("reporte".date('Y-m-d G:i:s'));
		$objWriter->save($main_path.'/files/'.$nombre.'.xls');
		response(array('code'=>200,"url"=>"http://detectaauditoria.pe/files/".$nombre.".xls"));
	} catch (PDOException $e) {
		response($e->getMessage());
	}
});

/*INCIDENTES*/

$app->get("/incidentes", function() use($app){

	$oIncidente= new Incidente();

	$response= $oIncidente->getIncidenteAll();
	response($response);
});


$app->get("/incidencias/:id_elemento", function($id_elemento) use($app){

	$oIncidente= new Incidente();

	$response= $oIncidente->getIncidenteByElemento($id_elemento);
	response($response);
});

//Registrar Incidencias
$app->post("/incidente", function() use($app){
	try {

		$oIncidente= new Incidente();

		$todo= $app->request->getBody();
		$todo= json_decode($todo);

		foreach ( $todo as $incidencia ) {
			$id_elemento = $incidencia->id_elemento;
			$id_incidente = $incidencia->tipo->id_incidente;
			if (isset($incidencia->detalle)) {
				$detail = $incidencia->detalle;
			}else {
				$detail = "";
			}
			$id_incidente_reporte= $oIncidente->setIncidenteByElemento($id_elemento, $id_incidente, $detail);
		}

		response($id_incidente_reporte);

	} catch (Exception $e) {
		response($e->getMessage());
	}
});

$app->get("/incidencias_supervisar", function() use($app){
	$supervisa= 0;
	$order = "li.id_elemento DESC";
	if(isset($_GET["supervisa"])){
		$supervisa= $_GET["supervisa"];
	}
	$oIncidente= new Incidente();
	$oZona= new Zona();

	$periodo= $oZona->getPeriodoActual();
	$id_periodo = $periodo->id_periodo;
	$response= $oIncidente->getAvailableIncidentesFromPeriodoSupervisar($id_periodo, $order , $supervisa);

	response($response);
});

$app->put("/incidente/aprobar",function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);

	$incidente_reporte_id = $todo->id_incidente_reporte;
	$supervisor_state = 1;
	$detail = "OK";

	$oIncidente= new Incidente();

	if(isset($todo->auditor)){
		$oIncidente->actualizarAuditoria($incidente_reporte_id, $supervisor_state,$detail);
	}else{
		$oIncidente->actualizarSupervision($incidente_reporte_id, $supervisor_state,$detail);
	}
	$response= array('code'=>200);
	response($response);
});

$app->put("/incidente/rechazar", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);

	$incidente_reporte_id = $todo->id_incidente_reporte;
	$observaciones= $todo->observaciones;
	$supervisor_state = 2;


	$oIncidente= new Incidente();

	if(isset($todo->auditor)){
		$oIncidente->actualizarAuditoria($incidente_reporte_id, $supervisor_state, $observaciones);
	}else{
		$oIncidente->actualizarSupervision($incidente_reporte_id, $supervisor_state, $observaciones);
	}
	$response= array('code'=>200);
	response($response);
});

$app->put("/incidencias_elemento/aprobar",function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);

	$id_elemento = $todo->id_elemento;
	$state = 2; // Aprobado

	$oIncidente= new Incidente();

	$oIncidente->actualizarAprobacionElementoxIncidente($id_elemento, $state);

	$response= array('code'=>200);
	response($response);
});

$app->put("/incidencias_elemento/rechazar", function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);

	$id_elemento = $todo->id_elemento;
	$state = 3; //rechazado

	$oIncidente= new Incidente();

	$oIncidente->actualizarAprobacionElementoxIncidente($id_elemento, $state);

	$response= array('code'=>200);
	response($response);
});

$app->put("/incidencias_elemento/actualizar",function() use($app){
	$todo= $app->request->getBody();
	$todo= json_decode($todo);

	$oIncidente= new Incidente();

	foreach ( $todo as $incidencia ) {
		$id_incidente_reporte = $incidencia->id_incidente_reporte;
		$id_incidente = $incidencia->id_incidente;
		$detail = $incidencia->detail;
		$oIncidente->updateIncidenteByElemento($id_incidente_reporte, $id_incidente, $detail);
	}

	$response= array('code'=>200);
	response($response);

});

$app->get("/entel/incidencias/periodo/:id_periodo", function($id_periodo) use($app){

	$oIncidente= new Incidente();
	$response= $oIncidente->getIncidenciasCliente($id_periodo);

	response($response);
});




?>
