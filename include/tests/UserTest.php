<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "configuration.php";

    ob_start();    

    class UserTest extends \PHPUnit_Framework_TestCase {

        const EXISTENT_EMAIL = "admin@localhost";
        const NON_EXISTENT_EMAIL =  "thisemaildonotexists@server.com";
        const ADMIN_USER_ID = "00000000-0000-0000-0000-000000000000";
        const ADMIN_EMAIL = "admin@localhost";
        const ADMIN_PASSWORD = "password";

        public function __construct () { 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        private function signInAsAdmin() {
            $u = new \PHP_MPM\User();
            $u->set("", UserTest::ADMIN_EMAIL, UserTest::ADMIN_PASSWORD, "administrator", \PHP_MPM\UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new \PHP_MPM\User())->signout();
        }

        public function testExistsWithExistentEmail() {
            $u = new \PHP_MPM\User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $this->assertTrue($u->exists());        
        }

        public function testExistsWithNotExistentEmail() {
            $u = new \PHP_MPM\User();
            $u->email = UserTest::NON_EXISTENT_EMAIL;
            $this->assertFalse($u->exists());        
        }

        public function testExistsWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new \PHP_MPM\User();
            $u->exists();
        }

        public function testSignUpWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $u = new \PHP_MPM\User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $u->signup();            
        }

        public function testSignUpWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new \PHP_MPM\User();
            $u->signup();            
        }


        public function testSignUpWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new \PHP_MPM\User();
            $u->signup();            
        }

        public function testSignUpWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new \PHP_MPM\User();
            $uuid = \PHP_MPM\Utils::uuid();
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", "", \PHP_MPM\UserType::DEFAULT);
            $u->signup();            
        }

        public function testSignUp() {
            $u = new \PHP_MPM\User();
            $uuid = \PHP_MPM\Utils::uuid();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", sprintf("Name: %s", $uuid), \PHP_MPM\UserType::DEFAULT);
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
            $this->signOut();
            $u = new \PHP_MPM\User();
            $u->add();                    
        }

        // TODO
        public function testAddWithoutAuthAdminSession() {
            //$this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
        }

        public function testAddWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $u = new \PHP_MPM\User();
            $u->set("", UserTest::EXISTENT_EMAIL, "password", "administrator", \PHP_MPM\UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAddWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new \PHP_MPM\User();
            $u->set("", "", "password", "administrator", \PHP_MPM\UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new \PHP_MPM\User();
            $uuid = \PHP_MPM\Utils::uuid();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", "", \PHP_MPM\UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAddWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new \PHP_MPM\User();
            $uuid = \PHP_MPM\Utils::uuid();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "", "administrator", \PHP_MPM\UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAdd() {
            $this->signInAsAdmin();
            $err = null;
            try {
                $u = new \PHP_MPM\User();
                $uuid = \PHP_MPM\Utils::uuid();            
                $u->set($uuid, sprintf("%s@server.com", $uuid), "password", "administrator", \PHP_MPM\UserType::ADMINISTRATOR);
                $u->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testLoginWithEmptyIdAndEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new \PHP_MPM\User();
            $u->login();                        
        }        

        public function testLoginWithNotExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $u = new \PHP_MPM\User();
            $u->email = UserTest::NON_EXISTENT_EMAIL;
            $u->password = "password";
            $u->login();                        
        }        

        public function testLoginWithExistentEmailAndInvalidPassword() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new \PHP_MPM\User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $u->password = \PHP_MPM\Utils::uuid();
            $this->assertFalse($u->login());                        
        }        

        public function testLoginWithExistenEmailAndValidPassword() {
            $u = new \PHP_MPM\User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $u->password = "password";
            $this->assertTrue($u->login());                        
        }        

        public function testSignOut() {
            $err = null;
            try {
                (new \PHP_MPM\User())->signout();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testIsAuthenticatedWithAuthSession() {
            $this->signInAsAdmin();
            $this->assertTrue(User::isAuthenticated());
        }

        public function testIsAuthenticatedWithoutAuthSession() {
            $this->signOut();
            $this->assertFalse(User::isAuthenticated());
        }

        public function testIsAuthenticatedAsAdminWithAdminSession() {
            $this->signInAsAdmin();
            $this->assertTrue(User::isAuthenticatedAsAdmin());
        }

        // TODO
        public function testIsAuthenticatedAsAdminWithoutAdminSession() {
        }

        public function testGetSessionUserIdWithSessionStarted() {
            $this->signInAsAdmin();
            $this->assertEquals(UserTest::ADMIN_USER_ID, User::getSessionUserId());
        }

        public function testGetSessionUserIdWithoutSessionStarted() {
            $this->signOut();
            $this->assertNull(User::getSessionUserId());            
        }

        public function testgenerateRecoverAccountTokenWithExistentEmail() {
            $u = new \PHP_MPM\User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $this->assertNotEmpty($u->generateRecoverAccountToken());            
        }

        public function testgenerateRecoverAccountTokenWithNotExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $u = new \PHP_MPM\User();
            $u->email = UserTest::NON_EXISTENT_EMAIL;
            $u->generateRecoverAccountToken();            
        }

        public function testgenerateRecoverAccountTokenWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new \PHP_MPM\User();
            $u->generateRecoverAccountToken();
        }

        public function testGetUserFromRecoverAccountToken() {
            $u = new \PHP_MPM\User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $token = $u->generateRecoverAccountToken();                        
            $tmpUser = User::getUserFromRecoverAccountToken($token);
            $this->assertEquals($u->email, $tmpUser["email"]);         
        }

        public function testSearchWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            User::search(1, 16);
        }

        public function testSearchWithAuthSession() {
            $this->signInAsAdmin();
            $results = User::search(0, 16);
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($results));
        }

        public function testDeleteWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $u = new \PHP_MPM\User();
            $u->delete();
        }

        // TODO
        public function testDeleteWithoutAuthAdminSession() {
        }

        public function testDeleteWithoutId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new \PHP_MPM\User();
            $u->delete();
        }

        public function testDeleteOwnUser() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $uuid = \PHP_MPM\Utils::uuid();
            $u = new \PHP_MPM\User();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", sprintf("Name: %s", $uuid), \PHP_MPM\UserType::ADMINISTRATOR);
            $u->add();
            $this->signOut();            
            $u->login();
            $u->delete();
        }        

        public function testDelete() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $uuid = \PHP_MPM\Utils::uuid();
                $u = new \PHP_MPM\User();             
                $u->set($uuid, sprintf("%s@server.com", $uuid), "password", sprintf("Name: %s", $uuid), \PHP_MPM\UserType::DEFAULT);
                $u->add();
                $u->delete();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                        
        }        
    }
?>