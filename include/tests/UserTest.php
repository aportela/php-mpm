<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

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
            $u = new User();
            $u->set("", UserTest::ADMIN_EMAIL, UserTest::ADMIN_PASSWORD, "administrator", UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new User())->signout();
        }

        public function testExistsWithExistentEmail() {
            $u = new User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $this->assertTrue($u->exists());        
        }

        public function testExistsWithNotExistentEmail() {
            $u = new User();
            $u->email = UserTest::NON_EXISTENT_EMAIL;
            $this->assertFalse($u->exists());        
        }

        public function testExistsWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->exists();
        }

        public function testSignUpWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $u = new User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $u->signup();            
        }

        public function testSignUpWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->signup();            
        }


        public function testSignUpWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->signup();            
        }

        public function testSignUpWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $uuid = Utils::uuid();
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", "", UserType::DEFAULT);
            $u->signup();            
        }

        public function testSignUp() {
            $u = new User();
            $uuid = Utils::uuid();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", sprintf("Name: %s", $uuid), UserType::DEFAULT);
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
            $u = new User();
            $u->add();                    
        }

        // TODO
        public function testAddWithoutAuthAdminSession() {
            //$this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
        }

        public function testAddWithExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $u = new User();
            $u->set("", UserTest::EXISTENT_EMAIL, "password", "administrator", UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAddWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new User();
            $u->set("", "", "password", "administrator", UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new User();
            $uuid = Utils::uuid();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "password", "", UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAddWithEmptyPassword() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new User();
            $uuid = Utils::uuid();             
            $u->set($uuid, sprintf("%s@server.com", $uuid), "", "administrator", UserType::ADMINISTRATOR);
            $u->add();            
        }

        public function testAdd() {
            $this->signInAsAdmin();
            $err = null;
            try {
                $u = new User();
                $uuid = Utils::uuid();            
                $u->set($uuid, sprintf("%s@server.com", $uuid), "password", "administrator", UserType::ADMINISTRATOR);
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

        public function testLoginWithNotExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $u = new User();
            $u->email = UserTest::NON_EXISTENT_EMAIL;
            $u->password = "password";
            $u->login();                        
        }        

        public function testLoginWithExistentEmailAndInvalidPassword() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $u->password = Utils::uuid();
            $this->assertFalse($u->login());                        
        }        

        public function testLoginWithExistenEmailAndValidPassword() {
            $u = new User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $u->password = "password";
            $this->assertTrue($u->login());                        
        }        

        public function testSignOut() {
            $err = null;
            try {
                (new User())->signout();
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
            $u = new User();
            $u->email = UserTest::EXISTENT_EMAIL;
            $this->assertNotEmpty($u->generateRecoverAccountToken());            
        }

        public function testgenerateRecoverAccountTokenWithNotExistentEmail() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $u = new User();
            $u->email = UserTest::NON_EXISTENT_EMAIL;
            $u->generateRecoverAccountToken();            
        }

        public function testgenerateRecoverAccountTokenWithEmptyEmail() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $u = new User();
            $u->generateRecoverAccountToken();
        }

        public function testGetUserFromRecoverAccountToken() {
            $u = new User();
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
            $u = new User();
            $u->delete();
        }

        // TODO
        public function testDeleteWithoutAuthAdminSession() {
        }

        public function testDeleteWithoutId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $u = new User();
            $u->delete();
        }

        public function testDelete() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $uuid = Utils::uuid();
                $u = new User();             
                $u->set($uuid, sprintf("%s@server.com", $uuid), "password", sprintf("Name: %s", $uuid), UserType::DEFAULT);
                $u->signup();
                $u->delete();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                        
        }        
    }
?>