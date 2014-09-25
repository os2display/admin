/**
 * @file
 * Contains the reverseEvents filter.
 */

/**
 * Add a reverse filter to eventlist.
 */
ikApp.filter('reverseEvents', function() {
  return function(items) {
    if (!angular.isArray(items)){
      return false
    }
    return items.slice().reverse();
  };
});
