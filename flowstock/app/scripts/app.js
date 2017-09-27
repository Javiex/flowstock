(function() {
  'use strict';

/**
 * @ngdoc overview
 * @name flowstockApp
 * @description
 * # flowstockApp
 *
 * Main module of the application.
 */
angular
  .module('flowstockApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'ui.router',
    'jcs-autoValidate',
    'angular-ladda',
    'ui.select'
  ]);
}());
