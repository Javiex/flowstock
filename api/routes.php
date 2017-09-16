<?php
session_start();

if(!defined('web')) die ('Acceso Denegado');

$app->get("/products", function() use($app){

	$oProductos= new Product();
	$response= $oProductos->getProducts();
	echoResponse($response);
});

$app->post("/product", function() use($app){

	try {
		$oProductos = new Product();

		$todo = $app->request->getBody();
		$todo = json_decode($todo);


		$cod1 = $todo->gender->id_gender;
		$cod2 = $todo->linea->code_line;
		$cod3 = $todo->familia->code_family;


		$line =  $todo->linea->id_line;
		$family = $todo->familia->id_family;
		$gender = $todo->gender->code_gender;
		$unit =  $todo->um->id_unit;
		$description = $todo->description;
		$codeBrand = $todo->brand->code_brand;

		$code = $cod1.$cod2.$cod3;


		$producto_id = $oProductos->setProduct($code, $line, $family, $gender, $unit, $description, $codeBrand);
		echoResponse($producto_id);

		//agregar el codigo del producto;
		$code .= $producto_id;
		$oProductos->updateCodeProduct($code, $producto_id);

	} catch (Exception $e) {
		echoResponse($e->getMessage());
	}
});

/*$app->get("/products", function() use($app){
	$oProductos= new Product();
	$search = '1';
	$response= $oProductos->getProductByElemento($search);
	response($response);
});*/

$app->get("/lines", function() use($app){
	$oLine = new Line();
	$response = $oLine->getLines();
	echoResponse($response);
});

$app->get("/models", function() use($app){
	$oModel = new Model();
	$response = $oModel->getModels();
	echoResponse($response);
});

$app->get("/families", function() use($app){
	$oFamily = new Family();
	$response = $oFamily->getFamilies();
	echoResponse($response);
});

$app->get("/genders", function() use($app){
	$oGender = new Gender();
	$response = $oGender->getGenders();
	echoResponse($response);
});

$app->get("/units", function() use($app){
	$oUnit = new Unit();
	$response = $oUnit->getUnits();
	echoResponse($response);
});

$app->get("/brands", function() use($app){
	$oBrand = new Brand();
	$response = $oBrand->getBrands();
	echoResponse($response);
});

?>
