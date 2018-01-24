'use strict';

angular.module('flowstockApp')
  .controller('ColorCtrl', function($scope, $state, sProductos){

  $scope.nuevoColor = {};

    $scope.init = function(){
      sProductos.listColor().then(function(response){
        $scope.colorAll = response;
      });
    };

    $scope.saveColor = function() {
      $scope.conC = confirm("¿Guardar nuevo color?");
      if($scope.conC == true){
        $scope.nuevoColor.name = $scope.nameColor;
        $scope.nuevoColor.code = $scope.codeColor;
        sProductos.addColor($scope.nuevoColor).then(function(res){
          if (res.error) {
            alert("falla");
          }else {
          alert("guardado");
          }
        });
      }else {
        alert("Cancelado el guardado");
      }

    };

    $scope.init();
  });
