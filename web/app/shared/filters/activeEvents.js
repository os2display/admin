/**
 * @file
 * Contains the activeEvents filter.
 * A filter to display only events that have not yet bypassed their end date.
 */

/**
 * Add an active events filter to eventlist.
 */
angular.module('ikApp').filter('activeEvents', function () {
  'use strict';

  return function (items) {
    // Return if event array empty.
    if (!angular.isArray(items)) {
      return false
    }

    // Get current time.
    var currentTime = parseInt(Date.now() / 1000);

    var ret = [];

    // Loop through event items.
    for (var i = 0; i < items.length; i++) {
      var item = items[i];

      // Calculate event duration.
      if (item.from && item.to && item.to >= currentTime) {
        ret.push(item);
      }
      else if (item.from && item.from >= currentTime) {
        ret.push(item);
      }
    }

    return ret;
  };
});