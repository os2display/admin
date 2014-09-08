var LoginPage = (function () {

  var timeout = 5 * 1000;

  /* We are testing the login page so the Angular App isn't loaded yet.
   Protractor requires an Angular App to present so we have to access the webdriver
   directly through browser.driver
   See: https://github.com/angular/protractor/issues/51
   */

  function LoginPage() {
    browser.driver.get('http://indholdskanalen.vm/login');
    this.userField = browser.driver.findElement(by.css('#username'));
    this.passwordField = browser.driver.findElement(by.css('#password'));
    this.loginButton = browser.driver.findElement(by.css('#_submit'));
  }


  LoginPage.prototype.logout = function () {
    browser.driver.get('http://indholdskanalen.vm/logout');
  };

  LoginPage.prototype.getTitle = function () {
    return browser.driver.getTitle();
  };

  LoginPage.prototype.getLoginAlert = function () {
    return browser.driver.findElement(by.css('.login--alert')).getText();
  };

  LoginPage.prototype.fillUser = function (username) {
    this.userField.sendKeys(username);
  };

  LoginPage.prototype.fillPassword = function (password) {
    if (password == null) {
      password = "password";
    }
    this.passwordField.sendKeys(password);
  };

  LoginPage.prototype.login = function () {
    this.loginButton.click();
  };

  return LoginPage;

})();

module.exports = LoginPage;