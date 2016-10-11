<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    class UserTest extends \PHPUnit_Framework_TestCase {

        public function testExistsWithEmailExistent() {
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $this->assertTrue($u->exists());        
        }

        public function testExistsWithEmailNotExistent() {
            $u = new User();
            $u->set("", "thisemaildonotexists@server.com", "", 0);
            $this->assertFalse($u->exists());        
        }

        public function testExistsWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->exists();
        }
    }

?>