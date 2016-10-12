<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Group.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    ob_start();    

    class GroupTest extends \PHPUnit_Framework_TestCase {

        const EXISTENT_USER_ID = "00000000-0000-0000-0000-000000000000";
        const EXISTENT_GROUP_ID = "1111111-1111-1111-1111-111111111111";
        const EXISTENT_GROUP_NAME = "Public";

        const ADMIN_EMAIL = "admin@localhost";
        const ADMIN_PASSWORD = "password";

        public function __construct () { 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        private function signInAsAdmin() {
            $u = new User();
            $u->set("", GroupTest::ADMIN_EMAIL, GroupTest::ADMIN_PASSWORD, "administrator", UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new User())->signout();
        }
        
        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new Group();
            $g->exists();                    
        }

        // TODO
        public function testExistsWithoutAuthAdminSession() {
        }

        public function testExistsWithExistentId() {
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = GroupTest::EXISTENT_GROUP_ID;
            $this->assertTrue($g->exists());        
        }

        public function testExistsWithNotExistentId() {
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = Utils::uuid();
            $this->assertFalse($g->exists());        
        }

        public function testExistsWithExistentName() {
            $this->signInAsAdmin();
            $g = new Group();
            $g->name = GroupTest::EXISTENT_GROUP_NAME;
            $this->assertTrue($g->exists());        
        }

        public function testExistsWithNotExistentName() {
            $this->signInAsAdmin();
            $g = new Group();
            $g->name = Utils::uuid();
            $this->assertFalse($g->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new Group();
            $g->add();                    
        }
    
        // TODO
        public function testAddWithoutAuthAdminSession() {
        }

        public function testAddWithExistentId() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = GroupTest::EXISTENT_GROUP_ID; 
            $g->add();                                
        }

        public function testAddWithExistentName() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = Utils::uuid();
            $g->name = GroupTest::EXISTENT_GROUP_NAME; 
            $g->add();                                
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = Utils::uuid();
            $g->name = ""; 
            $g->add();                                
        }

        public function testAddWithoutUsers() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $g = new Group();
                $uuid = Utils::uuid();
                $g->set(
                    $uuid,
                    sprintf("Group name: %s", $uuid),
                    sprintf("Group description: %s", $uuid),
                    array()
                );
                $g->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }
        }

        public function testAddWithEmptyUserId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new Group();
            $uuid = Utils::uuid();
            $g->set(
                $uuid,
                sprintf("Group name: %s", $uuid),
                sprintf("Group description: %s", $uuid),
                array(
                    new User()
                )
            );
            $g->add();
        }

        public function testAddWithNonExistentUserId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new Group();
            $uuid = Utils::uuid();
            $u = new User();
            $u->id = Utils::uuid();
            $g->set(
                $uuid,
                sprintf("Group name: %s", $uuid),
                sprintf("Group description: %s", $uuid),
                array(
                    $u
                )
            );
            $g->add();
        }

        public function testAddWithExistentUserId() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $g = new Group();
                $uuid = Utils::uuid();
                $u = new User();
                $u->id = GroupTest::EXISTENT_USER_ID;
                $g->set(
                    $uuid,
                    sprintf("Group name: %s", $uuid),
                    sprintf("Group description: %s", $uuid),
                    array(
                        $u
                    )
                );
                $g->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testUpdateWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new Group();
            $g->update();
        }

        // TODO
        public function testUpdateWithoutAuthAdminSession() {
        }

        public function testUpdateWithEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->update();                                
        }        

        public function testUpdateWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = Utils::uuid();
            $g->update();                                
        }        

        public function testUpdateWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->set(
                GroupTest::EXISTENT_GROUP_ID,
                "",
                "optional group description",
                array()
            );
            $g->update();                                
        }

        public function testUpdateWithoutUsers() {            
            $err = null;
            try {
                $this->signInAsAdmin();            
                $g = new Group();
                $g->set(
                    GroupTest::EXISTENT_GROUP_ID,
                    GroupTest::EXISTENT_GROUP_NAME,
                    "optional group description",
                    array()
                );
                $g->update();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                                                                    
        }

        public function testUpdateWithEmptyUserId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->set(
                GroupTest::EXISTENT_GROUP_ID,
                GroupTest::EXISTENT_GROUP_NAME,
                "optional group description",
                array(
                    new User()
                )
            );
            $g->update();                                
        }

        public function testUpdateWithNonExistentUserId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $u = new User();
            $u->id = Utils::uuid();            
            $g = new Group();
            $g->set(
                GroupTest::EXISTENT_GROUP_ID,
                GroupTest::EXISTENT_GROUP_NAME,
                "optional group description",
                array(
                    $u
                )
            );
            $g->update();
        }

        public function testUpdateWithExistentUserId() {
            $err = null;
            try {
                $this->signInAsAdmin();                        
                $u = new User();
                $u->id = GroupTest::EXISTENT_USER_ID;            
                $g = new Group();
                $g->set(
                    GroupTest::EXISTENT_GROUP_ID,
                    GroupTest::EXISTENT_GROUP_NAME,
                    "optional group description",
                    array(
                        $u
                    )
                );
                $g->update();  
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }
        }

        public function testSearchWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            Group::search(0, 16);
        }

        public function testSearchWithAuthSession() {
            $this->signInAsAdmin();
            $results = Group::search(0, 16);
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($results));
        }

        public function testDeleteWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new Group();
            $g->delete();
        }

        // TODO
        public function testDeleteWithoutAuthAdminSession() {
        }

        public function testDelete() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $g = new Group();
                $uuid = Utils::uuid();
                $u = new User();
                $u->id = GroupTest::EXISTENT_USER_ID;
                $g->set(
                    $uuid,
                    sprintf("Group name: %s", $uuid),
                    sprintf("Group description: %s", $uuid),
                    array(
                        $u
                    )
                );
                $g->add();
                $g->delete();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                        
        }

        public function testGetWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new Group();
            $g->get();            
        }        

        // TODO
        public function testGetWithoutAuthAdminSession() {
        }

        public function testGetWithoutEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->get();                        
        }

        public function testGetWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = Utils::uuid();
            $g->get();                        
        }        

        public function testGetWithExistentId() {
            $this->signInAsAdmin();
            $g = new Group();
            $g->id = GroupTest::EXISTENT_GROUP_ID;
            $g->get();                        
            $this->assertEquals($g->name, "Public");                                    
        }        
    }
?>
