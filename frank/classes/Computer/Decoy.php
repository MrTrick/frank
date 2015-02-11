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

define('DECOY_ERROR', "Implementation error - should not be calling this function");
class Computer_Decoy extends Computer {
	// Decoy computer has no files...
	protected $filesystem = array();
// Constructor
	public function __construct($name, Network &$network) {
		$this->name = $name;
		if (isset(self::$computers[$name])) throw new Exception("The computer $name already exists - cannot be created twice.");
		self::$computers[$name] =& $this;
		//Store the computer name
		$this->name = $name;
		//Don't install any tools... 
		//Connect the decoy computer to the network... (but there's no corresponding interface installed)
		$network->connect($this);
	}
// Filesystem
	public function &getNode($path) { return setError("File not found"); }
	public function createFiles($files, $path='') { throw new Exception(DECOY_ERROR); }
	public function &open($_path, $mode, Session &$session) { return setError("File not found"); }
// Users
	public function addUser($user, $password) { throw new Exception(DECOY_ERROR); }
// Tools
	public function addTool($tool, $path='/bin/', $filename=null) { throw new Exception(DECOY_ERROR); }
	public function getTool($cmd, Session &$session) { return setError("$cmd: command not found (type 'help' to see command list)"); }
// Services
	public function addService($name, $args=null) { throw new Exception(DECOY_ERROR); }
	public function &getService($name) { return setError("$name: service not installed"); }
// Network stuff
	public function addNetworkInterface($alias, $network_name=null, $type='NetworkInterface', $connect=true)  { throw new Exception(DECOY_ERROR); }
	public function &getNeighbourComputer($hostname) { return setError("eth0: $hostname is unreachable.");}
}
