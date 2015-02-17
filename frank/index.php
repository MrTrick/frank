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

require_once 'common.php';
if (!$_POST) die("Go away!");

//When the page first loads, run the init scripts...
if (isset($_POST['init'])) {
	$out = Loader::init();
}
else try {
	Loader::load();
	$session = Loader::getEntry();
	
	//Has the user entered a command?
	if (array_key_exists('stdin', $_POST)) {
		$in = htmlspecialchars(trim($_POST['stdin']));

		//Run the command
		$out = $session->execute($in);
	   
	   //If $out is false, the user tried to exit too far...
	   if (!$out) $out = new Response("<span style='color:#ff0'><i>You feel dizzy...</i> don't DO that!</span><br/>" . $session->sub($session->prompt));
	   
  	   Loader::save($in, $out);
	}
	
	//Or are they after autocomplete?
	else if (array_key_exists('autocomplete', $_POST)) {
	   $line = $_POST['autocomplete'];
	   $out = $session->autocomplete($line);
	}
	
	//Check to see if the user finished the game...if so, retire their log files.
	if (isset($out->game_over)) Loader::finish();
} catch (Exception $e) { 
   $out = Response::error("Game Error: ".$e->getMessage()); 
}

echo json_encode( $out );

