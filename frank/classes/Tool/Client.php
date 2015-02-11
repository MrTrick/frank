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

//'Generic client to be overriden for connecting to each service';
abstract class Tool_Client extends Tool {
	//Return the name of the service that the client connects to.
	public abstract static function service();

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
		if (!$session->state_info['service']) {
			$session->detach();
			$out->history='pop';
		}

		return $out;
	}


}
?>