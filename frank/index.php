<? 
/*----------------------------------------------------------------------------------------------------------------------
FRANK Engine:
Copyright: Patrick Barnes (c) 2008
Description: 
	Simulation of any number of connected computers, and their connections.
Creator:
	Patrick Barnes aka MrTrick  (mrtrick@gmail.com)
Web Location:
	http://mindbleach.com/frank
----------------------------------------------------------------------------------------------------------------------
The FRANK Engine is licensed under a creative commons license - Reproduction, distribution, and 
derivation are permitted, as long as the following conditions are upheld:
* The license is not changed - (Share-alike)
* Non-commercial use only - (No-commercial) 
* This header is left intact.
* Use of this software is attributed with a phrase such as 'using the FRANK engine' and a link to http://mindbleach.com/frank (Link-back)

This license does not cover the FRANK Game - see the frank/data folder for more information
----------------------------------------------------------------------------------------------------------------------*/
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