<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    ob_start();    

    class UserTest extends \PHPUnit_Framework_TestCase {

        public function testExistsWithExistentEmail() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $this->assertTrue($u->exists());        
        }

        public function testExistsWithNotExistentEmail() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "thisemaildonotexists@server.com", "", 0);
            $this->assertFalse($u->exists());        
        }

        public function testExistsWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->exists();
        }

        public function testSignUpWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $u->signup();            
        }

        public function testSignUpWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signup();            
        }

        public function testSignUpWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signup();            
        }

        public function testSignUp() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();            
            $u->set(Utils::uuid(), sprintf("%s@server.com", Utils::uuid()), "password", 0);
            $err = null;
            try {
                $u->signup();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->add();                    
        }

        public function testAddWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user            
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $lu = new User();
            $lu->set("", "admin@localhost", "password", 0);
            $lu->login();                        
            $u = new User();
            $a->add();
            */                                
        }

        public function testAddWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $lu = new User();
            $lu->set("", "admin@localhost", "password", 0);
            $lu->login();            
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $u->add();            
        }

        public function testAddWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $lu = new User();
            $lu->set("", "admin@localhost", "password", 0);
            $lu->login();                        
            $u = new User();
            $u->add();            
        }

        public function testAddWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $lu = new User();
            $lu->set("", "admin@localhost", "password", 0);
            $lu->login();                        
            $u = new User();
            $u->add();            
        }

        public function testAdd() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $lu = new User();
            $lu->set("", "admin@localhost", "password", 0);
            $lu->login();                        
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->login();                        
        }        

        public function testLoginWithNotExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("z-z-z-z-z-z-z-z", "", "", 0);
            $u->login();
        }        

        public function testLoginWithNotExistenEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "thisemaildonotexists@server.com", "", 0);
            $u->login();                        
        }        

        public function testLoginWithExistentIdAndInvalidPassword() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "WRONG_PASSWORD", 0);
            $this->assertFalse($u->login());
        }        

        public function testLoginWithExistenEmailAndInvalidPassword() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "WRONG_PASSWORD", 0);
            $this->assertFalse($u->login());                        
        }        

        public function testLoginWithExistentIdAndValidPassword() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "password", 0);
            $this->assertTrue($u->login());
        }        

        public function testLoginWithExistenEmailAndValidPassword() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $this->assertTrue($u->login());                        
        }        

        public function testSignOut() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();            
            $err = null;
            try {
                $u->signout();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testIsAuthenticatedWithAuthSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "password", 0);
            $u->login();
            $this->assertTrue(User::isAuthenticated());
        }

        public function testIsAuthenticatedWithoutAuthSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            $this->assertFalse(User::isAuthenticated());
        }

        public function testIsAuthenticatedAsAdminWithAdminSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "password", 0);
            $u->login();
            $this->assertTrue(User::isAuthenticatedAsAdmin());
        }

        public function testIsAuthenticatedAsAdminWithoutAdminSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            // TODO: example normal (not admin) user
        }

        public function testGetSessionUserIdWithSessionStarted() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("00000000-0000-0000-0000-000000000000", "", "password", 0);
            $u->login();
            $this->assertEquals("00000000-0000-0000-0000-000000000000", User::getSessionUserId());
        }

        public function testGetSessionUserIdWithoutSessionStarted() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $this->assertNull(User::getSessionUserId());            
        }

        public function testgenerateRecoverAccountTokenWithExistentEmail() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "", 0);            
            $this->assertNotEmpty($u->generateRecoverAccountToken());            
        }

        public function testgenerateRecoverAccountTokenWithNotExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "thisemaildonotexists@server.com", "", 0);
            $u->generateRecoverAccountToken();            
        }

        public function testgenerateRecoverAccountTokenWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "", "", 0);            
            $u->generateRecoverAccountToken();
        }

        public function testGetUserFromRecoverAccountToken() {
            // TODO: NOT WORKING!
            /*
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "", 0);
            $token = $u->generateRecoverAccountToken();
            $tmpUser = User::getUserFromRecoverAccountToken($token);
            $this->assertEquals($u->email, $tmpUser["email"]);         
            */
        }

        public function testSearchWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            User::search(0, 16);
        }

        public function testSearchWithAuthSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $results = User::search(0, 16);
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($results));
        }
    }

?>