/**
 * @file
 * Contains the activeEvents filter.
 * A filter to display only events that have not yet bypassed their end date.
 */

/**
 * Add an active events filter to eventlist.
 * @param action: what action to take for items that don't pass.
 */
ikApp.filter('activeEvents', function(action) {
  return function(items) {
    // Return if event array empty.
    if (!angular.isArray(items)){
      return false
    }

    // Loop through event items.
    for (var i = 0; i < items.length; i++) {
      var item = items[i];

      // Set current time.
      var currentTime = Date.now();

      // Calculate event duration.
      var duration = item.to - item.from;

      // If negative duration (end before start time) or end time is exceeded (old event).
      if (duration < 0 || item.to * 1000 < currentTime) {

        // Remove event item from view (Still exists in slide).
        items.splice(i,1);
      }
    }
    return items;
  };
});
