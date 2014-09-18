'use strict';

var LoginPage = require('./login.po.js');

describe('Login page', function () {
  var page;

  beforeEach(function () {
    page = new LoginPage();
  });

  afterEach(function() {
    page.logout();
  });

  it('ensures valid users can log in', function() {
    page.fillUser('admin');
    page.fillPassword('admin');
    page.login();

    expect(page.getTitle()).toBe("Indholdskanalen");
  });

  it('ensures unknown users are rejected', function() {
    page.fillUser('unknown');
    page.fillPassword('user');
    page.login();

    expect(page.getLoginAlert()).toBe("Invalid username or password");
  });

});