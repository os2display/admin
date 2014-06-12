ikApp.controller('IndexController', function($scope) {});
ikApp.controller('ChannelsController', function($scope) {});
ikApp.controller('ScreensController', function($scope) {});
ikApp.controller('TemplatesController', function($scope) {});


ikApp.controller('SlidesController', function($scope, slideFactory) {
    $scope.slides = slideFactory.getSlides();
});

