// Register the function, if it does not already exist.
if (!window.slideFunctions['twentythree']) {
    const generateIframeSrc = function(slide) {
        "use strict";
        var url = `//${slide.options.domain}/v.ihtml/player.html?source=embed&photo%5fid=${slide.options.id}`;
        url += "&autoPlay=0";
        url += "&loop=0";
        url += "&showDescriptions=0";
        url += "&showLogo=0";
        url += "&socialSharing=0";
        url += "&hideBigPlay=1";
        url += "&showTray=0";
        url += "&defaultQuality=fullhd";
        if (slide.options.sound) {
            url += "&autoMute=0";
        } else {
            url += "&autoMute=1&ambient=1";
        }

        return url;
    };

    const detachGlueframe = function(slide) {
        "use strict";
        // Clear out any existing frame.
        console.log("Detaching glueframe from " + slide.uniqueId);
        slide.glueFrame.destroy();
        slide.glueFrame = false;
    };

    const configureGlueframe = function(slide, region) {
        "use strict";
        // Setup event-listeners, see https://github.com/23/GlueFrame/issues/5 for events.

        // Force autoplay in case the player is not ready when to do the
        // set('playing');
        slide.glueFrame.bind("player:video:ready", function(eventName, videoApp) {
            region.itkLog.info('Forcing autoplay');
            videoApp.set('playing', true);
        });

        // Continue to next slide when the player ends.
        slide.glueFrame.bind("player:video:ended", function(eventName, videoApp) {
            region.itkLog.info('Video ended, advancing to next slide');
            region.nextSlide();
        });

        // Wait fadeTime before start to account for fade in.
        slide.glueframeConfigured = true;
    };

    const triggerProgress = function(glueFrame, region) {
        "use strict";
        glueFrame.get('duration', function(duration){
            glueFrame.get('currentTime', function(currentTime){
                console.log("Setting duration " + (duration - currentTime));
                region.progressBar.start(duration - currentTime);
            });
        });
    };

    window.slideFunctions['twentythree'] = {
        /**
         * Setup the slide for rendering.
         * @param scope
         *   The slide scope.
         */
        setup: function setupSlide (scope) {
            "use strict";
            // Conditions/assumptions:
            //  - Setup is called prior to running the slide. Setup is only
            //    invoked once pr slide-instance.
            //  - We don't yet have access to the region the slide is being
            //    placed in, so we can't bind events that would advance to
            //    next slide yet. Ie. we setup an unconfigured glueframe and
            //    then do the binding in run().
            //  - We have to do console.log's in setup as we don't have access
            //    to a logger.

            // Flow
            // 1: Setup injects an iframe with the nessecary parameters into the
            //    slides container.
            // 2: It clears out any existing glueframes and attach a clean one.
            // 3: See run for the next steps.

            var slide = scope.ikSlide;
            console.log("Setup for slide " + slide.uniqueId);
            var iframeId = 'js-twentythree--player-iframe-' + slide.uniqueId;

            // The iframe is already in place, bail out.
            if (document.getElementById(iframeId)) {
                return;
            }

            // Only continue if we have the nessecary slide-data.
            if (!slide.options.id || !slide.options.domain) {
                console.log("Missing data, continue");
                slide.setupSuccessfull = false;
                return;
            }

            // Build and attach the iframe.
            var iframe = document.createElement("iframe");
            iframe.setAttribute("src", generateIframeSrc(slide));
            iframe.setAttribute('id', iframeId);
            iframe.setAttribute("style", "width:100%; height:100%; position: absolute; top: 0; left: 0;");
            iframe.setAttribute("sandbox", "allow-same-origin allow-scripts");
            iframe.setAttribute("frameborder", "0");
            iframe.setAttribute("border", "0");
            iframe.setAttribute("scrolling", "0");
            iframe.setAttribute("frameborder", "0");
            iframe.setAttribute("allowfullscreen", "1");
            iframe.setAttribute("mozallowfullscreen", "0");
            iframe.setAttribute("webkitallowfullscreen", "0");
            iframe.setAttribute("allow", "autoplay; fullscreen");

            var playerContainerId = "js-twentythree--player-container-" + slide.uniqueId;
            if (!document.getElementById(playerContainerId)) {
                // Only continue if we have data.
                console.log("Unable to find container element with id " + playerContainerId);
                slide.setupSuccessfull = false;
                return;
            }
            document.getElementById(playerContainerId).appendChild(iframe);

            // At this point the player will load, and we can queue up an attach
            // of the glueframe.
            if (slide.glueFrame) {
                console.log("Clearing out previous glueframe");
                detachGlueframe(slide);
            }
            // Attach a new.
            slide.glueFrame = new GlueFrame(iframe, 'Player');
            console.log('Attaching glueframe for slide ' + slide.uniqueId);

            // Signal that run may proceede, but that the frame needs futher
            // configuration.
            slide.setupSuccessfull = true;
            slide.glueframeConfigured = false;
        },

        /**
         * Run the slide.
         *
         * @param slide
         *   The slide.
         * @param region
         *   The region object.
         */
        run: function runSlide (slide, region) {
            "use strict";
            // Conditions/assumptions:
            // - Setup has already injected an iframe and attached a
            //   glueframe, the glueframe might not be configured yet.
            //   Configuring the glueframe means hooking it up with a specific
            //   region, and we don't have access to the region before we've been
            //   run.
            // - We configure the glueframe with an event-handler that uses the
            //   region instance we're passed in to advance to the next slide.
            //   We only do this once pr. slide, so we assume that when the
            //   channel loops and we're run again, we can use the same
            //   reference.

            region.itkLog.info('Running twentythree twentythree slide: ' + slide.title + ' with fid ' + slide.options.id + ' id ' + slide.uniqueId);

            // Only continue if setup was executed successfully.
            if (slide.setupSuccessfull === false) {
                region.itkLog.info('Missing configuration for twentythree slide ' + slide.uniqueId + ', continuing');
                region.nextSlide();
                return;
            }

            // Do the initial glueframe-configuration if nessecary.
            if (!slide.glueframeConfigured) {
                configureGlueframe(slide, region);
            }

            // Start playback.
            slide.glueFrame.set('playing', true);

            // When we're run there is an initial fadein, delay in order for it
            // to complete.
            region.$timeout(function () {
                // Then wait another 500ms and check for errors.
                // TODO: This is a bit wonky as it relies on the fadein and this
                //       arbitary 500ms delay. Consider eg. a watchdog that
                //       monitors the video as long as the slide is running.
                region.$timeout(function () {
                    slide.glueFrame.get('error', function(value){
                        if (value !== null && value !== "") {
                            region.itkLog.info('Player-error detected for fid '+ slide.options.id +' "'+ value +'", advancing');
                            // TODO, we should clear out the iframe completely and
                            // try again for the next run. This would invovle
                            // refactoring the setup to call something we can call
                            // here.
                            region.nextSlide();
                        } else {
                            // Setup the progressbar now that the video is
                            // playing and we know its duration.
                            triggerProgress(slide.glueFrame, region);
                        }
                    }, true);
                }, 500);

            }, region.fadeTime);
        }
    };

    // Source: https://github.com/23/GlueFrame
    var GlueFrame = function(iframe, appName) {

        var $this = this;

        // GlueFrame version
        $this.glueframe = "1.1.3";

        // Allow posting messages only to the domain of the app
        var _domain = (""+iframe.src).split("/").slice(0,3).join("/");

        // Determine method of communication with iframe
        var _method = (function() {
            if (_domain == (""+window.location).split("/").slice(0,3).join("/") ) {
                return "object";
            } else if (typeof window.postMessage !== "undefined") {
                return "post";
            } else {
                return "none";
            }
        })();

        // Poll the iframe until the app is bootstrapped
        $this.ready = false;
        var _readyInterval = window.setInterval(function(){
            if (!this.ready && _method === "object") {
                if (iframe.contentWindow[appName] && iframe.contentWindow[appName].bootstrapped) {
                    $this.ready = true;
                    window.clearInterval(_readyInterval);
                    _processQueue();
                }
            } else if (!this.ready && _method === "post") {
                $this.get("bootstrapped", function(bootstrapped){
                    if (bootstrapped) {
                        $this.ready = true;
                        window.clearInterval(_readyInterval);
                        _processQueue();
                    }
                }, true);
            }
        }, 100);

        $this.glueFrameId = Math.floor((new Date()).getTime()*Math.random());
        var _callbackCount = 0;
        var _callbacks = {};

        // Store callback functions in the parent window
        var _registerCallback = function(callback, requireCallback) {
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
        var _addToQueue = function(method, args) {
            _queue.push({method: method, args: args});
        };

        // Loop through queue when app is ready
        var _processQueue = function() {
            for (var i = 0; i < _queue.length; i += 1) {
                var queueItem = _queue[i];
                queueItem.method.apply(null, queueItem.args);
            }
            _queue = [];
            $this.set("queuedEventsProcessed", true);
        };

        $this.get = function(prop, callback, force) {
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
                iframe.contentWindow.postMessage( JSON.stringify(messageObject), force ? "*" : _domain );
            }
        };

        $this.set = function(prop, val, callback) {
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
                iframe.contentWindow.postMessage( JSON.stringify(messageObject), _domain );
            }
        };

        $this.bind = function(event, callback, triggerQueue) {
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
                iframe.contentWindow.postMessage( JSON.stringify(messageObject), _domain );
            }
        };

        $this.fire = function(event, obj) {
            if (!$this.ready) {
                _addToQueue($this.fire, [event, obj]);
                return;
            }
            if (_method === "object") {
                return iframe.contentWindow[appName].fire.apply(null, [event, obj]);
            } else if (_method === "post") {
                var messageObject = {f: "fire", args: [event, obj]};
                iframe.contentWindow.postMessage( JSON.stringify(messageObject), _domain );
            }
        };

        // Remove event listeners, callbacks and intervals
        $this.destroy = function(){
            if (window.addEventListener) {
                window.removeEventListener("message", _receiveMessage, false);
            } else {
                window.detachEvent("onmessage", _receiveMessage);
            }
            window.clearInterval(_readyInterval);
            _callbacks = {};
        };

        // Parse messages received from iframe
        var _receiveMessage = function(e) {
            if (e.origin === _domain) {
                var data;
                try {
                    data = JSON.parse(e.data);
                }catch(e){}
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
    if(typeof(window.onGlueFrameAvailable)=='function') window.onGlueFrameAvailable(GlueFrame);

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = GlueFrame;
    }
}
