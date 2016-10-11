<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    class UserTest extends \PHPUnit_Framework_TestCase {

        public function testExistsWithExistentEmail() {
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $this->assertTrue($u->exists());        
        }

        public function testExistsWithNotExistentEmail() {
            $u = new User();
            $u->set("", "thisemaildonotexists@server.com", "", 0);
            $this->assertFalse($u->exists());        
        }

        public function testExistsWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->exists();
        }

        public function testAddWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $u->add();            
        }

        public function testAddWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->add();            
        }

        public function testAddWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->add();            
        }

        public function testAdd() {
            $u = new User();            
            $u->set(Utils::uuid(), sprintf("%s@server.com", Utils::uuid()), "password", 0);
            $err = null;
            try {
                $u->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }        
    }

?>