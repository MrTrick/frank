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

class Loader {
	public static $frank;
	public static $save_history=true; //Can be disabled - don't want to append history over history when restoring a current game.
	
	public static function &getEntry() {
		return self::$frank['entry'];
	}

	//Start a new game - load the scenario defined in the data scenario, fill in the frank var and return it.
	public static function create() {
		//Build the frank object
		require_once('data/scenario.php');
		self::$frank = array();
		self::$frank['time']=$TIME;
		self::$frank['entry'] = new Session($home, 'frank');
		
		self::$frank['computers'] =& Computer::$computers;
		self::$frank['networks'] =& Network::$networks;
	}
	
	//Load a game from a saved point
	public static function load() {
		if ($_SESSION['frank']) {
			self::$frank =& $_SESSION['frank'];
			unset($_SESSION['frank']);
		}
		else if ($id=$_SESSION['frank_id'] and $c=file_get_contents("stored/$id.o"))
			self::$frank = unserialize($c);
		else 
			throw new Exception("Cannot load frank!");
		
		//Load the computers and networks into the class vars
		Computer::$computers =& self::$frank['computers'];
		Network::$networks =& self::$frank['networks'];
	}
	
	//Game over, delete the object, and store the history.
	public static function finish() {
		if (!$id=$_SESSION['frank_id']) throw new Exception("No ID available!");
		unlink("stored/$id.o");
		//Does an existing log file exist? Move it to another folder...
		if (is_file("stored/$id.log")) {
			for ($c=1;is_file("stored/won/$id-$c.log");$c++);
			@rename("stored/$id.log", "stored/won/$id-$c.log");
		}
	}
	
	//Store the game state and history log
	public static function save($i, $o) {
		if (!$id=$_SESSION['frank_id']) throw new Exception("No ID available to save to!");
	
		//Append the input and output to the command log...
		if (self::$save_history) {
			$out = $o->html_mode ? $o->stdout : nl2br(str_replace(' ','&nbsp;',htmlspecialchars($o->stdout)));
			file_put_contents("stored/$id.log", "<span class=\"input\">$i</span><br>$out", FILE_APPEND);
		}
		
		//Store the state 
		file_put_contents("stored/$id.o", serialize(self::$frank));	
	}
	
	//Called on initial page load...
	//Check to see if there is a game in progress,
	// - if so, prompt them to see if they want to continue or start anew.
	// - if not, start one anew without a prompt.
	public static function init() {
		//Try and get a session id ...
		$id = $_SESSION['frank_id'];
		if (!$id) $id = $_COOKIE['progress'];
		if ($id and !is_file("stored/$id.o")) $id=false; //Check if it's actually good...
				
		//New game?
		//(no previous information could be found...)
		if (!$id) {
			//Create an ID for them
			$id = $_SERVER['REMOTE_ADDR'] . '_' . mt_rand(1000,9999);
			$_SESSION['frank_id'] = $id;
			setcookie('progress', $id, time()+2592000);
			
			//Store frank state just in the session for now, don't save until the user actually does something.
			self::create();
			$_SESSION['frank'] =& self::$frank;
			
			//Return a welcome message.
			return new Response($_SESSION['frank']['entry']->getWelcome());
		}
		//Continue an existing game...
		else {
			//An ID exists, make sure that it is propagated across both storage methods
			if (!$_SESSION['frank_id']) $_SESSION['frank_id'] = $id;
			if (!$_COOKIE['progress']) setcookie('progress', $id, time()+2592000);
			
			//Return a prompt:
			$prompt = new Loader();
			$_SESSION['frank'] = array('entry'=>$prompt); //Only store the prompt in here for now - more will be loaded later.
			
			return new Response("You seem to have been here before... continue, or start anew?\n(Enter 'start' to restart, or anything else to continue.): ");
		}
	}
	public function execute($input) {
		if ($input=='start') {
			self::create();
			
			//Does an existing log file exist? Move it to another folder...
			if ($id=$_SESSION['frank_id'] and is_file("stored/$id.log")) {
				for ($c=1;is_file("stored/fail/$id-$c.log");$c++);
				@rename("stored/$id.log", "stored/fail/$id-$c.log");
			}
			
			return new Response(self::getEntry()->getWelcome());
		}
		else {
			if (!$id=$_SESSION['frank_id']) throw new Exception("No ID available to restore from!");
			self::load();
			$history = file_get_contents("stored/$id.log");	 //If resuming an existing game, load the history as well... (this might take a while to transmit?)
			
			self::$save_history = false; //Don't save, you've just loaded.
			return new Response($history, R_HTML); //If loading an existing game, return all the history as well...
		}
	}
}
