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

define('VERSION', 'v1.0.1');

require_once 'common.php';
if (!$_POST) die("Go away!");

//When the page first loads, run the init scripts...
if ($_POST['init']) {
	$out = Loader::init();
}
else {
	Loader::load();

	$in = htmlspecialchars(trim($_POST['stdin']));
	
	$entry = Loader::getEntry();
	$out = $entry->execute($in);

	//If $out is false, the user tried to exit too far...
	if (!$out) $out = new Response("<span style='color:#ff0'><i>You feel dizzy...</i> don't DO that!</span><br/>" . $entry->sub($entry->prompt));
	
	Loader::save($in, $out);
	
	//Check to see if the user finished the game...if so, retire their log files.
	if ($out->game_over) Loader::finish();
}

echo json_encode( $out );
?>
