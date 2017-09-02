(function(){

  'use strict';

  angular.module('flowstockApp')
    .directive('mainMenu', function() {
      return {
        restrict: 'E',
        templateUrl: 'views/main-menu.html'
      };
    });
}());
