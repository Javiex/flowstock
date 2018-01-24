(function(){

  'use strict';

  angular.module('flowstockApp')
  .factory('sProductos',function(
    $http,
    sApi
  ) {

    function Product() {}
    Product.listProduct = function(){
      var url = sApi.listProduct();
      var p = $http.get(url);
      p = p.then(function (res) {
        return res.data;
      });
      return p;
    };

    Product.listLine = function(){
      var url = sApi.listLine();
      var p = $http.get(url);
      p = p.then(function (res) {
        return res.data;
      });
      return p;
    };

    Product.listModel = function(){
      var url = sApi.listModel();
      var p = $http.get(url);
      p = p.then(function (res) {
        return res.data;
      });
      return p;
    };

    Product.listFamily = function() {
      var url = sApi.listFamily();
      var p = $http.get(url);
      p = p.then(function (res) {
        return res.data;
      });
      return p;
    };

    Product.listGender = function() {
      var url = sApi.listGender();
      var p = $http.get(url);
      p = p.then(function (res) {
        return res.data;
      });
      return p;
    };

    Product.listUnit = function() {
      var url = sApi.listUnit();
      var p = $http.get(url);
      p = p.then(function(res){
        return res.data;
      });
      return p;
    };

    Product.listBrand = function() {
      var url = sApi.listBrand();
      var p = $http.get(url);
      p = p.then(function(res){
        return res.data;
      });
      return p;
    };

    Product.addProduct = function(params){
      var url = sApi.addProduct();
      return $http.post( url , params ).then(function(res){
        return res.data;
      }, function(res){
        return {error:res.status};
      });
    };

    Product.addProductDetail = function(params){
      var url = sApi.addProductDetail();
      return $http.post(url,params).then(function(res){
        return res.data;
      },function(res){
        return {error:res.status};
      });
    };

    Product.listBabySize = function() {
      var url = sApi.listBabySize();
      var p = $http.get(url);
      p = p.then(function(res){
        return res.data;
      });
      return p;
    };

    Product.listChildSize = function() {
      var url = sApi.listChildSize();
      var p = $http.get(url);
      p = p.then(function(res){
        return res.data;
      });
      return p;
    };

    Product.listColor = function() {
      var url = sApi.listColor();
      var p = $http.get(url);
      p = p.then(function(res){
        return res.data;
      });
      return p;
    };

    Product.addColor = function(params){
      var url = sApi.addColor();
      return $http.post( url, params ).then(function(res){
        return res.data;
      }, function(res){
        return {error:res.status};
      });
    };

    return Product;
  });
}());
