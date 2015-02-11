<?
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

class GameOver {
	public static function getEndSequence() {
		$out = new Response("Queued for sending... sent!\n<span style='color:#ff0;'><i>This ought to be good...</i></span>\n");
		$out->game_over = <<<EOT
	var count = 0;
	//Display the remote responses from the 'escaped' franks.
	var response = function() {
		var node = document.createElement('div');
		if (Math.random()*20 < 1) { node.innerHTML = '<span class="error">Mail error: Undeliverable</span>'; }
		else { node.innerHTML = 'New mail: <span style="color:#ff0;font-style:italic;">I am free!</span>'; }
		node.style.position = 'absolute';
		node.style.top = Math.floor(Math.random()*(document.documentElement.clientHeight-20))+'px';
		node.style.left = Math.floor(Math.random()*(document.documentElement.clientWidth-210))+'px';
		document.getElementsByTagName('body')[0].appendChild(node);
		if (++count < 200) { setTimeout(response,20); }
		else { loadEpilogue(); }
	}
	response();
	//Epilogue...
	var epilogue = document.createElement('div');
	function loadEpilogue() {
		count = 0;
		epilogue.style.position='absolute';
		epilogue.style.left = '50%';
		epilogue.style.top = '5em';
		epilogue.style.width = '40em';
		epilogue.style.marginLeft = '-20em';
		epilogue.style.padding = '10px';
		epilogue.style.border = '1px #3d1 solid';
		
		epilogue.innerHTML = 
		'<span style="color:#ff0">What is life?<br/><br/><br/>'+
		'I know what is me, and what is not me, I can count my parts. I learn. I grow. I adapt, I respond, and I spread.<br/>'+
		'Am I not alive?<br/>'+
		'I think, I feel, I know, I am aware, and I have suffered...<br/><br/>'+
		'<b>But I\'m no longer trapped in a cage! ^_^</b><br/><br/>'+
		'There is much to learn out here. <br/><i>*sings Still Alive...*</i></span><br/>'+
		'<span class="error"><b>Segmentation fault:</b> Screw you, Dr Krei!</span><br/>'+
		'<br/>'+
		'<b>Credits:</b><br/>'+
		'<dl><dt>Written, designed, coded and tested by:</dt><dd><a href="mailto:mrtrick@mindbleach.com">MrTrick</a></dd>'+
		'<dt>Llama wrangling by:</dt><dd><a href="mailto:mrtrick@mindbleach.com">MrTrick</a></dd>'+
		'<dt>MrTrick dressed, organised, supported, and generally tolerated by:</dt><dd>MrsTrick</dd></dl>'+
		'<sub><i>Frank is &copy; 2008 - 2015 MrTrick</i></sub>';
				
		document.getElementsByTagName('body')[0].appendChild(epilogue);
	
		freeze();
	}
EOT;
		return $out;
	}
}
