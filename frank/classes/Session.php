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

/* What does a session do?

- Links a client with a computer. 
- The client may be the javascript code, or a command on another computer.
- The client keeps track of the session.
- Tools store information in the session (as opposed to their own instances)

*/

class Session {
	//Session belongs to a particular computer...
	public $computer;
	
	//The user's identity for this session (could be null)
	public $user;	
		
	//Link to the next session, if connected
	public $next=null;
	
	//If at the shell, state is null. If running a program interactively, state is an array of form [classname, method]
	//The non-null value indicates the re-entry point when $session->execute is called
	public $state=null;
	
	//Session variables...
	public $prompt;
	public $pwd = array();
	
	//Public var to hold any other useful state information specific to a tool or remote service. 
	public $state_info = array();

//-----------------------------------------------------------------------------------------------------------
// Constructor
//-----------------------------------------------------------------------------------------------------------
	//pre-cond: Before constructing, authenticate $user against that computer...
	public function __construct(&$computer, $user) {
		$this->computer =& $computer;
		$this->user = $user;
		
		$sh_defaults = $this->getShellDefaults();	
		$this->prompt = $this->sub($sh_defaults['prompt']);
		$this->pwd = $this->path($this->sub($sh_defaults['pwd']));
	}
	
//-----------------------------------------------------------------------------------------------------------
//Useful functions relating to sessions
//-----------------------------------------------------------------------------------------------------------	
	//Convert relative path to an absolute path array
	public function path($path) {
		//If already an array, assume that it's in absolute form already.
		if (is_array($path)) return $path;
		//Start at the working directory, or at the root node if path string is absolute.
		$out = ($path{0}=='/') ? array() : $this->pwd;
		//Is there any path to iterate through (FIXES EXPLODE PROBLEM WHEN STRING IS EMPTY)
		$path = trim($path, '/');
		if (!$path) return $out;
		//Process the path string...
		foreach (explode('/',$path) as $d) { 
			if ($d=='.') continue; //stay at the same level
			else if ($d=='..') { if (!array_pop($out)) return setError("Invalid path"); } //Try to go up a level (pop the last path element off)
			else array_push($out, $d);
		}
		return $out;
	}
	
	//Convert $ placeholders into their values
	public function sub($in) { 
		$name = $this->computer->name;
		$date = getDateStamp();
		$user = $this->user;
		$pwd = $this->pwd;
		extract($this->state_info, EXTR_SKIP);
		eval( '$out = "'.$in.'";' );
		return $out;
	}

	//Get the default values from the /etc/sh config file
	public function getShellDefaults() {
		return explode_assoc("\n","=",$this->computer->read('/etc/sh', $this));
	}

	//Display a welcome message from that computer, using the .motd file if it exists.
	public function getWelcome() { 
		return $this->sub($this->computer->read('/etc/welcome', $this).$this->prompt);
	}
	
	//Attach a function or class::method to the session - any further input will be funelled through it until detach is called
	public function attach($reentry_point) {
		$this->state = $reentry_point;
	}
	//Detach from the session - any further input will come through 'run'.
	public function detach() {
		$this->state = null;
		$this->state_info = array();
	}
	
//-----------------------------------------------------------------------------------------------------------
//Execute method
//-----------------------------------------------------------------------------------------------------------	
	//Execute input passed to the session to the computer it's connected to
	public function execute($input) {
		//Is something still running in the session? If so, pass that input directly to it...
		if ($this->state)
			$output = call_user_func($this->state, $input, @$this);
		//No tool currently running, try and execute one...
		else if ($input) {
			$args = preg_split("/\s+/", trim($input));
			$cmd_name = array_shift($args);
			if ($cmd_name == 'exit') return false;
			if (!$cmd = $this->computer->getTool($cmd_name, $this)) 
				$output = Response::error();
			else if ($args[0] == '--help') //Special case - alias '$tool --help' to 'help $tool'
				$output = call_user_func(array('Tool_help', 'run'), array($cmd_name), @$this);
			else
				$output = call_user_func(array($cmd, 'run'), $args, @$this);
		}
		//Enter was pressed at the shell, return a blank response...
		else $output = new Response("");
		
		//If the tool has returned to the shell, show the prompt.
		if (!$this->state) 
			$output->stdout .= $this->sub($this->prompt);
	
		return $output;
	}
}

?>