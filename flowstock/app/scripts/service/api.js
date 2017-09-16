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

    });
}());
