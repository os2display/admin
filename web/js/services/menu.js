ikApp.factory('menuFactory', function() {
  var factory = {};
  var menu = [
    {name: 'channels', link: 'channels', title: 'Channels'},
    {name: 'slides', link: 'slides', title: 'Slides'},
    {name: 'screens', link: 'screens', title: 'Screens'},
    {name: 'templates', link: 'templates', title: 'Templates'}
  ];

  factory.getMenuItems = function() {
    return menu;
  }

  return factory;
});