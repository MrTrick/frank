/*------------------------------------------------------------------------------------------------
FRANK Engine and Game:
Copyright (c) 2008 - 2015 MrTrick
Description:
   Simulation of any number of connected computers, and their connections.
   Game content, plot, and tools for 'FRANK' game.
Creator:
   Patrick Barnes aka MrTrick  (mrtrick (at) mindbleach.com)
Web Location:
   http://mindbleach.com/frank
License:
   MIT
-------------------------------------------------------------------------------------------------*/
//Look at me, I'm javascript code!

//Objects in the window
var frank_console;
var input_line;
var input_box;

var debug;

var version;

//Key constants
var KEY_TAB = 9;
var KEY_ENTER = 13;
var KEY_UP = 38;
var KEY_DOWN =40;
var KEY_ESC = 27;

//Global vars
var toUpdate = false;

//------------------------------------------------------------------------------------------------------------------------------
//Event Functions - hooked into the page elements
//------------------------------------------------------------------------------------------------------------------------------

//Initialise the application
function load() {
	//Alias the commonly-used areas of the DOM
	frank_console = document.getElementById('console');
	input_line = document.getElementById('input_line');
	input_box = document.getElementById('input_box');
	
	debug = document.getElementById('debug');

	cmd_history.init();
	ajax.onError = ajaxError;

	ajax.load('frank/', {"init":true}, handleAJAXLoadResponse);

	//Have the cursor focus on the input box
	input_box.focus();
}

//Handle non-printed keys
var autocomplete_tab_twice = false;
function keydown(event) {
	ret=true;
	if (event.keyCode==KEY_TAB)  {
	   autocomplete(autocomplete_tab_twice);
	   autocomplete_tab_twice = true;
		
	   //Stop focus from changing
		if (window.opera) { setTimeout(function() {input_box.focus();},5); } //Don't allow TAB to change focus, for Opera browsers...
		return false; //For IE, don't allow TAB to change focus (if browser listens to it)	
	} else {
    	autocomplete_tab_twice = false;
   }
	
	if (event.keyCode==KEY_ENTER) { sendInput(); }
	else if (event.keyCode==KEY_ESC) { input_box.value=''; cmd_history.reset(); ret=false;}
	else if (event.keyCode==KEY_UP) { input_box.value = cmd_history.prev(); setCaretToEnd(input_box);  ret=false;}
	else if (event.keyCode==KEY_DOWN) { input_box.value = cmd_history.next(); setCaretToEnd(input_box);  }
	if (!toUpdate) { toUpdate = setTimeout(updateInputArea,1); }
	return ret;
}	
//Handle printed keys.	
function keypress(event) { if (event.keyCode==KEY_TAB) {return false;} } //TAB override, for Firefox and others.

//------------------------------------------------------------------------------------------------------------------------------
//Client-side DOM stuff...
//------------------------------------------------------------------------------------------------------------------------------

//Update the input_line in the console with the current contents of the textbox.
//includes highlighted text, and the position of the cursor.
function updateInputArea() {
	input_line.innerHTML = chunkText(input_box.value, 80, getOffset(), getSelectionStart(input_box), getSelectionEnd(input_box));
	toUpdate = false;
	scrollToBottom(frank_console);
}

function ajaxError(code, response) {
	var msg;
	switch(code) {
	case 'XMLHTTP': msg = "Your browser doesn't seem to support AJAX. Sorry..."; break;
	case 'HTTP403': msg = "It seems you are <b title='403 Error'>forbidden</b> from continuing."; break;
	case 'HTTP404': msg = "It seems you have lost your <b title='404 Error'>way.</b>"; break;
	case 'HTTP500': msg = "It seems your grip on <b title='500 Server Error'>reality</b> has gone."; break;
	case 'MALFORMED': //msg = "Zhfg or n tyvgpu va gur <b title='Znysbezrq NWNK erfcbafr. Try again, maybe?'>zngevk</b>"; break;
		msg = "Malformed response: " + response; break;
	//case 'URL':
	default: msg = "Something feels <b title='"+code+" Error'>wrong.</b>";
	}
	printError(msg);
}

function print(text, htmlEnabled, type) {
	if (!htmlEnabled) { text = chunkText(text, 80, getOffset()); }
	frank_console.removeChild(input_line);
	if (defined(type)) { frank_console.innerHTML += '<span class="'+type+'">'+text+'</span>'; }
	else { frank_console.innerHTML += text; }
	frank_console.appendChild(input_line);
	scrollToBottom(frank_console);
}
function printError(text) {
	print("<span class=\"error\">Error:<br/>"+text+"</span>", true);
}

//Stops all the event handlers...
function freeze() {
	input_box.onkeydown = null;
	input_box.onkeypress = null;
	document.getElementsByTagName('body')[0].onfocus=null;
	frank_console.onclick=null;
}

//------------------------------------------------------------------------------------------------------------------------------
//Application functions - 
//------------------------------------------------------------------------------------------------------------------------------
function sendInput() {
	line = input_box.value;	
	cmd_history.add(line);

	input_line.innerHTML = '';
	input_box.value ='';
	print(line + "\n", false, 'input'); //Print it, and escape any html in the input box

	ajax.load('frank/', {"stdin":line}, handleAJAXResponse);
}

function autocomplete(twice) {
   var line = input_box.value;
	var caret = getSelectionStart(input_box);
	var partial = line.substr(0, caret);
	
	ajax.load('frank/', {"autocomplete": partial}, function(response) {
	   //Has the input changed since requesting autocomplete?
	   //Our response is possibly out of date, so ignore it.
	   if (line !== input_box.value) return;
	   
	   //Is the session in a mode that supports autocomplete?
	   if (!response) return;
	
	   //If the input can be autocompleted, do so
      var make_space = response.choices 
                    && response.choices.length == 1 
                    && response.choices[0].charAt(response.choices[0].length - 1) !== '/';
	   
	   var suggest = response.suggest + (make_space ? ' ' : ''); 
	   if (suggest) {
	       input_box.value = line.substr(0, caret) + suggest + line.substr(caret); 
	       setCaret(input_box, caret + suggest.length); 
	       updateInputArea();
	   }
	   
	   //Otherwise, display the choices
	   else if (twice) {
	       print(line + "\n", false, 'input');
	       print(response.choices.join("  ") + "\n" + response.prompt, false);
	   }
	   
	});
}

function handleAJAXResponse(args) {
	if (typeof args != 'object') { printError("Incorrect response - expected type object"); }
	print(args.stdout, args.html_mode);
	
	if (args.history=='push') { cmd_history.push(); }
	else if (args.history=='pop') { cmd_history.pop(); }
	
	if (defined(args.game_over)) {
		eval(args.game_over);
	}
}

function handleAJAXLoadResponse(args) {
	handleAJAXResponse(args);
	document.getElementById('heading').innerHTML = "FRANK "+args.version;
}

//------------------------------------------------------------------------------------------------------------------------------
//History Class - stores previously used commands, up to 20, and 20 levels
//------------------------------------------------------------------------------------------------------------------------------
var cmd_history = {
	arr: [], ind: -1,
	init: function() { this.arr=[['']]; this.ind=-1; },
	add: function(entry) { this.arr[0].unshift(entry); this.ind=-1;},
	reset: function() { this.ind=-1; },
	push: function() { this.arr.unshift(['']); this.ind=-1; },
	pop: function() { if (this.arr.length>1) {this.arr.shift();} else {this.arr=[['']];} this.ind=-1; },
	prev : function() { if (this.ind < this.arr[0].length-1) {this.ind++;} return this.arr[0][this.ind]; },
	next : function() { if (this.ind >= 0) {this.ind--;} if (this.ind>=0) {return this.arr[0][this.ind];} else {return '';} }
};

//------------------------------------------------------------------------------------------------------------------------------
//AJAX Class - handles all AJAX calls. [minimized from ajax.js]
//------------------------------------------------------------------------------------------------------------------------------
var ajax={
	onError:false,
	load:function(url,args,callback){
		var onError=this.onError;
		if(!url){onError('URL');return;}
		var http=false;
		if(typeof ActiveXObject!='undefined'){try{http=new ActiveXObject("Msxml2.XMLHTTP");}catch(e1){try{http=new ActiveXObject("Microsoft.XMLHTTP");}catch(e2){http=false;}}}
		else if(typeof XMLHttpRequest !='undefined'){try{http=new XMLHttpRequest();}catch(e3){http=false;}}
		if(!http){onError('XMLHTTP');return;}
		var data='';for(i in args){data+=encodeURIComponent(i)+'='+encodeURIComponent(args[i])+'&';}
		if(http.overrideMimeType){http.overrideMimeType('text/xml');}
		http.open("POST",url,true);
		http.onreadystatechange=function(){
			if(http.readyState!=4){return;}
			if(http.status!=200){onError('HTTP'+http.status);return;}
			var result=http.responseText?http.responseText:'';result=result.replace(/[\n\r]/g,"");
			if(result){
				try{result=eval('('+result+')');}catch(E){onError('MALFORMED',http.responseText);return;}
				if(callback){callback(result);}
			} else if(callback){callback();}
		};
		http.setRequestHeader("Method","POST "+url+" HTTP/1.1");
		http.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
		http.send(data);
	}
};

//------------------------------------------------------------------------------------------------------------------------------
//Compatibility Functions - to abstract implementation differences in different browsers
//------------------------------------------------------------------------------------------------------------------------------
function defined(a) { return typeof a != 'undefined'; } 
function getEvent(e) { if( defined(window.event) ) { return window.event; } else { return e; }}
//IE sucks for anything caret-related!
function setCaretToEnd (control) { //In most browsers, set the caret in a text control to the end.
	var range, length;
	if (control.createTextRange) { range = control.createTextRange(); range.collapse(false); range.select(); }
	else if (control.setSelectionRange) { control.focus(); length = control.value.length+1; control.setSelectionRange(length, length); }
}
function getSelectionStart(control) { //Get the index of the start of the selection
	if (defined(control.selectionStart)) { return control.selectionStart; }
	var range = document.selection.createRange();
	if (range.compareEndPoints("StartToEnd", range)!==0) { range.collapse(true); }
	var b = range.getBookmark();
	return b.charCodeAt(2) - 2;
}
function getSelectionEnd(control) { //Get the index of the end of the selection
	if (defined(control.selectionEnd)) { return control.selectionEnd; }
	var range = document.selection.createRange();
	if (range.compareEndPoints("StartToEnd", range)!==0) { range.collapse(false); }
	var b = range.getBookmark();
	return b.charCodeAt(2) - 2;
}
function setCaret(control, position) {
   if (defined(control.setSelectionRange)) { return control.setSelectionRange(position, position); }
   alert("Oops, not supported on IE < 9");
}
function scrollToBottom(control) {
	control.scrollTop = (control.scrollHeight>control.offsetHeight)?control.scrollHeight:control.offsetHeight;
	if (window.opera) {control.scrollTop = 999999999;}
}
function escapeHTML(str) {
	str.replace(/"/g,"&quot;");
	str.replace(/</g,"&lt;");
	str.replace(/>/g,"&gt;");
	str.replace(/&/g,"&amp;");
	return str;
}
function getOffset() {
	return input_line.previousSibling && input_line.previousSibling.nodeValue ? input_line.previousSibling.nodeValue.length : 0;
}
function chunkText(str, size, offset_col, start_sel, end_sel) {
	if (defined(start_sel) && (!defined(end_sel) || start_sel==end_sel)) { end_sel = start_sel+1; }
	if (!defined(str)) { str=''; }
	var sub = {"\n":"<br/>", " ":"&nbsp;", "<":"&lt;", ">":"&gt;", "&":"&amp;"};	
	var out = [];
	var col = offset_col;
	for (var i in str.split('')) {
		if (i == start_sel) { out.push("<span class=\"highlight\">"); }
		else if (i == end_sel) { out.push("</span>"); }
		c = str.charAt(i);
		if (c=="\n") { col=0; }
		if (defined(sub[c])) { c = sub[c]; }
		out.push( c );
		if (++col == 80) { out.push("<br />"); col = 0; }
	}
	if (end_sel > str.length) { out.push("<span class=\"highlight\">&nbsp;</span>"); }
	return out.join('');
}
