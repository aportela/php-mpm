<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    ob_start();
    session_start();

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

        public function testLoginWithEmptyIdAndEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->login();                        
        }        

        public function testLoginWithNotExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $u = new User();
            $u->set("z-z-z-z-z-z-z-z", "", "", 0);
            $u->login();
        }        

        public function testLoginWithNotExistenEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $u = new User();
            $u->set("", "thisemaildonotexists@server.com", "", 0);
            $u->login();                        
        }        

        public function testLoginWithExistentIdAndInvalidPassword() {
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "WRONG_PASSWORD", 0);
            $this->assertFalse($u->login());
        }        

        public function testLoginWithExistenEmailAndInvalidPassword() {
            $u = new User();
            $u->set("", "admin@localhost", "WRONG_PASSWORD", 0);
            $this->assertFalse($u->login());                        
        }        

        public function testLoginWithExistentIdAndValidPassword() {
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "password", 0);
            $this->assertTrue($u->login());
        }        

        public function testLoginWithExistenEmailAndValidPassword() {
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $this->assertTrue($u->login());                        
        }        

        public function testIsAuthenticatedWithAuthSession() {
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "password", 0);
            $u->login();
            $this->assertTrue(User::isAuthenticated());
        }

        public function testIsAuthenticatedWithoutAuthSession() {
            $u = new User();
            $u->logout();
            $this->assertFalse(User::isAuthenticated());
        }

    }

?>