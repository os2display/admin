ikApp.controller('IndexController', function($scope) {});
ikApp.controller('TemplatesController', function($scope) {});


ikApp.controller('ScreensController', function($scope, screenFactory) {
    $scope.screens = screenFactory.getScreens();
});


ikApp.controller('ChannelsController', function($scope, channelFactory) {
  $scope.channels = channelFactory.getChannels();
});