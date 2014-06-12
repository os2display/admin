var ikApp = angular.module('ikApp', ['ngRoute', 'ngAnimate'], function($interpolateProvider) {
    $interpolateProvider.startSymbol('<[');
    $interpolateProvider.endSymbol(']>');
});
