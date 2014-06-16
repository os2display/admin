/**
 * Slide service.
 */
ikApp.factory('slideFactory', function() {
    var factory = {};
    var slides = [
        {
            id: 1,
            src: '/images/outlines/slide-example-1.png',
            title: 'Lorem ipsum',
            orientation: '',
            template: '',
            options: {
                'bgcolor': '#ccc',
                'textcolor': '#fff',
                'textbgcolor': 'rgba(0, 0, 0, 0.7)',
                'image': '',
                'headline': '',
                'text': ''
            }
        },
        {
            id: 2,
            src: '/images/outlines/slide-example-2.png',
            title: 'Billede af en vej'
            orientation: '',
            template: '',
            options: {
                'bgcolor': '#ccc',
                'textcolor': '#fff',
                'textbgcolor': 'rgba(0, 0, 0, 0.7)',
                'image': '',
                'headline': '',
                'text': ''
            }
        },
        {
            id: 3,
            src: '/images/outlines/slide-example-3.png',
            title: 'Lorem ipsum'
            orientation: '',
            template: '',
            options: {
                'bgcolor': '#ccc',
                'textcolor': '#fff',
                'textbgcolor': 'rgba(0, 0, 0, 0.7)',
                'image': '',
                'headline': '',
                'text': ''
            }
        },
        {
            id: 4,
            src: '/images/outlines/slide-example-1.png',
            title: 'En tur i byen'
            orientation: '',
            template: '',
            options: {
                'bgcolor': '#ccc',
                'textcolor': '#fff',
                'textbgcolor': 'rgba(0, 0, 0, 0.7)',
                'image': '',
                'headline': '',
                'text': ''
            }
        },
        {
            id: 5,
            src: '/images/outlines/slide-example-3.png',
            title: 'Lorem ipsum'
            orientation: '',
            template: '',
            options: {
                'bgcolor': '#ccc',
                'textcolor': '#fff',
                'textbgcolor': 'rgba(0, 0, 0, 0.7)',
                'image': '',
                'headline': '',
                'text': ''
            }
        }
    ];
    var next_id = 6;


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
     * Get all slides.
     * @returns {Array}
     */
    factory.getSlides = function() {
        return slides;
    }


    /**
     * Find the slide with @id
     * @param id
     * @returns slide or null
     */
    factory.getSlide = function(id) {
        var arr = [];
        angular.forEach(slides, function(value, key) {
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
     * Returns an empty slide.
     * @returns slide (empty)
     */
    factory.emptySlide = function() {
        return {
            id: null,
            title: '',
            orientation: '',
            template: '',
            options: {
                'bgcolor': '#ccc',
                'textcolor': '#fff',
                'textbgcolor': 'rgba(0, 0, 0, 0.7)',
                'image': '',
                'headline': '',
                'text': ''
            }
        };
    }


    /**
     * Saves slide to slides. Assigns an id, if it is not set.
     * @param slide
     * @returns slide
     */
    factory.saveSlide = function(slide) {
        if (slide.id === null) {
            slide.id = getNextID();
            slides.push(slide);
        } else {
            var s = factory.getSlide(slide.id);

            if (s === null) {
                slide.id = getNextID();
                slides.push(slide);
            } else {
                s = slide;
            }
        }
        return slide;
    }

    return factory;
});



ikApp.factory('channelFactory', function() {
    var factory = {};
    var channels = [];
    var next_id = 0;

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
     * Get channels.
     * @returns {Array}
     */
    factory.getChannels = function() {
        return channels;
    }


    /**
     * Find the channel with @id
     * @param id
     * @returns channel or null
     */
    factory.getChannel = function(id) {
        var arr = [];
        angular.forEach(channels, function(value, key) {
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
     * Returns an empty channel.
     * @returns channel (empty)
     */
    factory.emptyChannel = function() {
        return {
            id: null,
            title: '',
            orientation: ''
        };
    }


    /**
     * Saves channel to channels. Assigns an id, if it is not set.
     * @param channel
     * @returns channel
     */
    factory.saveChannel = function(channel) {
        if (channel.id === null) {
            channel.id = getNextID();
            channels.push(channel);
        } else {
            var s = factory.getChannel(channel.id);

            if (s === null) {
                channel.id = getNextID();
                channel.push(channel);
            } else {
                s = channel;
            }
        }
        return channel;
    }

    return factory;
});


ikApp.factory('templateFactory', function() {
    var factory = {};
    var templates = [
        {
            id: 1,
            src: '/images/outlines/template-example-1.png'
        },
        {
            id: 2,
            src: '/images/outlines/template-example-2.png'
        },
        {
            id: 3,
            src: '/images/outlines/template-example-3.png'
        },
        {
            id: 4,
            src: '/images/outlines/template-example-4.png'
        },
        {
            id: 5,
            src: '/images/outlines/template-example-5.png'
        }
    ];

    factory.getTemplates = function() {
        return templates;
    }

    return factory;
});

ikApp.factory('imageFactory', function() {
    var factory = {};
    var images = [
        {
            title: 'road',
            url: '/images/outlines/slide-config-default.png'
        }
    ];

    factory.getImages = function() {
        return images;
    }

    return factory;
});
