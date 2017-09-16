<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

$main_path= $_SERVER['DOCUMENT_ROOT'];
require_once($main_path.'/libs/Slim/Slim.php');

\Slim\Slim::registerAutoloader();

$app= new \Slim\Slim();
define("web", true);
$app->contentType('text/html; charset=utf-8');
require_once($main_path.'/libs/functions.php');
require_once($main_path.'/api/config/config.php');
// require_once($main_path.'/api/model/galeria.php');
// require_once($main_path.'/api/model/usuario.php');
// require_once($main_path.'/api/model/zona.php');
// require_once($main_path.'/api/model/elemento.php');
// require_once($main_path.'/api/model/parametros.php');
// require_once($main_path.'/api/model/periodo.php');
// require_once($main_path.'/api/model/incidente.php');
require_once($main_path.'/api/model/line.php');
require_once($main_path.'/api/model/model.php');
require_once($main_path.'/api/model/family.php');
require_once($main_path.'/api/model/gender.php');
require_once($main_path.'/api/model/unit.php');
require_once($main_path.'/api/model/brand.php');
require_once($main_path.'/api/model/product.php');
require_once($main_path.'/api/routes.php');

if($app->request->isOptions()) {
	$app->response->headers->set('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
	$app->response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept');
}else{
	$app->run();
}
?>
