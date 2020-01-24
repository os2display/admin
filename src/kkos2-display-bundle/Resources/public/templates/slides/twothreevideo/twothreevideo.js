// Register the function, if it does not already exist.
if (!window.slideFunctions["twothreevideo"]) {

   window.slideFunctions["twothreevideo"] = {
     /**
      * Setup the slide for rendering.
      * @param scope
      *   The slide scope.
      */
     setup: scope => {
       const slide = scope.ikSlide;
       let subslides = [];
       let num_subslides = 0;
       if (slide.external_data && slide.external_data.sis_data_slides) {
         subslides = slide.external_data.sis_data_slides;
         num_subslides = slide.external_data.sis_data_num_slides;
       }

       scope.ikSlide.data = {
         currentSlide: 0,
         subslides: subslides,
         num_subslides: num_subslides,
       };

       scope.ratio = window.kkSlideRatio.getRatio();
     },

    run: (slide, region) => {
      // Experience has shown that we can't be certain that all our data is
      // present, so we'll have to be careful verify presence before accessing
      // anything.
      if (!slide.options || !slide.data || !slide.data.subslides) {
        // We're missing data, either it was not there in the first place, or
        // we're in the process of switching to a new revision of the slide where
        // setup has not yet been invoked and our slide.data property is not
        // yet in place.
        // In either case, wait a sec, and then skip to next slide. When we get
        // back to the current slide (may happen right away) setup has hopefully
        // been invoked.
        region.$timeout(function () {
          region.itkLog.info("Missing sis data, delaying and skipping to buy time the framework to run setup ...");
          region.nextSlide();
        }, 1000);
      }
      if (slide.data.num_subslides === 0) {
        region.$timeout(function () {
          // We're empty, advance to the next (real) slide right away.
          console.log("no subslides, continuing straight away");
          region.nextSlide();
        }, 1000);
      }

      // OK, the slide has data and subslides. Now show them.
      console.log('Slide has ' + slide.data.num_subslides + ' subslides');
      (async () => {
        slide.data.currentSlide = 0;
        // Loop and wait for subslides to play.
        for (let subslideIndex = 0; subslideIndex < slide.data.num_subslides; subslideIndex++) {
          let twoThree = new TwoThree();
          await twoThree.play(`.twothreevideo-id-${slide.uniqueId} .twothreevideo-iframe-${subslideIndex}`);

          await region.$timeout(function () {
            slide.data.currentSlide++;
          }, 500);
        }

        // Once we are done waiting for the looping subslides, go to next slide.
        region.$timeout(function () {
          region.nextSlide();
        }, 200);
      })();
    }

   };

  const TwoThree = function () {
    var $this = this;
    $this.glueFrame = null;

    $this.getReadyPlayer = function (selector) {
      return new Promise(function (resolve, reject) {
        const iframe = document.querySelector(selector);
        if (!iframe) {
          reject(`No iframe found with class "${selector}"`);
        }
        $this.glueFrame = new GlueFrame(
          iframe,
          "Player"
        );
        let waiter = 0;
        let interval = setInterval(() => {
          if (waiter++ >= 5) {
            if ($this.glueFrame.ready) {
              resolve();
            } else {
              reject("Timed out waiting for player ready");
            }
            clearInterval(interval);
          }
        }, 800);
      });
    };

    $this.getPlayerProperty = function (name) {
      return new Promise(resolve => {
        $this.glueFrame.get(name, value => {
          resolve(value);
        });
      });
    };

   $this.wait = function (ms) {
      return new Promise(resolve => {
        setTimeout(resolve, ms);
      });
    };

    $this.startPlaying = function () {
      return new Promise(resolve => {
        $this.glueFrame.set("playing", true);
        $this.wait(3000)
          .then(() => {
            $this.getPlayerProperty("video_title")
              .then(value => {
                console.log(`Started player for ${value}`);
              })
          })
          .then(() => {
            resolve()
          })
      });
    };

    $this.monitor = function () {
      return new Promise(function (resolve, reject) {
        let currentTime = 0;
        let loopsWithNoProgress = 0;
        let interval = setInterval(function () {
          // This is quite a lot of chained promises. We could probably do this
          // nicer with async/await, but that will be some other time. The idea
          // here is to check a bunch of properties on the player to see if it
          // is still somewhat alive. If it has stalled for more than 10 secs
          // we kill it and return so the show can go on without the sick
          // player.
          $this.getPlayerProperty("error")
            .then(value => {
              if (value && value !== "") {
                throw value;
              }
            })
            .then(() => {
              return $this.getPlayerProperty("currentTime")
                .then(value => {
                  if (value !== undefined && value === currentTime) {
                    loopsWithNoProgress++;
                  } else {
                    loopsWithNoProgress = 0;
                  }
                  if (loopsWithNoProgress > 5) {
                    throw `Player did not progress after seconds '${currentTime}'`;
                  }
                  currentTime = value;
                  console.log('@ ' + currentTime);
                });
            })
            .then(() => {
              return $this.getPlayerProperty("ended").then(value => {
                if (value !== undefined && value === true) {
                  clearInterval(interval);
                  console.log("Player finished");
                  resolve();
                }
              });
            })
            .catch(err => {
              $this.glueFrame.set("playing", false);
              console.log("Error playing video. Stopping.");
              clearInterval(interval);
              reject(err);
            });
        }, 2000);
      });
    };

    $this.play = function (selector) {
      return $this.getReadyPlayer(selector)
        .then(() => {
          return $this.startPlaying()
        })
        .then(() => {
          return $this.monitor()
        })
        .catch(err => {
          console.log(err);
        })
        .then(() => {
          $this.glueFrame.destroy();
        })
    };

    /*
   *  Don't touch the code below here. It's just pasted in from:
   * https://github.com/23/GlueFrame
   *
   * In the future this should be included in a nicer way.
   */
    var GlueFrame = function (iframe, appName) {

      var $this = this;

      // GlueFrame version
      $this.glueframe = "1.1.3";

      // Allow posting messages only to the domain of the app
      var _domain = ("" + iframe.src).split("/").slice(0, 3).join("/");

      // Determine method of communication with iframe
      var _method = (function () {
        if (_domain == ("" + window.location).split("/").slice(0, 3).join("/")) {
          return "object";
        } else if (typeof window.postMessage !== "undefined") {
          return "post";
        } else {
          return "none";
        }
      })();

      // Poll the iframe until the app is bootstrapped
      $this.ready = false;
      var _readyInterval = window.setInterval(function () {
        if (!this.ready && _method === "object") {
          if (iframe.contentWindow[appName] && iframe.contentWindow[appName].bootstrapped) {
            $this.ready = true;
            window.clearInterval(_readyInterval);
            _processQueue();
          }
        } else if (!this.ready && _method === "post") {
          $this.get("bootstrapped", function (bootstrapped) {
            if (bootstrapped) {
              $this.ready = true;
              window.clearInterval(_readyInterval);
              _processQueue();
            }
          }, true);
        }
      }, 100);

      $this.glueFrameId = Math.floor((new Date()).getTime() * Math.random());
      var _callbackCount = 0;
      var _callbacks = {};

      // Store callback functions in the parent window
      var _registerCallback = function (callback, requireCallback) {
        var callbackIdentifier = $this.glueFrameId + "_" + (++_callbackCount);
        if (typeof callback === "function") {
          _callbacks[callbackIdentifier] = callback;
        } else if (requireCallback) {
          throw "GlueFrame: Callback not registered correctly.";
        }
        return callbackIdentifier;
      };

      // Queue up method calls until app is ready
      var _queue = [];
      var _addToQueue = function (method, args) {
        _queue.push({method: method, args: args});
      };

      // Loop through queue when app is ready
      var _processQueue = function () {
        for (var i = 0; i < _queue.length; i += 1) {
          var queueItem = _queue[i];
          queueItem.method.apply(null, queueItem.args);
        }
        _queue = [];
        $this.set("queuedEventsProcessed", true);
      };

      $this.get = function (prop, callback, force) {
        if (!$this.ready && !force) {
          _addToQueue($this.get, [prop, callback]);
          return;
        }
        var cbId = _registerCallback(callback, true);
        if (_method === "object") {
          var value = iframe.contentWindow[appName].get.apply(null, [prop]);
          if (typeof _callbacks[cbId] !== "undefined") {
            _callbacks[cbId].apply(null, [value]);
          }
        } else if (_method === "post") {
          var messageObject = {f: "get", args: [prop], cbId: cbId};
          iframe.contentWindow.postMessage(JSON.stringify(messageObject), force ? "*" : _domain);
        }
      };

      $this.set = function (prop, val, callback) {
        if (!$this.ready) {
          _addToQueue($this.set, [prop, val, callback]);
          return;
        }
        var cbId = _registerCallback(callback, false);
        if (_method === "object") {
          var value = iframe.contentWindow[appName].set.apply(null, [prop, val]);
          if (typeof _callbacks[cbId] !== "undefined") {
            _callbacks[cbId].apply(null, [value]);
          }
        } else if (_method === "post") {
          var messageObject = {f: "set", args: [prop, val], cbId: cbId};
          iframe.contentWindow.postMessage(JSON.stringify(messageObject), _domain);
        }
      };

      $this.bind = function (event, callback, triggerQueue) {
        var triggerQueue = triggerQueue || false;
        if (!$this.ready) {
          _addToQueue($this.bind, [event, callback, true]);
          return;
        }
        var cbId = _registerCallback(callback, true);
        if (_method === "object") {
          iframe.contentWindow[appName].bind.apply(null, [event, callback, triggerQueue]);
        } else if (_method === "post") {
          var messageObject = {f: "bind", args: [event], cbId: cbId, triggerQueue: triggerQueue};
          iframe.contentWindow.postMessage(JSON.stringify(messageObject), _domain);
        }
      };

      $this.fire = function (event, obj) {
        if (!$this.ready) {
          _addToQueue($this.fire, [event, obj]);
          return;
        }
        if (_method === "object") {
          return iframe.contentWindow[appName].fire.apply(null, [event, obj]);
        } else if (_method === "post") {
          var messageObject = {f: "fire", args: [event, obj]};
          iframe.contentWindow.postMessage(JSON.stringify(messageObject), _domain);
        }
      };

      // Remove event listeners, callbacks and intervals
      $this.destroy = function () {
        if (window.addEventListener) {
          window.removeEventListener("message", _receiveMessage, false);
        } else {
          window.detachEvent("onmessage", _receiveMessage);
        }
        window.clearInterval(_readyInterval);
        _callbacks = {};
      };

      // Parse messages received from iframe
      var _receiveMessage = function (e) {
        if (e.origin === _domain) {
          var data;
          try {
            data = JSON.parse(e.data);
          } catch (e) {
          }
          if (typeof data !== "undefined" && typeof data.cbId !== "undefined" && typeof _callbacks[data.cbId] === "function") {
            _callbacks[data.cbId].apply(null, [data.a, data.b]);
          }
        }
      };

      // Listen for message events if need
      if (window.addEventListener) {
        window.addEventListener("message", _receiveMessage, false);
      } else {
        window.attachEvent("onmessage", _receiveMessage);
      }

    };
    if (typeof (window.onGlueFrameAvailable) == 'function') window.onGlueFrameAvailable(GlueFrame);

    if (typeof module !== 'undefined' && module.exports) {
      module.exports = GlueFrame;
    }
  };


}
