describe('/', function() {
  var ptor = protractor.getInstance();

  // Initial setup. Auto login.
  beforeEach(function() {
    ptor.ignoreSynchronization = true;
    ptor.get('/');
    element(by.css('#username')).sendKeys('admin');
    element(by.css('#password')).sendKeys('admin');
    element(by.css('#_submit')).click();
  });

  it('should display title Indholdskanalen', function() {
    expect(browser.getTitle()).toBe("Indholdskanalen");
  });
});