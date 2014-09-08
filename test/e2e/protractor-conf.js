exports.config = {
  allScriptsTimeout: 11000,

  specs: [
    '*/*.spec.js'
  ],

  capabilities: {
    'browserName': 'chrome'
  },

  baseUrl: 'http://indholdskanalen.vm/',

  framework: 'jasmine',

  jasmineNodeOpts: {
    defaultTimeoutInterval: 30000
  }
};
