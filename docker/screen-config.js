/**
 * Sets up the config for the app.
 */
window.config = {
  // Used to activate screen (rest API) and load resources.
  "resource": {
    "server": "//screen.kdb-os2display.docker/",
    "uri": 'proxy'
  },
  // Used by web-socket.
  "ws": {
    "server": "//screen.kdb-os2display.docker/"
  },
  // API key to use.
  "apikey": "059d9d9c50e0c45b529407b183b6a02f",
  // If cookie is secure it's only send over https.
  "cookie": {
    "secure": false
  },
  // Display debug messages.
  "debug": false,
  // Software version.
  "version": "test",
  // itkLog configuration.
  "itkLog": {
    "version": "1",
    "errorCallback": null,
    "logToConsole": true,
    "logLevel": "all"
  },
  // Fallback image url, (null = default), else set to "assets/images/fallback_override.png" and add the image
  fallback_image: null
};
