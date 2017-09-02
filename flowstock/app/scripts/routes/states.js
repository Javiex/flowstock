(function(){

  'use strict';

  angular.module('flowstockApp')
    .config(function (
      $stateProvider,
      $urlRouterProvider
    ) {
      $urlRouterProvider.otherwise('/');
      $stateProvider
      .state('init',{
        url: '/',
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .state('about', {
        url: '/about',
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl',
        controllerAs: 'about'
      })
      .state('warehouse', {
        url: '/warehouse',
        templateUrl: 'views/warehouse.html'
      })
      .state('product', {
        url:'/product',
        templateUrl: 'views/productList.html',
        controller: 'ProductCtrl',
        controllerAs: 'product'
      });
  })
}());
