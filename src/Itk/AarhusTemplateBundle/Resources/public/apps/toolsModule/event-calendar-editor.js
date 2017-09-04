angular.module('toolsModule')
.directive('eventCalendarEditor', [
  'kobaFactory', 'busService', function (kobaFactory, busService) {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      link: function (scope) {
        // Reset resources.
        scope.availableResources = [];

        // Get resources for the calendar.
        kobaFactory.getResources().then(
          function (data) {
            // Store data in the scope.
            scope.availableResources = data;
            // Filter the current slides options based on the resources
            // available.
            if (scope.slide.options.hasOwnProperty('resources')) {
              var selected = [];
              var len = scope.slide.options.resources.length;
              for (var i = 0; i < len; i++) {
                var found = false;
                for (var j = 0; j < data.length; j++) {
                  if (data[j].mail === scope.slide.options.resources[i].mail) {
                    found = true;
                    break;
                  }
                }

                if (found) {
                  // Item is found, so add it to the list.
                  selected.push(scope.slide.options.resources[i]);
                }
              }
            }

            scope.slide.options.resources = selected;
          },
          function error(reason) {
            busService.$emit('log.error', {
              'cause': reason,
              'msg': 'Kunne ikke hente bookings for ressource.'
            });
          }
        );

        /**
         * Add calendar events from source (for event calendar.)
         */
        scope.addCalendarEvents = function addCalendarEvents() {
          var arr = [];

          // Process bookings for each resource.
          var addResourceBookings = function addResourceBookings(data) {
            for (var i = 0; i < data.length; i++) {
              var event = data[i];
              arr.push(event);
            }
          };

          // Get bookings for each resource.
          for (var i = 0; i < scope.slide.options.resources.length; i++) {
            var resource = scope.slide.options.resources[i];
            var now = new Date();
            var todayStart = (new Date(now.getFullYear(), now.getMonth(), now.getDate())).getTime() / 1000;
            var todayEnd = todayStart + 86400;

            kobaFactory.getBookingsForResource(resource.mail, todayStart, todayEnd)
            .then(
              addResourceBookings,
              function error(reason) {
                busService.$emit('log.error', {
                  'cause': reason,
                  'msg': 'Kunne ikke hente bookings for ressource.'
                });
              }
            );
          }

          scope.slide.external_data = arr;
        };
      },
      templateUrl: '/bundles/itkaarhustemplate/apps/toolsModule/event-calendar-editor.html'
    };
  }
]);
