<?
require_once 'Common.php';
   
class Frank_Tools_TestCase extends Frank_Common_TestCase {
    protected $name = "Tools";
    
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
    
    public function testExecution() {
       //Valid executable
       $this->runCommandSequence(array('user'=>'frank'));
       
       //Load an invalid one and try to execute it.
       $this->runCommandSequence(array('./notes.txt'=>"<span class='error'>./notes.txt: not a valid executable.</span>"));
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
    
    public function testCp() {
       $this->runCommandSequence(array(
           'cp foo foo foo' => "<span class='error'>Too many arguments</span>",
           'cp' => "<span class='error'>You must specify a source file or folder to copy.</span>",
           'cp notes.txt' => "<span class='error'>You must specify a target to copy to.</span>",    
           'cp / /tmp' => "<span class='error'>Cannot copy /</span>",
           'cp /root/ /tmp/' => "<span class='error'>Source - Read access denied</span>", 
           'cp notes.txt foo.txt' => "notes.txt copied",
       ));       
       $this->assertNotEmpty($this->session->computer->getNode('/home/frank/foo.txt'), "notes.txt was actually copied");       
    }
    
    public function testFtp() {
        //The ftp server is the last computer in the network
        $network = Network::$networks['LAB_SECURE'];
        $lab_ftp = end($network->computers);

        //Connect as anonymous
        $this->runCommandSequence(array(
            "ftp $lab_ftp->name" => "Connected to ftpd - File server on $lab_ftp->name - as anonymous<br />",
            "help" => "FTP Commands are:\n",
            //Passes through to computer tools
            "pwd" => "/ftp", 
            "ls" => "Contents of /ftp:<br />\n&nbsp;<span style='color:#00f'>camera/</span><br />\n&nbsp;<span style='color:#00f'>isos/</span><br />\n&nbsp;<span style='color:#00f'>scratch/</span>",
            //Put function - not allowed for anonymous
            "put notes.txt" => "<span class='error'>Target error - Write access denied</span>",
            //Get function 
            "get scratch/bunny.jpg" => "File transferred successfully to /home/frank/bunny.jpg",         
        ));
        
        //Check - was the file transferred?
        $this->assertNotEmpty($this->session->computer->getNode('/home/frank/bunny.jpg'), "File was copied back to client computer");
        
        //Check - that there is a current FTP session        
        $this->assertNotEmpty($this->session->state, "Still connected");
        $this->assertNotEmpty($this->session->next, "Still connected");
        
        $this->runCommandSequence(array(
            "quit" => "Bye!",
            "ls" => "Contents of /home/frank"
        ));
        
        //Check that the ftp session was closed
        $this->assertEmpty($this->session->state, "Detached");
        $this->assertEmpty($this->session->next, "Detached");
            
             
        $lab_ftp->addUser("testuser","password");
        
        $this->runCommandSequence(array(
          "ftp $lab_ftp->name testuser:password" => "Connected to ftpd - File server on $lab_ftp->name - as testuser<br />",
          "put notes.txt" => "File transferred successfully to /ftp/notes.txt",
          "quit" => "Bye!"
        ));
    }
    
    public function testSsh() {
        $lab_server = end(Network::$networks['LAB_SECURE']->computers);
        $lab_server->addUser("testuser","password");
        
        $this->runCommandSequence(array( 
           "ssh $lab_server->name testuser" => "<span class='error'>Access denied - authentication failure</span>",
           "ssh $lab_server->name someone:password" => "<span class='error'>Access denied - authentication failure</span>",
        ));
        
        //TODO: all the other ssh tests
    }
    
    public function testPing() {
        $lab_server = end(Network::$networks['LAB_SECURE']->computers);       
        
        $this->runCommandSequence(array(
           "ping $lab_server->name" => "$lab_server->name responds",
           "ping -broadcast eth0" => "Accessible computers on network LAB_SECURE:",
           "ping -broadcast eth1" => "<span class='error'>Interface eth1 does not exist</span>"
        ));
        
        //TODO: all the other ssh tests
    }
    
    public function testFind() {
        $find_all = $this->runCommandSequence(array(
            "cd /tmp/" => "/tmp",
            "find" => "/tmp/test.txt<br />\n/tmp/mail/20110303.msg<br />\n/tmp/mail/20110215.msg<br />\n/tmp/mail<br />\n/tmp/hello<br />\n/tmp",
            "find -n=*.msg" => "/tmp/mail/20110303.msg<br />\n/tmp/mail/20110215.msg",
            "find -c=FRANK" => "/tmp/mail/20110303.msg",
            "find /" => "/"
        ));       
        $this->assertStringEndsWith("Results: 63<br />\n[".$this->session->computer->name."]>",$find_all->stdout);
    }
    
    //TODO: Testing all the other tools
 }
