ikApp.controller('IndexController', function($scope) {});
ikApp.controller('TemplatesController', function($scope) {});


ikApp.controller('SlidesController', function($scope, slideFactory) {
    $scope.slides = slideFactory.getSlides();
});

ikApp.controller('ScreensController', function($scope, screenFactory) {
    $scope.screens = screenFactory.getScreens();
});

ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = channelFactory.getChannels();
});