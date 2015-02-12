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
       $res = $this->session->execute('cat notes.txt');
       $this->assertStringStartsWith("Be very careful with frank", $res->stdout);

       $res = $this->session->execute('cat /tut/t1.0');
       $this->assertStringStartsWith("<i>(This is a short tutorial ", $res->stdout);
       $this->assertTrue( $res->html_mode );
       
       $res = $this->session->execute('cat notes.txt crash.log');
       $this->assertStringStartsWith("<span class='error'>Too many arguments</span>", $res->stdout);
       
       $res = $this->session->execute('cat');
       $this->assertStringStartsWith("<span class='error'>Missing argument, see 'help cat' for usage.", $res->stdout);
       
       $res = $this->session->execute('cat /home');
       $this->assertStringStartsWith("<span class='error'>/home is a folder - to view folder contents, use ls", $res->stdout);
       
       $res = $this->session->execute('cat /root');
       $this->assertStringStartsWith("<span class='error'>Read access denied", $res->stdout);
       
       //TODO: How to trigger the 'file is not readable' response?
    }
    
    //TODO: Testing all the other tools
 }
    
    
       
