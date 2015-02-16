/**
 * @file
 * Configuration file to control the Angular application.
 */
angular.module('ikApp').value('configuration', {
  "search": {
    "address": 'https://service.indholdskanalen.vm',
    "index": 'e7df7cd2ca07f4f1ab415d457a6e1c13'
  },
  "sharingService": {
    "enabled": false,
    "address": "https://service.indholdskanalen.vm"
  }
});
