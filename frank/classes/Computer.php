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

class Computer {
	public static $computers=array();

	public $name; //Name of the computer - must be unique.

	const default_welcome = '[Timestamp - $date] Welcome to machine $name.
';

	// Store all files, programs, configuration, etc inside the 'filesystem'.
	protected $filesystem = array(
		'.permissions'=>"all:r\nanonymous:",
		'bin'=>array(),		// (all system programs live in here)
		'boot'=>array(), 		// (service 'binaries' live in here)
		'dev'=>array(		// (some dummy files pretending to be hard disks, etc, also the network interfaces configuration)
			'cdrom'=>"head: cannot open '/dev/cdrom' for reading: No medium found",
			'hda'=>"/dev/hda cannot be read: incorrect character set",
			'hda1'=>"/dev/hda1 cannot be read: incorrect character set",
			'hda2'=>"/dev/hda2 cannot be read: incorrect character set",
			'net'=>array(),
		),
		'etc'=>array(		// (any other configuration files live in here)
			'passwd'=>"root:114c9defac04969c7bfad8efaa8ea194", //'altered' hash, doesn't correspond with any password.
			'sh'=>"pwd=/home/\$user/\nprompt=[\$name]>",
			'welcome'=>Computer::default_welcome
		),
		'home'=>array(),		// (each user has a home directory in here)
		'media'=>array(),		// (if any removeable storage exists, it will be in here)
		'root'=>array(		// (the root user's home directory)
			'.permissions'=>"all:\nroot:w" //disable access to anyone but the root user.
		),
		'tmp'=>array(		// (tmp directory, all users have access)
			'.permissions'=>"all:w",
			'test.txt'=>"foo:bar! Llamamama llamamama..."
		)
	);
				
//-----------------------------------------------------------------------------------------------------------
// Constructor
//-----------------------------------------------------------------------------------------------------------
	public function __construct($name) {
		$this->name = $name;
		if (isset(self::$computers[$name])) throw new Exception("The computer $name already exists - cannot be created twice.");
		self::$computers[$name] =& $this;

		//Store the computer name
		$this->name = $name;
		//Install all the standard tools
		$arr = array(
			'ls', 'cd', 'pwd', 'cat', 'ps', 'find', 
			'cp', 'mv', 'rm', 'mkdir', //'ed','echo', 
			'user', 'hostname', 'ping', 'ifconfig', 
			'ftp', 'ssh',
			'su', 'help'
		);
		foreach ($arr as $t) $this->addTool($t);
	}

//-----------------------------------------------------------------------------------------------------------
// Filesystem
//-----------------------------------------------------------------------------------------------------------
	//System functions - they ignore permissions
	//------------------------------------------------
	public function createFiles($files, $path='') {
		$node =& $this->getNode($path);
		if (!is_array($node)) return false;
		foreach ($files as $name=>$obj) //Add the files and folders in the $files arg to the filesystem...
			if (is_array($obj) and isset($node[$name])) { //If a folder that exists, create it first
				if (!$this->createFiles($obj, ($path=='')?$name:rtrim($path,'/').'/'.$name)) return false; 
			} else { //If a file, or a new folder, place it in the filesystem.
				$node[$name] = $obj; 
	      }
				
		return true;
	}
	public function &getNode($path) {
		$path = trim($path,'/');
		if (!$path) return $this->filesystem; //Root node?
		$path = explode('/',$path); //No support for '..' or '.' here...
		$node =& $this->filesystem;
		foreach($path as $step) if (isset($node[$step])) $node=&$node[$step]; else return setError("File not found");
		return $node;
	}

	// Read/write functionality inside the context of a session
	//----------------------------------------------------------------------------------------------
	//Try and get a reference to the node
	public function &open($_path, $mode, Session &$session) {
		//Convert the text path into an array of path parts
		$path = $session->path($_path);
		if ($path===false) return false;
		//Iterate through the filesystem, and get the permissions level...
		$node =& $this->filesystem; //starting at the root node.
		do {
			//If a permissions files exists, check it (user-specific permissions override all permissions)
			if (is_array($node) and isset($node['.permissions']) and is_string($node['.permissions'])) {
				$permissions = explode_assoc("\n",":",$node['.permissions']);
				if (isset($permissions['all'])) $p = $permissions['all'];
				if (isset($permissions[$session->user])) $p = $permissions[$session->user];
			}
		} while ($step=array_shift($path) and isset($node[$step]) and ($node=&$node[$step])!==null);
		
		if ($step || $node===null) return setError("File not found");
		if ( $p and (($mode == 'w' and $p=='w') or ($mode == 'r' and ($p=='r' or $p=='w')) ) )
			return $node;
		else 
			return setError($mode=='w'?"Write access denied":"Read access denied");
	}

	/*
	private function cleanPermissions(&$c) {
		if (is_array($c)) {
			if (isset($c[".permissions"])) unset($c[".permissions"]);
			array_walk($c, array(&$this, 'cleanPermissions')); //Recurses to clean any subdirectories of $c
		}
	}*/
	
	//Given a path, builds a file tree holding all the files the user can see.
	public function getFolderCopy($path, Session &$session, &$skipped=false) {
		$path = $session->path($path);
		if ($path===false) return false;
		$node = $this->open($path, 'r', $session);
		if ($node===false) { $skipped=true; return array(); }
		else if (!is_array($node)) return $node;
		$out = array();
		foreach(array_keys($node) as $n) if ($n != '.permissions')
			$out[$n] = $this->getFolderCopy($path+array(count($path)=>$n), $session, $skipped);
		return $out;
	}
	
	public function write($path, $content, Session &$session, $overwrite=false) {
		//if (DEBUG) { echo "write: ".var_dump($path); var_dump($content);}
		//Convert the text path into an array of path parts
		$path = $session->path($path); 
		if ($path===false) return false;
		$file = array_pop($path);
		//Pre-emptively disable modifying any .permissions file...
		if ($file == '.permissions') return setError("Cannot modify .permissions files");
		//Try and open that directory to write the file in there...
		$node =& $this->open($path, 'w', $session);
		if ($node===false) return false;
		//Make sure it's actually a directory
		else if (!is_array($node)) return setError("Invalid path - cannot write file");
		//And unless overwrite enabled, make sure the file doesn't already exist...
		else if (!$overwrite and array_key_exists($file, $node)) return setError("File already exists!");
		//Is the data being written a folder - delete any .permissions files.
		//if (is_array($content)) $this->cleanPermissions($content);

		$node[$file] = $content;
		return true;
	}
	
	public function read($path, Session &$session) {
		return $this->open($path, 'r', $session);
	}

//-----------------------------------------------------------------------------------------------------------
// Users
//-----------------------------------------------------------------------------------------------------------
	public function addUser($user, $password) {
		$passwd_file =& $this->getNode('/etc/passwd');
		$users = explode_assoc("\n",":",$passwd_file);
		//Create a home directory if they don't already have one. (except for root... but root is created by default)
		if (!isset($users[$user])) {
		  $res = $this->createFiles(array($user=>array('.permissions'=>"all:\nroot:w\n$user:w")), '/home');
		  if (!$res) throw new Exception("Could not create home directory for $user");
		}
		//Create a line for them in the passwd file (overwrite an old password entry if it exists)
		$users[$user] = md5(SALT.$password);
		$passwd_file = implode_assoc("\n",":",$users);
	}
	
	//Given a username and password, checks to see if that user/password pair exists in the /.passwd file.
	//Returns true if it matches, false if a mismatch or the .passwd file is missing.
	public function authenticate($user, $password=null) {
		if (!$password) list($user, $password) = array_pad(explode(':', $user, 2), 2, null);
		$users = explode_assoc("\n",":",$this->getNode('/etc/passwd'));
		if (isset($users[$user]) && md5(SALT.$password) == $users[$user]) return $user;
		else return false;
	}

//-----------------------------------------------------------------------------------------------------------
// Tools
//-----------------------------------------------------------------------------------------------------------

	public function addTool($tool, $path='/bin/', $filename=null) {
		__autoload("Tool_$tool"); //Barfs if try to load a non-existent tool
		$r = $this->createFiles(array( ($filename?$filename:$tool)=>($tool.':'.md5(SALT.$tool)) ), $path);
		if (!$r) throw new Exception("Cannot create tool");
	}
	
	public static function isExecutable($file) {
	   if (!is_string($file)) return false;
	   list($tool, $hash) = array_pad(explode(":", $file), 2, null); 

	   return $hash === md5(SALT.$tool);
	}	
	
	public function getTool($cmd, Session &$session) {
		//if just the tool name, look in the /bin directory.
		if (strpos($cmd, '/')===false)
			$file =& $this->getNode('/bin/'.$cmd);
		else 	
		   $file = $this->read($cmd, $session);

		if (!$file) return setError("$cmd: command not found (type 'help' to see command list)");
		else if (is_object($file)) return setError("$cmd: service is already running.");
		else if (is_array($file)) return setError("$cmd: not a valid executable.");

		list($tool, $hash) = array_pad(explode(":", $file), 2, null); 
		//Is the hash right?
		if ($hash != md5(SALT.$tool)) return setError("$cmd: not a valid executable."); //Not a real 'executable'
		//Does the tool exist in the system?
		try { __autoload("Tool_$tool"); } catch (Exception $e) { return setError("$cmd: not a valid executable."); } //Not a real 'executable'
		
		return "Tool_$tool"; //Return a string with the tool class name.
	}
	
//-----------------------------------------------------------------------------------------------------------
// Services
//-----------------------------------------------------------------------------------------------------------

	public function addService($name, $args=null) {
		$class = "Service_$name";
		$service = new $class($this, $args); 
		//Store the object directly in the file system
		$this->createFiles(array($name=>&$service),'/boot/');
	}
	
	public function &getService($name) {
		//Services are only in the boot directory.
		if (strpos($name, '/')!==false) return setError("$name: Invalid service name");
		$s =& $this->getNode('/boot/'.$name); //Does it have a configuration entry in the boot folder?
		if ($s===false) return setError("$name: service not installed");
		return $s;
	}
	
//-----------------------------------------------------------------------------------------------------------
// Network stuff
//-----------------------------------------------------------------------------------------------------------
	
	public function addNetworkInterface($alias, $network, $type='NetworkInterface', $connect=true) {
		__autoload($type); //Barfs if try to load a non-existent interface type
		if($network != null and !is_string($network)) $network = $network->name;		
		//Create the interface
		$config = array('type'=>$type, 'network'=>$network, 'status'=>'down');
				
		//Should the interface (try to) start?
		//(If connect isn't false, use it as a potential password...)
		if ($connect) call_user_func_array(array($type, 'start'), array(&$this, &$config, $connect)); 
		
		//Save the configuration to the /dev/net folder
		$this->createFiles(array($alias=>implode_assoc("\n","=",$config)), '/dev/net/');
	}
		
	//Given the name of a computer attached to the same network, try to obtain it.
	public function &getNeighbourComputer($hostname) {
		$errors='';
		//Look at each interface on this computer, and if connected to a network check that network for that computer name.
		foreach($this->getNode('/dev/net') as $alias=>$c) {
			$config = explode_assoc("\n", "=", $c);
			$network =& Network::$networks[$config['network']];
			if ($config['status']!='up') 
				$errors .= "$alias is inactive.\n";
			else if (!$network) 
				$errors .= "$alias is misconfigured.\n";
			else if (!isset($network->computers[$hostname]))
				$errors .= "$alias: $hostname is unreachable.\n";
			//Computer found? Return it!
			else return $network->computers[$hostname];	
		}
		return setError(rtrim($errors)); //Computer not found...
	}


   public static function clear() {
       self::$computers = array();
   }
}
