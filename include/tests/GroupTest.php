<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "configuration.php";

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
            $u = new \PHP_MPM\User();
            $u->set("", GroupTest::ADMIN_EMAIL, GroupTest::ADMIN_PASSWORD, "administrator", \PHP_MPM\UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new \PHP_MPM\User())->signout();
        }
        
        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new \PHP_MPM\Group();
            $g->exists();                    
        }

        // TODO
        public function testExistsWithoutAuthAdminSession() {
        }

        public function testExistsWithExistentId() {
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = GroupTest::EXISTENT_GROUP_ID;
            $this->assertTrue($g->exists());        
        }

        public function testExistsWithNotExistentId() {
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = \PHP_MPM\Utils::uuid();
            $this->assertFalse($g->exists());        
        }

        public function testExistsWithExistentName() {
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->name = GroupTest::EXISTENT_GROUP_NAME;
            $this->assertTrue($g->exists());        
        }

        public function testExistsWithNotExistentName() {
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->name = \PHP_MPM\Utils::uuid();
            $this->assertFalse($g->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new \PHP_MPM\Group();
            $g->add();                    
        }
    
        // TODO
        public function testAddWithoutAuthAdminSession() {
        }

        public function testAddWithExistentId() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = GroupTest::EXISTENT_GROUP_ID; 
            $g->add();                                
        }

        // TODO
        public function testAddWithExistentName() {
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = \PHP_MPM\Utils::uuid();
            $g->name = ""; 
            $g->add();                                
        }

        public function testAddWithoutUsers() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $g = new \PHP_MPM\Group();
                $uuid = \PHP_MPM\Utils::uuid();
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
            $g = new \PHP_MPM\Group();
            $uuid = \PHP_MPM\Utils::uuid();
            $g->set(
                $uuid,
                sprintf("Group name: %s", $uuid),
                sprintf("Group description: %s", $uuid),
                array(
                    new \PHP_MPM\User()
                )
            );
            $g->add();
        }

        public function testAddWithNonExistentUserId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $uuid = \PHP_MPM\Utils::uuid();
            $u = new \PHP_MPM\User();
            $u->id = \PHP_MPM\Utils::uuid();
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
                $g = new \PHP_MPM\Group();
                $uuid = \PHP_MPM\Utils::uuid();
                $u = new \PHP_MPM\User();
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
            $g = new \PHP_MPM\Group();
            $g->update();
        }

        // TODO
        public function testUpdateWithoutAuthAdminSession() {
        }

        public function testUpdateWithEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->update();                                
        }        

        public function testUpdateWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = \PHP_MPM\Utils::uuid();
            $g->update();                                
        }        

        public function testUpdateWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
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
                $g = new \PHP_MPM\Group();
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
            $g = new \PHP_MPM\Group();
            $g->set(
                GroupTest::EXISTENT_GROUP_ID,
                GroupTest::EXISTENT_GROUP_NAME,
                "optional group description",
                array(
                    new \PHP_MPM\User()
                )
            );
            $g->update();                                
        }

        public function testUpdateWithNonExistentUserId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $u = new \PHP_MPM\User();
            $u->id = \PHP_MPM\Utils::uuid();            
            $g = new \PHP_MPM\Group();
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
                $u = new \PHP_MPM\User();
                $u->id = GroupTest::EXISTENT_USER_ID;            
                $g = new \PHP_MPM\Group();
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
            $data = Group::search(0, 16);
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($data->results));
        }

        public function testDeleteWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $g = new \PHP_MPM\Group();
            $g->delete();
        }

        // TODO
        public function testDeleteWithoutAuthAdminSession() {
        }

        public function testDelete() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $g = new \PHP_MPM\Group();
                $uuid = \PHP_MPM\Utils::uuid();
                $u = new \PHP_MPM\User();
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
            $g = new \PHP_MPM\Group();
            $g->get();            
        }        

        // TODO
        public function testGetWithoutAuthAdminSession() {
        }

        public function testGetWithoutEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->get();                        
        }

        public function testGetWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = \PHP_MPM\Utils::uuid();
            $g->get();                        
        }        

        public function testGetWithExistentId() {
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = GroupTest::EXISTENT_GROUP_ID;
            $g->get();                        
            $this->assertEquals($g->name, "Public");                                    
        }        
    }
?>
