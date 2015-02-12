<?
chdir(__DIR__.'/../');
require_once 'common.php';
spl_autoload_register('__autoload');
$_SERVER['REMOTE_ADDR'] = 'testing';
   
class Test_Tools_TestCase extends PHPUnit_Framework_TestCase {
    protected $name = "Tools";
    
    public function setUp() {
       //Wipe away any evidence from the previous test
       $_SESSION = array();
       $_COOKIE = array();
       foreach( glob("../stored/testing*") as $file ) @unlink($file);
    
       //Initialise a new session (start a new game)
       //(ob_start and ob_get_clean are needed to prevent phpunit from complaining about headers)
       ob_start(); 
       $this->init = Loader::init();
       $this->session = Loader::getEntry();
       $this->assertEmpty( ob_get_clean() ); 
    }
    
    /**
     * Utility - run a list of commands, expecting responses to start with the given values
     */
    protected function runCommandSequence($cmds_responses) {
       foreach($cmds_responses as $cmd=>$response) {
          $res = $this->session->execute($cmd);
          $this->assertStringStartsWith($response, $res->stdout, "Ran '$cmd', expected '$response'");
       }
    }
    
    public function testBasic() {
       //Check the initial response
       $this->assertInstanceOf('Response', $this->init);
       $this->assertNotEmpty(preg_match('/Welcome to lab machine lab_(\\d+)/', $this->init->stdout, $m));
       $this->assertTrue( $m[1] >= 1 && $m[1] <= 10 );
       
       //Check the session
       $this->assertInstanceOf('Session', $this->session);
       $this->assertInstanceOf('Computer', $this->session->computer);
       $this->assertEquals('lab_'.$m[1], $this->session->computer->name);
       $this->assertEquals('frank', $this->session->user);
       $this->assertEquals(array("home","frank"), $this->session->pwd);
       
       //Check the response
       $res = $this->session->execute('user');
       $this->assertInstanceOf('Response', $res);
       $this->assertStringStartsWith("frank<br />", $res->stdout);
    }
    
    public function testCat() {
       $this->runCommandSequence(array(
           'cat notes.txt' => "Be very careful with frank",
           'cat /tut/t1.0' => "<i>(This is a short tutorial ",
           'cat notes.txt crash.log' => "<span class='error'>Too many arguments</span>",
           'cat' => "<span class='error'>Missing argument, see 'help cat' for usage.",
           'cat /home' => "<span class='error'>/home is a folder - to view folder contents, use ls",
           'cat /root' => "<span class='error'>Read access denied"
       ));
       
       //TODO: How to trigger the 'file is not readable' response?
    }
    
    public function testCd() {
        $this->runCommandSequence(array(
           'cd foo foo' => "<span class='error'>Too many arguments</span>",
           'cd' => '/home/frank',
           'cd /../' => "<span class='error'>Invalid path</span>",
           'cd /nosuch' => "<span class='error'>File not found</span>",
           'cd /home/frank/notes.txt' => "<span class='error'>Not a folder</span>",
           'cd /' => '/',
           'cd tmp' => '/tmp',
           'cd ../home' => '/home',
           'cd ./frank/' => '/home/frank'
        ));
    }
    //TODO: Testing all the other tools
 }
    
    
       
