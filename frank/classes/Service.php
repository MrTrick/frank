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

abstract class Service {
	public $host;
	public $auth_required;
	
	//Build the service...
	public function __construct(Computer &$host, $args=null) {
		$this->host =& $host;
		$this->auth_required = $args['auth_required'];
	}

	//Connect to the service from a session, with the given credentials. Returns 'true' on success, 'false' (and sets error) on failure.
	public function connect(Session &$client_session, $credentials=null) {
		$session_data =& $client_session->state_info['service_info'];
		
		//If correct credentials provided, or none provided and none required, success!! Go to onConnection. (if no credentials, username is anonymous)
		if ( ($credentials and $user=$this->host->authenticate($credentials)) or (!$credentials and !$this->auth_required) ) {
			$client_session->next = new Session($this->host, $user?$user:'anonymous');
			$session_data['entry'] = 'onConnection';
			return true;
		}
		//If a password is required, and not given, go to a login prompt
		else if (!$credentials) {
			$session_data['entry'] = 'prompt';
			return true;
		}
		//Incorrect credentials...
		else {
			$this->disconnect($client_session); //Disconnect, if partially connected already (login prompt)
			return setError("Access denied - authentication failure");
		}
	}
	
	//Service entry point
	public function execute(Session &$client_session, $input=false) {
		//Check that the service is hooked into and accessible from the session, and an entry point is set...
		if ($this->connected($client_session))
			return $this->{$client_session->state_info['service_info']['entry']}($client_session, $input);
		//Otherwise, force a disconnection...
		$this->disconnect($client_session);
		return setError("Connection to {$this->host->name} lost");
	}
	
	//Prompt for a login name and password...
	public function prompt(Session &$client_session, $input=false) {
		$session_data =& $client_session->state_info['service_info'];
		//Need to get login name?
		if (!$session_data['user'] and $input===false)
			return new Response("login: ");
		//Need to get password?
		else if (!array_key_exists('user', $session_data)) {
			$session_data['user'] = $input;
			return new Response("password: ");
		}
		//Successfully connected?
		else if ($this->connect($client_session, $session_data['user'].":".$input))
			return $this->execute($client_session); //Run the onConnection method 
		//Failure...
		else return setError("Access denied - authentication failure"); //Error message set by connect()
	}

	//Overriden by each service to respond when first connected.
	public abstract function onConnection(Session &$client_session);
	
	//Boolean - returns true if $client_session is connected to this service
	public function connected(Session &$client_session) {
		return 
			$client_session->computer->getNeighbourComputer($this->host->name)
			and $client_session->state_info['service'] === $this
			and $client_session->state_info['service_info']['entry'];
	}
	
	//Disconnect the link to this service, and destroy the remote session.
	public function disconnect(&$client_session) {
		$client_session->next = null;
		unset($client_session->state_info['service']); //Kill the *reference* to the service...
		$client_session->state_info['service_info']=null;
	}
}
