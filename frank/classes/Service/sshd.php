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
