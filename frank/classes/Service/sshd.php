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

class Service_sshd extends Service {
	//Construct the service - authentication is required
	public function __construct(&$host, $args) {
		$args['auth_required'] = true;
		parent::__construct($host, $args);
	}

	public function onConnection(Session &$client_session) {
		$client_session->state_info['service_info']['entry'] = 'run';	
		return new Response($client_session->next->sub("Connected to sshd - Secure shell server on \$name\n".$client_session->next->prompt));
		//return new Response($client_session->next->getWelcome());
	}

	public function run(Session &$client_session, $input) {
		//If the session handles the request, return it.
		if ($r = $client_session->next->execute($input)) return $r;
		//If the cilent wants to exit, close the connection
		else if (trim($input) == 'exit') {
			$this->disconnect($client_session);
			return new Response("Disconnected from {$this->host->name}\n");
		}
		else throw new Exception("Session->execute returned false under unknown circumstances");
	}
}
