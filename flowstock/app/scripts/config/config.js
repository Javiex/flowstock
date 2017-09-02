(function(){

  'use strict';

  angular.module('flowstockApp')
    .constant('config', {

      /**
       * api location
       * @param {URL} 'http://comercialpaty.pe/api'
       */
       API_ROOT : 'http://localhost',
      // API_ROOT : 'http://comercialpaty.pe',
      API_NAME : '/api',


      /**
       * active the html5mode removing the hash if true
       * @param false - if development
       * @param true - if production
       * @type {Boolean}
       */
      DEVELOP : true

    });

}());
