var Dashboard = (function () {

  var timeout = 5 * 1000;

  var loginPage;
  var loginPagePO = require('../login/login.po.js');


  function Dashboard() {
    loginPage = new loginPagePO();
    loginPage.fillUser('admin');
    loginPage.fillPassword('admin');
    loginPage.login();

    browser.get('http://indholdskanalen.vm/');

    this.title = browser.driver.getTitle();
    this.menubutton = browser.findElement(by.css('.header--nav-menu-link'));
  }

  Dashboard.prototype.logout = function () {
    loginPage.logout();
  };

  Dashboard.prototype.getMenu = function () {
    return browser.findElement(by.css('.nav'));
  };

  Dashboard.prototype.getOverlay = function () {
    return browser.findElement(by.css('.nav--overlay'));
  };


  return Dashboard;

})();

module.exports = Dashboard;