'use strict';

/**
 * @ngdoc function
 * @name flowstockApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the flowstockApp
 */
angular.module('flowstockApp')
  .controller('ProductCtrl', function ($scope, $state, sProductos) {

    // this.awesomeThings = [
    //   'HTML5 Boilerplate',
    //   'AngularJS',
    //   'Karma'
    // ];

    $scope.submitting = false;
    $scope.prodDetail = false;
    $scope.prodBtn = true;
    $scope.nuevoProducto = {};
    $scope.nuevoProducto.linea = [];
    $scope.nuevoProducto.familia = [];
    $scope.nuevoProducto.gender = [];
    $scope.nuevoProducto.um = [];
    $scope.nuevoProducto.description = [];
    $scope.nuevoProducto.brand = [];

    $scope.detail = {};

    $scope.init = function(){
      sProductos.listProduct().then(function(response){
        $scope.productosAll = response;
      });
      sProductos.listLine().then(function(response){
        $scope.lineAll = response;
      });
      sProductos.listFamily().then(function(response){
        $scope.familyAll = response;
      });
      sProductos.listGender().then(function(response){
        $scope.genderAll = response;
      });
      sProductos.listUnit().then(function(response){
        $scope.unitAll = response;
      });
      sProductos.listBrand().then(function(response){
        $scope.brandAll = response;
      });

    };

    $scope.saveProduct = function(){
      $scope.con = confirm("¿Desea guardar el producto?");
      if ($scope.con == true) {
        $scope.submitting = true;
        $scope.nuevoProducto.linea = $scope.linea;
        $scope.nuevoProducto.familia = $scope.familia;
        $scope.nuevoProducto.gender = $scope.gender;
        $scope.nuevoProducto.um = $scope.um;
        $scope.nuevoProducto.description = $scope.description;
        $scope.nuevoProducto.brand = $scope.brand;

        sProductos.addProduct($scope.nuevoProducto).then(function(res){
          if(res.error){
            $scope.submitting = false;
            $scope.prodDetail = false;
            $scope.prodBtn = true;
          }else {
            $scope.submitting = false;
            $scope.prodDetail = true;
            $scope.prodBtn = false;
            $scope.detail.idProduct = res;

            sProductos.listModel().then(function(response){
              $scope.modelAll = response;
            });
            sProductos.listBabySize().then(function(response){
              $scope.babySizeAll = response;
            });
            sProductos.listChildSize().then(function(response){
              $scope.childSizeAll = response;
            });

            sProductos.listColor().then(function(response){
              $scope.colorAll = response;
            });
          }
        });
        console.log($scope.nuevoProducto);
      }else {
        alert("Cancelo el guardado");
      }
    };

    $scope.saveDetailProduct = function() {
      $scope.con = confirm("¿Desea guardar el detalle del producto?");

      if($scope.con == true){
        $scope.submitting = true;
        sProductos.addProductDetail($scope.detail).then(function(res){
          if (res.error) {
            $scope.submitting = false;
          }else {
            $scope.submitting = false;
            $state.go("product");
          }
        });
        console.log($scope.detail);
      } else {
        alert("No se guardo");
      }
    };

    $scope.init();
  });
