ikApp.controller('MenuController', function($scope) {
  $scope.menu = [
    {name: 'channels', link: 'channels', title: 'Channels'},
    {name: 'slides', link: 'slides', title: 'Slides'},
    {name: 'screens', link: 'screens', title: 'Screens'},
    {name: 'templates', link: 'templates', title: 'Templates'}
  ];
});
