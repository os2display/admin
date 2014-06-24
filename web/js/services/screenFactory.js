/**
 * Screen service.
 */
ikApp.factory('screenFactory', function() {
    var factory = {};
    var groups = [
        {
            id: 1,
            title: 'Aula',
            screens: [2,3]
        },
        {
            id: 2,
            title: 'Voksenafdelingen',
            screens: [1,2]
        }
    ];
    var screens = [
        {
            id: 1,
            title: 'Forhal',
            orientation: 'tall',
            width: '1920',
            height: '1080'
        },
        {
            id: 2,
            title: 'Kælder',
            orientation: 'wide',
            width: '1080',
            height: '1920'
        },
        {
            id: 3,
            title: 'Børneafdelingen',
            orientation: 'wide',
            width: '1080',
            height: '1920'
        }
    ];
    var next_id = 4;
    var next_group_id = 3;

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
     * Internal function to get next group id.
     * @returns id
     */
    function getNextGroupID() {
        var i  = next_group_id;
        next_group_id = i + 1;

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
     * Get all groups.
     * @returns {Array}
     */
    factory.getGroups = function() {
        return groups;
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
     * Find the group with @id
     * @param id
     * @returns group or null
     */
    factory.getGroup = function(id) {
        var arr = [];
        angular.forEach(groups, function(value, key) {
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
     * Find the groups screen with @id is part of
     * @param id
     * @returns group or null
     */
    factory.getScreenGroups = function(id) {
        var id = parseInt(id);
        var arr = [];
        angular.forEach(groups, function(value, key) {
            if (value.screens.indexOf(id) != -1) {
                arr.push(value);
            }
        })

        if (arr.length === 0) {
            return null;
        } else {
            return arr;
        }
    }


    /**
     * Remove screen from group
     * @param screen_id
     * @param group_id
     */
    factory.removeScreenFromGroup = function(screen_id, group_id) {
        var group = factory.getGroup(group_id);
        var screen_id = parseInt(screen_id);
        var idx = group.screens.indexOf(screen_id);

        if (idx > -1) {
            group.screens.splice(idx, 1);
        }
    }

    /**
     * Add screen to group
     * @param screen_id
     * @param group_id
     */
    factory.addScreenToGroup = function(screen_id, group_id) {
        var group = factory.getGroup(group_id);
        var screen_id = parseInt(screen_id);
        var idx = group.screens.indexOf(screen_id);

        if (idx == -1) {
            group.screens.push(screen_id);

        }
    }


    /**
     * Returns an empty screen.
     * @returns screen (empty)
     */
    factory.emptyScreen = function() {
        return {
            id: null,
            title: '',
            orientation: '',
            width: '',
            height: ''
        };
    }


    /**
     * Returns an empty group.
     * @returns group (empty)
     */
    factory.emptyGroup = function() {
        return {
            id: null,
            title: ''
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
            var s = factory.getScreen(screen.id);

            if (s === null) {
                screen.id = getNextID();
                screens.push(screen);
            } else {
                s = screen;
            }
        }
        return screen;
    }

    /**
     * Saves the groups a screen i spart of
     * @param group
     * @returns groups
     */

    //factory.saveScreenToGroups( {

    //}

    /**
     * Saves group to groups. Assigns an id, if it is not set.
     * @param group
     * @returns group
     */
    factory.saveGroup = function(group) {
        if (group.id === null) {
            group.id = getNextID();
            group.push(group);
        } else {
            var g = factory.getScreen(group.id);

            if (s === null) {
                group.id = getNextID();
                groups.push(group);
            } else {
                g = group;
            }
        }
        return group;
    }

    return factory;
});

