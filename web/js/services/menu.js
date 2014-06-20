ikApp.factory('menuFactory', function() {
  var factory = {};
  var menu = [
    {name: 'channels', link: 'channels', title: 'Channels' },
    {name: 'slides', link: 'slides', title: 'Slides' },
    {name: 'screens', link: 'screens', title: 'Screens' },
    {name: 'templates', link: 'templates', title: 'Templates' }
  ];

  var path;
  factory.setLocation = function(location) {
    path = location.url().substring(1);
  }

  factory.getMenuItems = function() {
    $.each(menu, function (index, value) {
      if (value.link === path) {
        value.active = true;
      }
      else {
        value.active = false;
      }
    });
    return menu;
  }

  return factory;
});
