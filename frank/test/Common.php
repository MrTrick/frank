<?
chdir(__DIR__.'/../');
require_once 'common.php';
spl_autoload_register('__autoload');
$_SERVER['REMOTE_ADDR'] = 'testing';

abstract class Frank_Common_TestCase extends PHPUnit_Framework_TestCase {	

    public function setUp() {
       //Wipe away any evidence from the previous test
       $_SESSION = array();
       $_COOKIE = array();
       foreach( glob("../stored/testing*") as $file ) @unlink($file);

       //Initialise a new session (start a new game)
       //(ob_start and ob_get_clean are needed to prevent phpunit from complaining about headers)
       ob_start();
       $this->init = Loader::init();
       $this->session =& Loader::getEntry();
       $content = ob_get_clean();
       //$this->assertEmpty( $content );
    }

    /**
     * Utility - run a list of commands, expecting responses to start with the given values
     * Variadic - allows multiple lists to be executed in sequence
     */
    protected function runCommandSequence($_cmds_responses) {
       foreach(func_get_args() as $cmds_responses)
       foreach($cmds_responses as $cmd=>$response) {
          $res = $this->session->execute($cmd);
          $this->assertStringStartsWith($response, $res->stdout, "Ran '$cmd', expected '$response'");
       }
       return $res; //Return the output of the last command
    }
}

