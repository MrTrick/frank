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

class Service_ftpd extends Service {
	//protected $start_directory;

	//Construct the service 
	public function __construct(&$host, $args) {
		parent::__construct($host, $args);
		$host->createFiles(array('ftp'=>array('.permissions'=>"all:w\nanonymous:r")));
		//if ($host->getNode($this->start_directory)===false) throw new Exception("Invalid start directory.");
		//$this->start_directory = $args['start_directory'];
	}

	public function onConnection(Session &$client_session) {
		$client_session->next->pwd = array('ftp'); //$client_session->next->path($this->start_directory);
		$client_session->state_info['service_info']['entry'] = 'run';	
		return new Response($client_session->next->sub("Connected to ftpd - File server on \$name - as \$user\nftp>"));
	}
	
	public function help(Session &$client_session, $args) {
		$cmd = array_shift($args);
		switch($cmd) {
			case 'help':
				return new Response("help - Help function\n".
				"<b>Usage:</b> help [COMMAND]\n".
				"Show help for using a particular COMMAND.\n".
				"If COMMAND is not specified, display a summary of available commands.\n");
			case 'quit':
				return new Response("quit - Close connection\n".
				"<b>Usage:</b> quit\n".
				"Close the ftp connection, and return to the local session ftp was called from.\n".
				"<b>Options:</b> None.\n");
			case 'get':
				return new Response("get - Receive file\n".
				"<b>Usage:</b> get SOURCE [TARGET]\n".
				"Download the SOURCE file to the client computer.\n".
				"<b>Examples:</b>\n".
				"&nbsp;&nbsp;&nbsp;<i>get file.txt</i>\n".
				"Download file.txt from the remote directory to the local directory, saving as file.txt\n\n".
				"&nbsp;&nbsp;&nbsp;<i>get /home/joe/file.txt</i>\n".
				"Download file.txt from the the remote /home/joe directory to the local directory, saving as file.txt\n\n".
				"&nbsp;&nbsp;&nbsp;<i>get file.txt /tmp/readme.txt</i>\n".
				"Download file.txt from the remote directory to the local /tmp directory, saving as readme.txt\n");
			case 'put':
				return new Response("put - Receive file\n".
				"<b>Usage:</b> put SOURCE [TARGET]\n".
				"Upload the SOURCE file to the client computer.\n".
				"<b>Examples:</b>\n".
				"&nbsp;&nbsp;&nbsp;<i>put file.txt</i>\n".
				"Upload file.txt from the local directory to the remote directory, saving as file.txt\n\n".
				"&nbsp;&nbsp;&nbsp;<i>put /home/joe/file.txt</i>\n".
				"Upload file.txt from the the local /home/joe directory to the remote directory, saving as file.txt\n\n".
				"&nbsp;&nbsp;&nbsp;<i>put file.txt /tmp/readme.txt</i>\n".
				"Upload file.txt from the local directory to the remote /tmp directory, saving as readme.txt\n");
			case 'cd':
			case 'ls':
			case 'pwd':
			case 'rm':
			case 'mkdir':
			case 'user':
				$cmd = $this->host->getTool($cmd, $client_session->next);
				return new Response( call_user_func_array(array($cmd,'help'), $args) );
			default:
				case '':
				return new Response( "FTP Commands are:\n".
				"cd    - Change directory\n".
				"get   - Download a file or folder from the server\n".
				"help  - Show this message\n".
				"ls    - Server directory listing tool\n".
				"mkdir - Create a folder on the server\n".
				"put   - Upload a file or folder to the server\n".
				"pwd   - List the current directory on the server\n".
				"quit  - Close the ftp connection\n".
				"rm    - Remove a file or folder on the server\n".
				"user  - Print the user's name\n", R_PLAIN);
		}
	}
	
	public function copy(Session &$from_session, Session &$to_session, $args) {
		//Parse the 'from' argument
		$from = array_shift($args);
		if (!$from) return Response::error("Proper usage: (get|put) SOURCE [TARGET]");
		
		//Parse the 'to' argument - if not specified, use the file named by 'to'...
		$to = array_shift($args);
		if (!$to) $to = array_pop($from_session->path($from)); //To is the last element of the from path...
		
		//Try and read the source file...
		$file = $from_session->computer->read($from, $from_session);
		if ($file===false) return Response::error("Source error - ".getError());
		//Try and write it to the client...
		if (!$to_session->computer->write($to, $file, $to_session)) 
			return Response::error("Target error - ".getError());
	
		return new Response("File transferred successfully to /".implode('/',$to_session->path($to))."\n");
	}
	
	public function run(Session &$client_session, $input) {
		$args = preg_split("/\s+/", trim($input));
		$first = array_shift($args);
		//What sort of command was received?
		switch($first) {
			case 'help': //Show the help...
				$out = $this->help($client_session, $args); break;
			case 'quit':  
				$this->disconnect($client_session); 
				return new Response("Bye!\n"); 
			case 'get': 
				$out = $this->copy($client_session->next, $client_session, $args); break;
			case 'put': 
				$out = $this->copy($client_session, $client_session->next, $args); break;
			case 'cd':
			case 'ls':
			case 'pwd':
			case 'rm':
			case 'mkdir':
			case 'user': 
				$cmd = $this->host->getTool($first, $client_session->next);
				$out = call_user_func_array(array($cmd, 'run'), array($args, &$client_session->next));
				break;
			case '': //If no command given, return a blank response.
				$out = new Response(''); break;
			default:
				$out = Response::error("Invalid command");
		}
		$out->stdout.="ftp>";
		return $out;
	}
}



?>