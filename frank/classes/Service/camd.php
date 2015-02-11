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

class Service_camd extends Service {
	public static function loadImage($file) {
		$image = file($file, FILE_IGNORE_NEW_LINES);
		$height = count($image);
		if (!$height) throw new Exception("Image cannot be 0 lines high!");
		$width = strlen($image[0]);
		foreach($image as $num=>$line)
			if (strlen($line) > $width ) $width = strlen($line);
		return array($image, array($width, $height));		
	}
	
	private $image;
	private $w, $h;
	private $camera_x, $camera_y;
	private $fov_w, $fov_h;
	
	public function __construct(&$host, $args) {
		$this->image = $args['image'];
		list($this->w, $this->h) = $args['dimensions'];
		list($this->camera_x, $this->camera_y) = $args['camera'] ? $args['camera'] : array(0,0);
		list($this->fov_w, $this->fov_h) = $args['fov'] ? $args['fov'] : array(70,22);
		parent::__construct($host, $args);
	}
	
	public function getView() {
		$out = '';
		$out = '+' . str_repeat('-', $this->fov_w) . "+\n";
		for ($y=$this->camera_y; $y<$this->camera_y+$this->fov_h;$y++)
			//$out .= substr($this->image[$y], $this->camera_x, $this->fov_w) . "\n";
			$out .= '|' . substr($this->image[$y], $this->camera_x, $this->fov_w) . "|\n";
		$out .= '+' . str_repeat('-', $this->fov_w) . "+\n";
		return $out;
	}

	public function onConnection(Session &$client_session) {
		$client_session->state_info['service_info']['entry'] = 'run';	
		return new Response($client_session->next->sub('camd - Camera server on $name. User: $user') . "\n" . $this->getView() .":>", R_PLAIN);	
	}

	public function run(Session &$client_session, $input) {
		$args = explode(" ",strtolower(trim($input)));
		$user = $client_session->next->user;
		
		$cmd = array_shift($args);
		switch($cmd) {
			case "":
				return new Response(":>", R_PLAIN);
			case "v":
				return new Response($this->getView() . ":>", R_PLAIN);
			case "q":
				$this->disconnect($client_session);
				return new Response("Disconnected from {$this->host->name} - bye.\n");
			case "a":
				if ($user == 'anonymous') { $e = "Camera movement disallowed by anonymous user."; break; }
				$a = array_shift($args);
				if (!$a or !is_numeric($a)) { $e = "Invalid angle given."; break; }
				$this->camera_x = ($a < 0) ? 0 : ($a > $this->w - $this->fov_w) ? $this->w - $this->fov_w : floor($a);
				return new Response($this->getView() . "[Camera moved to angle ({$this->camera_x})] :>", R_PLAIN);
			case "l":
				if ($user == 'anonymous') { $e = "Camera movement disallowed by anonymous user."; break; }
				$this->camera_x -= 5;
				if ($this->camera_x < 0) $this->camera_x = 0;
				return new Response($this->getView() . "[Camera panned left ({$this->camera_x})] :>", R_PLAIN);
			case "r":
				if ($user == 'anonymous') { $e = "Camera movement disallowed by anonymous user."; break; }
				$this->camera_x += 5;
				if ($this->camera_x > $this->w - $this->fov_w) $this->camera_x = $this->w - $this->fov_w;
				return new Response($this->getView() . "[Camera panned right ({$this->camera_x})] :>", R_PLAIN);
			case "h":
				return new Response("Commands:\n".
				"v - View the camera feed.\n".
				"l - Pan the camera left.\n".
				"r - Pan the camera right.\n".
				"a N - Pan the camera to angle N.\n".
				"h - Show this message\n".
				"q - Close the connection.\n:>", R_PLAIN);
			default:
				$e = htmlspecialchars($input).": invalid command. (type h to see available commands)";
		}
		$r = Response::error($e);
		$r->stdout .= ":>";
		return $r;
	}			
}

?>
