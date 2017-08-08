angular.module('templateModule').directive('manualCalendarEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    link: function (scope) {
      function resetInputEvent() {
        scope.newEvent = {
          "title": null,
          "place": null,
          "from": null,
          "to": null
        };
      }
      resetInputEvent();

      /**
       * Add event to slide
       */
      scope.newEventItem = function newEventItem() {
        // Add event data to slide array.
        scope.slide.options.eventitems.push(angular.copy(scope.newEvent));

        resetInputEvent();
      };

      /**
       * Remove event from slide.
       */
      scope.removeEventItem = function removeEventItem(event) {
        scope.slide.options.eventitems.splice(scope.slide.options.eventitems.indexOf(event), 1);
      };

      /**
       * Sort events for slide.
       */
      scope.sortEvents = function sortEvents() {
        if (scope.slide.options.eventitems.length > 0) {
          // Sort the events by from date.
          scope.slide.options.eventitems = $filter('orderBy')(scope.slide.options.eventitems, "from")
        }
      };

      /**
       * Is outdated for events on slide
       */
      scope.eventIsOutdated = function setOutdated(event) {
        var to = event.to;
        var from = event.from;
        var now = Date.now() / 1000;

        return (to && now > to) || (!to && now > from);
      };

      // Run sorting of events.
      scope.sortEvents();
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/templateModule/manual-calendar-editor.html'
  };
});
