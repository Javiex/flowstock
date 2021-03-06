(function(){
  'use strict';

  angular.module('flowstockApp')
    .service('sApi', function (
      config
    ) {

      this.api_url = function(){
        return config.API_ROOT + config.API_NAME;
      };

      this.listProduct = function(){
        return this.api_url() + '/products';
      };

      this.addProduct = function(){
        return this.api_url() + '/product';
      };

      this.addProductDetail = function(){
        return this.api_url() + '/productDetail';
      };

      this.listLine = function(){
        return this.api_url() + '/lines';
      };

      this.listFamily = function(){
        return this.api_url() + '/families';
      };

      this.listGender = function() {
        return this.api_url() + '/genders';
      };

      this.listUnit = function() {
        return this.api_url() + '/units';
      };

      this.listBrand = function() {
        return this.api_url() + '/brands';
      };

      this.listModel = function() {
        return this.api_url() + '/models';
      };

      this.listBabySize = function() {
        return this.api_url() + '/Babysizes';
      };

      this.listChildSize = function() {
        return this.api_url() + '/Childsizes';
      };

      this.listColor = function() {
        return this.api_url() + '/colors';
      };

      this.addColor = function() {
        return this.api_url() + '/color';
      };

      this.addLine = function() {
        return this.api_url() + '/line';
      };

    });
}());
