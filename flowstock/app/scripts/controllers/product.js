'use strict';

/**
 * @ngdoc function
 * @name flowstockApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the flowstockApp
 */
angular.module('flowstockApp')
  .controller('ProductCtrl', function ($scope, sProductos) {

    // this.awesomeThings = [
    //   'HTML5 Boilerplate',
    //   'AngularJS',
    //   'Karma'
    // ];

    $scope.submitting = false;
    $scope.prodDetail = true;
    $scope.prodBtn = true;
    $scope.nuevoProducto= {};
    $scope.nuevoProducto.linea = [];
    $scope.nuevoProducto.familia = [];
    $scope.nuevoProducto.gender = [];
    $scope.nuevoProducto.um = [];
    $scope.nuevoProducto.description = [];
    $scope.nuevoProducto.brand = [];

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
          $scope.idProduct = res;

          sProductos.listModel().then(function(response){
            $scope.modelAll = response;
          });
        // $state.go("product");
          // document.getElementById('nProduct').reset();
          $("#addProduct").modal('hide');
        }
      });
      // alert($scope.nuevoProducto);
      console.log($scope.nuevoProducto);
    };
    // function saveProduct()

    $scope.init();

    // sElementos.listarIncidencias().then(function(response){
    //   $scope.incidenciasAll = response;
    // });

  });
