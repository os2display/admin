/**
 * @file
 * Contains the reverseEvents filter.
 */

/**
 * Add a reverse filter to eventlist.
 */
angular.module('ikApp').filter('reverseEvents', function () {
  'use strict';

  return function (items) {
    if (!angular.isArray(items)) {
      return false
    }

    // Turn the list upside down.
    return items.slice().reverse();
  };
});
