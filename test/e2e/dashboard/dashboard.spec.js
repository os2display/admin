/**
 * Created by turegjorup on 11/08/14.
 */

describe('The Dashboard', function() {

  var dashboard;

  var dashboardPO = require('./dashboard.po.js');

  beforeEach(function() {
    dashboard = new dashboardPO();
  });

  afterEach(function() {
    dashboard.logout();
  });

//  it('Should have correct title', function() {
//    expect(dashboard.title).toBe("Indholdskanalen");
//  });


  describe('Navigation', function() {

    it('Should have hidden nav overlay', function() {
      expect(dashboard.getOverlay().isDisplayed()).toBeFalsy();
    });

    it('Should have hidden menu', function() {
      expect(dashboard.getMenu().isDisplayed()).toBeFalsy();
    });

    it('Should show nav overlay on click', function() {
      dashboard.menubutton.click();
      expect(dashboard.getOverlay().isDisplayed()).toBeTruthy();
    });

    it('Should open menu on menu icon click', function() {
      dashboard.menubutton.click();
      expect(dashboard.getMenu().isDisplayed()).toBeTruthy();
    });

    it('Should close menu on nav overlay click', function() {
      dashboard.menubutton.click();

      browser.actions().mouseMove({x: 50, y: 50}).click().perform();
      expect(dashboard.getMenu().isDisplayed()).toBeFalsy();
    });

  });



});
