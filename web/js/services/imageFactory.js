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
