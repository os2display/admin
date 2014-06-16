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
            title: 'Billede af en vej',
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
            id: 4,
            src: '/images/outlines/slide-example-1.png',
            title: 'En tur i byen',
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

