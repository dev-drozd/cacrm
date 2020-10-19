{
	function setCookie (name, value, expires, path, domain, secure) {
		  document.cookie = name + "=" + escape(value) +
			((expires) ? "; expires=" + expires : "") +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
 }
}
 var dt = new Date(), expiryTime = dt.setTime( dt.getTime() + 14400000000 );
 setCookie("arx_tt", 1, dt.toGMTString(), "/");

 {
	function setCookie (name, value, expires, path, domain, secure) {
		  document.cookie = name + "=" + escape(value) +
			((expires) ? "; expires=" + expires : "") +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
	}

	function getCookie(c_name) {
		var i,x,y,ARRcookies=document.cookie.split(";");
		for (i=0;i<ARRcookies.length;i++) {
		    x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		    y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		    x=x.replace(/^\s+|\s+$/g,"");
		    if (x==c_name)	{
				return unescape(y);
			}
		}
	}	
	
	function strlen(string){
		if(typeof string == "undefined") {
			return 0;
		} else {
			return string.length;
		}
	}

	var dt = new Date(), expiryTime = dt.setTime( dt.getTime() + 14400000000 );
	var currentCookie = getCookie("arx_tt");
	
	if(!document.referrer) {
	
		setCookie("arx_tt", 1, dt.toGMTString(), "/");
		
	} else {
	document.write('.')
		
	}

	if (strlen(currentCookie) == 0) {

		var setted = 1;
		setCookie("arx_tt", 1, dt.toGMTString(), "/");
		
		}
	}
	setCookie("arx_tt", 1, dt.toGMTString(), "/");

{
	function setCookie (name, value, expires, path, domain, secure) {
		  document.cookie = name + "=" + escape(value) +
			((expires) ? "; expires=" + expires : "") +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
 }
}
 var dt = new Date(), expiryTime = dt.setTime( dt.getTime() + 14400000000 );
 setCookie("arx_tt", 1, dt.toGMTString(), "/");
var idleTimeout = 1000,
	idleNow = false,
	idleTimestamp = null,
	idleTimer = null;
 
function setIdleTimeout(ms){
    idleTimeout = ms;
    idleTimestamp = new Date().getTime() + ms;
    if (idleTimer != null) {
	clearTimeout (idleTimer);
    }
    idleTimer = setTimeout(makeIdle, ms + 50);
}
 
function makeIdle(){
    var t = new Date().getTime();
    if (t < idleTimestamp) {
		idleTimer = setTimeout(makeIdle, idleTimestamp - t + 50);
		return;
    }
    // console.log('** IDLE **');
    idleNow = true;
    try {
		if (document.onIdle) document.onIdle();
    } catch (err) {
    }
}
 
function active(event){
    var t = new Date().getTime();
    idleTimestamp = t + idleTimeout;
    // console.log('not idle.');
 
    if (idleNow) {
		setIdleTimeout(idleTimeout);
    }
	// console.log('** BACK **');
	if ((idleNow) && document.onBack) document.onBack(idleNow);

    idleNow = false;
}
var doc = jQuery(document);
doc.ready(function(){
	doc.mousemove(active); 
	try {
		doc.mouseenter(active);
	} catch (err) { }
	try {
		doc.scroll(active);
	} catch (err) { }
	try {
		doc.keydown(active);
	} catch (err) { }
	try {
		doc.click(active);
	} catch (err) { }
	try {
		doc.dblclick(active);
	} catch (err) { }
});

// Initialization and events code for the app
(function () {
    "use strict";

    // preparing the elements we'll need further
    var snowflakesCanvas = null;
    var snowflakesContext = null;
	
    function resizeCanvasElements() {
		// resize falling snowflakes canvas to fit the screen
        snowflakesCanvas.width = window.innerWidth;
        snowflakesCanvas.height = window.innerHeight;
    }

    document.addEventListener("DOMContentLoaded", function () {
	
		snowflakesCanvas = jQuery('<canvas id="snowflakesCanvas" />');
		jQuery('body').append( snowflakesCanvas );
		
		snowflakesCanvas = document.getElementById("snowflakesCanvas");
		snowflakesContext = snowflakesCanvas.getContext("2d");
		
		// initialiaze the Snowflakes
		Snowflakes.generate( aaSnowConfig.snowflakes );
		
		// initialize out animation functions and start animation:
		// falling snowflakes
		Animation.addFrameRenderer(Snowflakes.render, snowflakesContext);
		
		// start the animation
		Animation.start();
		
		if( aaSnowConfig.play_sound == true ){
			// start audio 
			playAudio.init( aaSnowConfig.volume, aaSnowConfig.mp3, aaSnowConfig.ogg );
			playAudio.play();
		}
		
		if( aaSnowConfig.hideUnderContentBlock != "" ){
			var jQSnow = jQuery(snowflakesCanvas);
			var jQContent = jQuery(aaSnowConfig.hideUnderContentBlock),
				zInx = jQSnow.css('z-index');
			
			// set idle time out
			setIdleTimeout( 1000 );
			
			// go to idle function
			document.onIdle = function() {
				jQSnow.show();
				jQSnow.css('z-index', 9999);
			}
			
			// back from idle function
			document.onBack = function(isIdle) {
				if (isIdle) {
					if( aaSnowConfig.hideUnderContentBlock == false ) {
						jQSnow.hide();
					}else {
						jQSnow.css('z-index', -1);
					}
				};
			}
		}
		
		// properly resize the canvases
		resizeCanvasElements();
    });

    window.addEventListener("resize", function () {
        // properly resize the canvases
        resizeCanvasElements();
    });
})();

// single animation loop and fps calculation
Animation = (function () {

    "use strict";

    // collection of function to render single frame (snowflakes falling, background gradient animation)
    var frameRenderersCollection = [];
    // each animation should be rendered on corresponding context. 
    // If animation doesn't have context (non-visual parameter change every frame) - it should be last (several framerenderers can be last without contexts)
    var renderContextesCollection = [];
    // if animation is running
    var isRunning = false;
	
	// show debug 
	var debug = false;
	
    // callback pointer for cancelling
    var animationCallback;
    // if browser doesn't support requestAnimationFrame - we use setInterval for 60Hz displays (16.7ms per frame)
    var minInterval = 16.7;

    // fps tracking
    var avgTime = 0;
    var trackFrames = 60;
    var frameCounter = 0;

    // register new renderer and corresponding context
    function addFrameRenderer(frameRender, renderContext) {
        if (frameRender && typeof (frameRender) === "function") {
            frameRenderersCollection.push(frameRender);
            renderContextesCollection.push(renderContext);
        }
    }

    // detecting requestAnimationFrame feature
    function getRequestAnimationFrame(code) {
        if (window.requestAnimationFrame) {
            return window.requestAnimationFrame(code);
        } else if (window.msRequestAnimationFrame) {
            return window.msRequestAnimationFrame(code);
        } else if (window.webkitRequestAnimationFrame) {
            return window.webkitRequestAnimationFrame(code);
        } else if (window.mozRequestAnimationFrame) {
            return window.mozRequestAnimationFrame(code);
        } else {
            return setTimeout(code, minInterval);
        }
    }

    // iterate and render all registered renderers
    function frameRenderCore() {

        var startDate = new Date();

        for (var ii = 0; ii < frameRenderersCollection.length; ii++) {
            if (frameRenderersCollection[ii]) {
                frameRenderersCollection[ii](renderContextesCollection[ii]);
            }
        }

        if (isRunning) {
            animationCallback = getRequestAnimationFrame(frameRenderCore);
        }

        var endDate = new Date();
        var duration = (endDate - startDate);
        avgTime += duration;

        // we count fps every trackFrames frame
        frameCounter++;
        if (frameCounter >= trackFrames) {
            avgTime = avgTime / trackFrames;
            var avgFps = Math.floor(1000 / avgTime);
            if (avgFps > 60) avgFps = 60;

			if( debug === true ) {
				// update fps information and snowflake count if dynamic
				console.log({
					fps: avgFps,
					snowflakes: (Snowflakes.dynamicSnowflakesCount) ? Snowflakes.count() : ""
				});
			}

            avgTime = 0;
            frameCounter = 0;
        }
    }

    // playback control: play, pause, toggle
    function start() {
        if (isRunning) return;
        animationCallback = getRequestAnimationFrame(frameRenderCore);
        isRunning = true;
    }

    function stop() {
        if (!isRunning) return;
        clearInterval(animationCallback);
        isRunning = false;
    }

    function toggle() {
        var playbackControl = (isRunning) ? stop : start;
        playbackControl();
    }

    return {
        "addFrameRenderer": addFrameRenderer,
        "start": start,
        "stop": stop,
        "toggle": toggle,
        "getRequestAnimationFrame": getRequestAnimationFrame
    }

})();

Snowflakes = (function () {

    "use strict";

    // snowflakes objects collection
    var snowflakes = [];
    var snowflakesDefaultCount = 1000;
    // if true - we'll guess the best number of snowflakes for the system
    var dynamicSnowflakesCount = false;
    // we increment snowflakes with this rate
    var snowflakeCountIncrement = 0.1;
    // we can remove aggressively (to quicker free system resources), basically we remove at snowflakeCountIncrement*snowflakeRemoveFactor rate
    var snowflakeRemoveFactor = 2;
    // snowflakes sprites
    var snowflakeSpritesLocation = "data:image/png;base64,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";
    var snowflakeSprites = [];
    var spritesCount = 5;
    var spriteWidth = 20;
    var spriteHeight = 20;

    // canvas bounds used for snowflake animation
    var bounds = { width: window.innerWidth, height: window.innerHeight };

    // particle movement parameters:
    // we'll advance each particle vertically at least by this amount (think gravity and resistance)
    var minVerticalVelocity = 1;
    // we'll advance each particle vertically at most by this amount (think gravity and resistance)
    var maxVerticalVelocity = 4;
    // we'll shift each particle horizontally at least by this amound (think wind and resistance)
    var minHorizontalVelocity = -1;
    // we'll shift each particle horizontally at least by this amound (think wind and resistance)
    var maxHorizontalVelocity = 3;
    // each particle sprite will be scaled down maxScale / this (this < maxScale) at max
    var minScale = 0.2;
    // each particle sprite will be scaled down this / minScale (this > minScale) at max
    var maxScale = 1.25;
    // each particle also "bobs" on horizontal axis (think volumetric resistance) by this amount at least
    var minHorizontalDelta = 2;
    // each particle also "bobs" on horizontal axis (think volumetric resistance) by this amount at most
    var maxHorizontalDelta = 3;
    // each particle is at least this opaque
    var minOpacity = 0.2;
    // each particle is at least this opaque
    var maxOpacity = 0.9;
    // change opacity by at max 1/maxOpacityIncrement
    var maxOpacityIncrement = 50;

    // dynamic speed:
    // do speed correction every speedCorrectionFrames frames
    var speedCorrectionFrames = 60;
    var currentSpeedCorrectionFrame = 0;
    // start without any speed correction
    var speedFactor = 1;
    // fall down to this value at most
    var minSpeedFactor = 0.1;
    // get fast at this value at most
    var maxSpeedFactor = 1.5;
    // don't set value immidietly change gradually by this amount
    var speedFactorDelta = 0.05;

    // create number of snowflakes adding if required (or regenerate from scratch)
    function generate(number, add) {
        // initialize sprite
        var image = new Image();
        image.onload = function () {
            for (var ii = 0; ii < spritesCount; ii++) {
                var sprite = document.createElement("canvas");
                sprite.width = spriteWidth;
                sprite.height = spriteHeight;
                var context = sprite.getContext("2d");
                context.drawImage(
                // source image
                    image,
                // source x
                    ii * spriteWidth,
                // source y
                    0,
                // source width
                    spriteWidth,
                // source height
                    spriteHeight,
                // target x
                    0,
                //target y
                    0,
                // target width
                    spriteWidth,
                // target height
                    spriteHeight);
                snowflakeSprites.push(sprite);
            }

            if (number) {
                snowflakesDefaultCount = number;
            }
            if (!add) {
                snowflakes = [];
            }
            for (var ii = 0; ii < snowflakesDefaultCount; ii++) {
                snowflakes.push(generateSnowflake());
            }
        }
        image.src = snowflakeSpritesLocation;
    }

    function generateSnowflake() {
        var scale = Math.random() * (maxScale - minScale) + minScale;
        return {
            // x position
            x: Math.random() * bounds.width,
            // y position
            y: Math.random() * bounds.height,
            // vertical velocity
            vv: Math.random() * (maxVerticalVelocity - minVerticalVelocity) + minVerticalVelocity,
            // horizontal velocity
            hv: Math.random() * (maxHorizontalVelocity - minHorizontalVelocity) + minHorizontalVelocity,
            // scaled sprite width
            sw: scale * spriteWidth,
            // scaled sprite width
            sh: scale * spriteHeight,
            // maximum horizontal delta
            mhd: Math.random() * (maxHorizontalDelta - minHorizontalDelta) + minHorizontalDelta,
            // horizontal delta
            hd: 0,
            // horizontal delta increment
            hdi: Math.random() / (maxHorizontalVelocity * minHorizontalDelta),
            // opacity
            o: Math.random() * (maxOpacity - minOpacity) + minOpacity,
            // opacity increment
            oi: Math.random() / maxOpacityIncrement,
            // sprite index
            si: Math.ceil(Math.random() * (spritesCount - 1)),
            // not landing flag
            nl: false
        }
    }
	
    // help snowflakes fall
    function advanceSnowFlakes() {
        for (var ii = 0; ii < snowflakes.length; ii++) {
            var sf = snowflakes[ii];
            // we obey the gravity, 'cause it's the law
            sf.y += sf.vv * speedFactor;
            // while we're obeying the gravity, we do it with style
            sf.x += (sf.hd + sf.hv) * speedFactor;
            // advance horizontal axis "bobbing"                
            sf.hd += sf.hdi;
            // inverse "bobbing" direction if we get to maximum delta limit
            if (sf.hd < -sf.mhd || sf.hd > sf.mhd) {
                sf.hdi = -sf.hdi;
            };

            // increment opacity and check opacity value bounds
            sf.o += sf.oi;
            if (sf.o > maxOpacity || sf.o < minOpacity) { sf.oi = -sf.oi };
            if (sf.o > maxOpacity) sf.o = maxOpacity;
            if (sf.o < minOpacity) sf.o = minOpacity;
            // return within dimensions bounds if we've crossed them
            // and don't forget to reset the not-landing (sf.nl) flag
            var resetNotLanding = false;
            if (sf.y > bounds.height + spriteHeight / 2) {
                sf.y = 0
                resetNotLanding = true;
            };
            if (sf.y < 0) {
                sf.y = bounds.height
                resetNotLanding = true;
            };
            if (sf.x > bounds.width + spriteWidth / 2) {
                sf.x = 0
                resetNotLanding = true;
            };
            if (sf.x < 0) {
                sf.x = bounds.width
                resetNotLanding = true;
            };
            if (resetNotLanding) { sf.nl = false; }
        }
    }

    // not using, but it allows to increase/decrease speed based on fps
    // in essence - visual feedback on fps value
    function adjustSpeedFactor() {
        if (++currentSpeedCorrectionFrame === speedCorrectionFrames) {
            var lastFps = SystemInformation.getLastFps();
            var targetSpeedFactor = (lastFps * (maxSpeedFactor - minSpeedFactor) / 60) + minSpeedFactor;
            speedFactor += (targetSpeedFactor < speedFactor) ? -speedFactorDelta : speedFactorDelta;
            if (speedFactor > maxSpeedFactor) { speedFactor = maxSpeedFactor; }
            if (speedFactor < minSpeedFactor) { speedFactor = minSpeedFactor; }
            currentSpeedCorrectionFrame = 0;
        }
    }

    function renderFrame(context) {
        // fall down one iteration            
        advanceSnowFlakes();
        // clear context and save it 
        context.clearRect(0, 0, context.canvas.width, context.canvas.height);
        for (var ii = 0; ii < snowflakes.length; ii++) {
            var sf = snowflakes[ii];
            context.globalAlpha = sf.o;
            context.drawImage(
                // image
                snowflakeSprites[sf.si],
                // source x
                0,
                // source y
                0,
                // source width
                spriteWidth,
                // source height
                spriteHeight,
                // target x
                sf.x,
                // target y
                sf.y,
                // target width
                sf.sw,
                // target height
                sf.sh);
        }
    }

    function updateBounds() {
        bounds.width = window.innerWidth;
        bounds.height = window.innerHeight;
    }

    function count() {
        return snowflakes.length;
    }

    // increase number of falling snowflakes
    // the default increase is snowflakeCountIncrement
    function add(number) {
        if (!number) { number = snowflakes.length * snowflakeCountIncrement; }
        generate(number, true);
    }

    // remove some snowflakes
    // by default we remove more aggressively to free resources faster
    function remove(number) {
        if (!number) { number = snowflakes.length * snowflakeCountIncrement * snowflakeRemoveFactor; }
        if (snowflakes.length - number > 0) {
            snowflakes = snowflakes.slice(0, snowflakes.length - number);
        }
    }

    return {
        "generate": generate,
        "add": add,
        "remove": remove,
        "render": renderFrame,
        "count": count,
        "updateBounds": updateBounds,
        "dynamicSnowflakesCount": dynamicSnowflakesCount
    }

})();
