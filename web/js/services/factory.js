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
