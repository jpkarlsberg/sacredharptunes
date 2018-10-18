//////////////////////////////////////
//                                  //
//        Wimpy Button Bridge       //
//             v 1.07a              //
//            8/20/2012             //
//                                  //
//      Copyright ©2011 Plaino      //
//           Available at           //
//       www.wimpyplayer.com        //
//                                  //
//////////////////////////////////////

var wimpyButtonSwf			= "http://sacredharptunes.com/includes/wimpy/wimpy_button.swf";
var wimpyButtonImagePlay	= "http://sacredharptunes.com/includes/wimpy/b_play.png";
var wimpyButtonImagePause	= "http://sacredharptunes.com/includes/wimpy/b_pause.png";
var wimpyButtonReg			= "";	// Example: "NE4lN0NubVUlMjhVZCU3RXpFRCUyNDBrJUMyJTgyWDB4U1YlMkEwQVo"
var wimpyButtonTheFile		= "";	// (Startup file) URL to file used to pre-populate the button with a media file.
var wimpyButtonAutoPlay		= "";	// "yes" or ""
var wimpyButtonLoopMe		= "";	// "yes" or ""
var wimpyButtonIcecast		= "";	// An integer or "". e.g. "20" -- Number of seconds between re-connections.
var wimpyButtonBufferAudio	= "";	// An integer or "". e.g. "3" -- Number of seconds worth of the media file to store before playing. (NOT related to icecast)

function wimpyButtonTrackStarted(myFile_in){
	/*
	alert("Now Playing: " + myFile_in);
	//*/
}

function wimpyButtonTrackStopped(myFile_in){
	wimpyButtonState = 0;
	wimpyButtonResetGrapicStates();
	/*
	alert("Track Stopped: " + myFile_in);
	//*/
}

function wimpyButtonTrackDone(myFile_in){
	wimpyButtonState = 0;
	wimpyButtonResetGrapicStates();
	/*
	alert("Track Done: " + myFile_in);
	//*/
}

// -----------------------------------------------------
//               DO NOT EDIT BELOW HERE 
//               (Unless you'r a wizard)
// -----------------------------------------------------



var wimpyButtonContainerID = "wimpyButtonBridgeTarget";	// The "container" div is created dynamically and does NOT reference an existing DIV.
var wimpyButtonID = "wimpyButtonBridge"; // wimpyButtonID is assigned to the <object> that is used to display the flash player.
var wimpyButtonState = 0; // Local reference to play/pause. 1= playing, 0=paused or not yet played.
var wimpyButtonRAND = 0; // Used to create unique ID's if there are more than one buttons on the page.
var wimpyButtonObjIDS = new Object(); // Where we store the unique IDs for individual buttons.
var wimpyButtonCurrentFile = ""; // Used to assist in determinign if we are resuming playback, or need to restart.
var wimpyButtonWidth = 1; // Wimpy Button gets written to the at the top of the page inside a <DIV>, which is 1 pixel width + height,
var wimpyButtonHeight = 1;

// Contains info on useragent.
var wimpyButtonBrowserInfo =(function(){var m=false;if(!m){var k=document,f=navigator,l="Shockwave Flash",o="application/x-shockwave-flash",j="undefined",w3c=typeof k.getElementById!=j&&typeof k.getElementsByTagName!=j&&typeof k.createElement!=j,ua=f.userAgent.toLowerCase(),os=f.platform.toLowerCase(),win=os?/win/.test(os):/win/.test(ua),mac=os?/mac/.test(os):/mac/.test(ua),ios=(/iphone/.test(ua)||/ipod/.test(ua)||/ipad/.test(ua))?true:false,webkit=/webkit/.test(ua)?parseFloat(ua.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,ie=/msie/.test(ua)&& !/opera/.test(ua),version=parseFloat((ua.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[])[1]),flash=false,g=[];c=null;if(typeof f.plugins!=j&&typeof f.plugins[l]=="object"){c=f.plugins[l].description;if(c&& !(typeof f.mimeTypes!=j&&f.mimeTypes[o]&& !f.mimeTypes[o].enabledPlugin)){flash=true;ie=false;c=c.replace(/^.*\s+(\S+\s+\S+$)/,"$1");g[0]=parseInt(c.replace(/^(.*)\..*$/,"$1"),10);g[1]=parseInt(c.replace(/^.*\.(.*)\s.*$/,"$1"),10);g[2]=/[a-zA-Z]/.test(c)?parseInt(c.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0;}}else if(typeof window.ActiveXObject!=j){try{var a=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");if(a){c=a.GetVariable("$version");if(c){flash=true;ie=true;c=c.split(" ")[1].split(",");g=[parseInt(c[0],10),parseInt(c[1],10),parseInt(c[2],10)];}}}catch(e){}}m=true;}if(flash){flash=parseFloat(g[0]+"."+g[1]);}return{w3c:w3c,ua:ua,os:os,win:win,mac:mac,ios:ios,webkit:webkit,ie:ie,version:version,flash:flash}})();
// Used to start the page once fully loaded.
var wimpyButtonAddOnloadEvent = addOnloadEvent=(function(){var f=[],d,exec,c=function(){if(arguments.callee.d)return;arguments.callee.d=true;d=true;while(exec=f.shift())exec();};return function(g){if(d)return g();if(document&&document.getElementsByTagName&&document.getElementById&&document.body){f.push(g);return c();}if(!f[0]){if(document.addEventListener){document.addEventListener('DOMContentLoaded',c,false);}(function(){/*@cc_on;try{document.body.doScroll('up');return c();}catch(e){}/*@if(false)@*/if(/loaded|complete/.test(document.readyState))return c();/*@end@*/;if(!c.d)setTimeout(arguments.callee,30);})();if(window.addEventListener){window.addEventListener('load',c,false);}else if(window.attachEvent){window.attachEvent('onload',c);}}f.push(g);if(document.body){c();}}})();


// Check if we are a local file (file://) or on a web server (http://)
function wimpyButtonCheckLocal(){
	var loc = document.location.href;
	if(loc.substr(0,4) != "http"){
		var obj = document.getElementById("warningBox");
		obj.style.display = "block";
	}
}

// Make it easier to get the flash object (Pulled into a function because this code use to be gy-normous)
function wimpyButtonGetElement(id){
	return document.getElementById(id);
}

function wimpyButtonPause(){
	// Set the state locally.
	wimpyButtonState = 0;
	// Call flash and pause the player.
	wimpyButtonGetElement(wimpyButtonID).js_wimpy_pause();
}


function wimpyButtonPlay(theFile){
	// Check if our local state is playing (12 or paused (0)
	if(wimpyButtonState < 1){
		wimpyButtonState = 1;
		// Set the file URL (name) so we know what is currently playing.
		wimpyButtonCurrentFile = theFile;
		// Call flash to start playing.
		wimpyButtonGetElement(wimpyButtonID).js_wimpy_play(theFile);
	}
}
function wimpyButtonResetGrapicStates(){
	// Run through each button on the page and set the image to "play"
	for(var prop in wimpyButtonObjIDS){
		var x = wimpyButtonGetElement(prop);
		// Only if tag has src attribute (may be a div, or something else weird)
		if(x.src){
			x.src = wimpyButtonImagePlay;
		}
	}
}
function wimpyButtonPlayPause(theFile){
	// Capture the event
	var evt = window.event || arguments.callee.caller.arguments[0];
	// IE / Firefox and friends differences.
	var obj = evt.target || evt.srcElement;
	// If no ID assigned, then assign one.
	if(!obj.id){
		obj.id = "wimpyButtonRAND" + (wimpyButtonRAND++);
	}
	// Add the button to our container.
	wimpyButtonObjIDS[obj.id] = obj.id;
	// Set the image to "play" 
	wimpyButtonResetGrapicStates();
	// Check if the current file palying is the same as the new request, and if we are currently playing.
	if(wimpyButtonCurrentFile != theFile && wimpyButtonState == 1){
		wimpyButtonState = 0;
	}
	// Set the images according tot he state.
	// And call the appropriate action to start or stop playback.
	if(wimpyButtonState == 1){
		if(obj.src){
			obj.src = wimpyButtonImagePlay;
		}
		wimpyButtonPause();
	} else {
		if(obj.src){
			obj.src = wimpyButtonImagePause;
		}
		wimpyButtonPlay(theFile);
	}
}


function wimpyButtonWrite(){
	// Initialization sequence.
	// Check if we are local or not.
	wimpyButtonCheckLocal();
	// Write the DIV wrapper for the Wimpy Button Flash object.
	// Set to "temp" as a voodoo thing to ensure that the function executes fully before continuing.
	var temp = wimpyButtonWriteDiv();
	// Write the Flash object to the DIV.
	// NOTE: We need to seperate the writing of the DIV from the writing of the Flash OBJECT because Internet Explorer (when in "quirks" mode) chokes when the DIV and OBJECT are written at the same time.
	temp = writeFlash();
}

function wimpyButtonWriteDiv(){
	// Create a new DIV and append to BODY
	var newdiv = document.createElement('div');
	newdiv.setAttribute("id",wimpyButtonContainerID);
	newdiv.style.cssText = 'position:fixed;top:0px;left:0px;width:'+wimpyButtonWidth+'px;height:'+wimpyButtonHeight+'px;';
	document.body.appendChild(newdiv);
	return true;
}

function writeFlash(){

	
	var myNewline = "\n", // For discovering issues during development. See "textarea" below.
	AflashVars = new Array(), // Holds our options.
	flashVars = "", // The final flashvars string
	flashVarsQ = "", // Just a ?
	flashHTML = ""; // Our OBJECT code string

	// Set options
	if(wimpyButtonReg != "") AflashVars.push("wimpyReg=" + wimpyButtonReg);
	if(wimpyButtonAutoPlay == "yes" || wimpyButtonAutoPlay == true) AflashVars.push("autoplay=yes");
	if(wimpyButtonLoopMe == "yes" || wimpyButtonLoopMe == true) AflashVars.push("loopMe=yes");
	if(wimpyButtonIcecast != "") AflashVars.push("icecast=" + wimpyButtonIcecast);
	if(wimpyButtonBufferAudio != "") AflashVars.push("bufferAudio=" + wimpyButtonBufferAudio);
	if(wimpyButtonTheFile != "") AflashVars.push("theFile=" + wimpyButtonTheFile);

	// Build options string
	if(AflashVars.length > 0){
		flashVars = AflashVars.join("&");
		flashVarsQ = "?";
	}

	// IE gets OBJECT attributes.
	if(wimpyButtonBrowserInfo.ie){
		flashHTML += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' + wimpyButtonWidth + '" height="' + wimpyButtonHeight + '" id="' + wimpyButtonID + '">' + myNewline;
	} else {
		flashHTML += '<object type="application/x-shockwave-flash" data="' + wimpyButtonSwf + flashVarsQ + flashVars + '" width="' + wimpyButtonWidth + '" height="' + wimpyButtonHeight + '" id="' + wimpyButtonID + '">' + myNewline;
	}

	flashHTML += '	<param name="movie" value="' + wimpyButtonSwf + flashVarsQ + flashVars + '" />' + myNewline;
	flashHTML += '	<param name="bgcolor" value="#000000" />' + myNewline;
	flashHTML += '	<param name="quality" value="high" />' + myNewline;
	flashHTML += '	<param name="scale" value="noscale" />' + myNewline;
	flashHTML += '	<param name="salign" value="lt" />' + myNewline;
	flashHTML += '	<param name="allowScriptAccess" value="always" />' + myNewline;
	flashHTML += '	<param name="allowFullScreen" value="true" />' + myNewline;
	flashHTML += '	<param name="menu" value="false" />' + myNewline;
	flashHTML += '	<param name="wmode" value="opaque" />' + myNewline;
	if(flashVars != ""){
		flashHTML += '	<param name="flashvars" value="' + flashVars + '" />' + myNewline;
	}
	flashHTML += '</object>' + myNewline;

	// Uncomment below to see the OBJECT as it is written into the page:
	//flashHTML += '<textarea name="textarea" id="textarea" wrap="VIRTUAL" cols="40" rows="10">'+flashHTML+'</textarea>' + myNewline;

	// Write the OBJECT intot he DIV.
	var obj = document.getElementById(wimpyButtonContainerID);
	obj.innerHTML = flashHTML;

	return true;
}

// Write Wimpy Button once the DOM is loaded.
wimpyButtonAddOnloadEvent(wimpyButtonWrite);

