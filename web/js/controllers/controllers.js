ikApp.controller('IndexController', function($scope) {});
ikApp.controller('ScreensController', function($scope) {});
ikApp.controller('TemplatesController', function($scope) {});


ikApp.controller('SlidesController', function($scope, slideFactory) {
    $scope.slides = slideFactory.getSlides();
});

ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = channelFactory.getChannels();
});