/**
 * Screen service.
 */
ikApp.factory('screenFactory', function() {
    var factory = {};
    var screens = [
        {
            id: 1,
            title: 'Forhal',
            orientation: 'tall',
            width: "1920px",
            height: "1080px"
        },
        {
            id: 2,
            title: 'Kælder',
            orientation: 'wide',
            width: "1080px",
            height: "1920px"
        }
    ];
    var next_id = 3;

    /**
     * Internal function to get next id.
     * @returns id
     */
    function getNextID() {
        var i  = next_id;
        next_id = i + 1;

        return i;
    }


    /**
     * Get all screens.
     * @returns {Array}
     */
    factory.getScreens = function() {
        return screens;
    }


    /**
     * Find the screen with @id
     * @param id
     * @returns screen or null
     */
    factory.getScreen = function(id) {
        var arr = [];
        angular.forEach(screens, function(value, key) {
            if (value['id'] == id) {
                arr.push(value);
            }
        })

        if (arr.length === 0) {
            return null;
        } else {
            return arr[0];
        }
    }


    /**
     * Returns an empty screen.
     * @returns screen (empty)
     */
    factory.emptySlide = function() {
        return {
            id: null,
            title: '',
            orientation: '',
            width: '',
            height: ''
        };
    }


    /**
     * Saves screen to screens. Assigns an id, if it is not set.
     * @param screen
     * @returns screen
     */
    factory.saveScreen = function(screen) {
        if (screen.id === null) {
            screen.id = getNextID();
            screen.push(screen);
        } else {
            var s = factory.getSlide(screen.id);

            if (s === null) {
                screen.id = getNextID();
                screens.push(screen);
            } else {
                s = screen;
            }
        }
        return screen;
    }

    return factory;
});

