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

//'Generic client to be overriden for connecting to each service';
class Tool_Client extends Tool {
	//Return the name of the service that the client connects to.
	public static function service() { throw new Exception("Must override this function."); }

	/* Return help for this tool */
	public static function help($alias=null) {
		$class = "Tool_$alias";
		$service_name = call_user_func(array($class,'service'));
		return 
"$alias - ".call_user_func(array($class,'description'))."
<b>Usage:</b> $alias HOST [LOGIN:PASSWORD]
Attempt to connect to $service_name on the remote computer HOST.
The HOST must have an $service_name service running for it to accept the connection.
";
	}
	
	/*Run the command with the given args.*/
	public static function run($args, &$session, $class=null) {
		$service_name = call_user_func(array($class,'service'));	
		if (count($args) > 2) 
			return Response::error("Too many arguments.");
		else if (!$hostname = array_shift($args))
			return Response::error("You must define a host to connect to.");
		else if (!$host =& $session->computer->getNeighbourComputer($hostname))
			return Response::error("$hostname is unreachable.");
		else if (!$service =& $host->getService($service_name))
			return Response::error("$hostname does not respond.");
		else if (!$service->connect($session, array_shift($args)))
			return Response::error();
		//Connected, great!
		//Attach this tool to the session, store a reference to the service for next time, and execute it.
		$session->attach(array($class, 'execute'));
		$session->state_info['service'] =& $service;
		
		$out = $service->execute($session);
		$out->history='push';
		return $out;
	}
	
	/*If already running, execute the following sentence*/
	public static function execute($input, &$session) {
		//Get a response from the service
		$service =& $session->state_info['service'];
		$out = $service->execute($session, $input);
		if (!$out) $out = Response::error(); 
		
		//If the server disconnected, close...
		if (!isset($session->state_info['service'])) {
			$session->detach();
			$out->history='pop';
		}

		return $out;
	}


}

