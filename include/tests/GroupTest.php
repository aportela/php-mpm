<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Group.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    ob_start();    

    class GroupTest extends \PHPUnit_Framework_TestCase {

        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $g = new Group();
            $g->set(
                "",
                "",
                "",
                array()
                );
            $g->exists();                    
        }

        public function testExistsWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            // TODO: normal (non admin user)
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                "",
                "",
                "",
                array()
                );
            $g->exists();
            */                    
        }

        public function testExistsWithExistentId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->set(
                "1111111-1111-1111-1111-111111111111",
                "",
                "",
                array()
                );
            $this->assertTrue($g->exists());        
        }

        public function testExistsWithNotExistentId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->set(
                "z-z-z--z-z-z-z",
                "",
                "",
                array()
                );
            $this->assertFalse($g->exists());        
        }

        public function testExistsWithExistentName() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->set(
                "",
                "Public",
                "",
                array()
                );
            $this->assertTrue($g->exists());        
        }

        public function testExistsWithNotExistentName() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->set(
                "",
                "this_group_Name_DO_NOT_exISt__--",
                "",
                array()
                );
            $g->exists(); 
            $this->assertFalse($g->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $g = new Group();
            $g->set(
                Utils::uuid(),
                "Name",
                "Description",
                array()
            );
            $g->add();                    
        }

        public function testAddWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user            
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $g = new Group();
            $g->set(
                Utils::uuid(),
                "Name",
                "Description",
                array()
            );
            $g->add();                    
            */
        }

        public function testAddWithExistentId() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                "1111111-1111-1111-1111-111111111111",
                "New group name",
                "New group description",
                array()
            );
            $g->add();                                
        }

        public function testAddWithExistentName() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                Utils::uuid(),
                "Public",
                "New group description",
                array()
            );
            $g->add();                                
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                Utils::uuid(),
                "",
                "New group description",
                array()
            );
            $g->add();                                
        }

        public function testAddWithoutUsers() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();            
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $uuid = Utils::uuid();
            $nonExistentUserId = new User();
            $nonExistentUserId->set("z-z-z-z-z-", "", "", 0);
            $g->set(
                $uuid,
                sprintf("Group name: %s", $uuid),
                sprintf("Group description: %s", $uuid),
                array(
                    $nonExistentUserId
                )
            );
            $g->add();
        }

        public function testAddWithExistentUserId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {
                $err = null;
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();            
                $g = new Group();
                $uuid = Utils::uuid();
                $existentUserId = new User();
                $existentUserId->set("00000000-0000-0000-0000-000000000000", "", "", 0);
                $g->set(
                    $uuid,
                    sprintf("Group name: %s", $uuid),
                    sprintf("Group description: %s", $uuid),
                    array(
                        $existentUserId
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $g = new Group();
            $g->update();
        }

        public function testUpdateWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user                        
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $g = new Group();
            $g->update();
            */
        }

        public function testUpdateWithEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                "",
                "Existent group name",
                "Existent group description",
                array()
            );
            $g->update();                                
        }        

        public function testUpdateWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                "-z-z-z-z-z-z-z",
                "Existent group name",
                "Existent group description",
                array()
            );
            $g->update();                                
        }        

        public function testUpdateWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                "1111111-1111-1111-1111-111111111111",
                "",
                "optional group description",
                array()
            );
            $g->update();                                
        }

        public function testUpdateWithoutUsers() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {            
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();            
                $g = new Group();
                $g->set(
                    "1111111-1111-1111-1111-111111111111",
                    "Public",
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $g = new Group();
            $g->set(
                "1111111-1111-1111-1111-111111111111",
                "Public",
                "optional group description",
                array(
                    new User()
                )
            );
            $g->update();                                
        }

        public function testUpdateWithNonExistentUserId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $nonExistentUserId = new User();
            $nonExistentUserId->set("z-z-z-z-z-", "", "", 0);
            $g = new Group();
            $g->set(
                "1111111-1111-1111-1111-111111111111",
                "Public",
                "optional group description",
                array(
                    $nonExistentUserId
                )
            );
            $g->update();
        }

        public function testUpdateWithExistentUserId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {                        
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();            
                $existentUserId = new User();
                $existentUserId->set("00000000-0000-0000-0000-000000000000", "", "", 0);
                $g = new Group();
                $g->set(
                    "1111111-1111-1111-1111-111111111111",
                    "Public",
                    "optional group description",
                    array(
                        $existentUserId
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            Group::search(0, 16);
        }

        public function testSearchWithAuthSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $results = Group::search(0, 16);
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($results));
        }

        public function testDeleteWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            $g = new Group();
            $g->delete();
        }

        public function testDeleteWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user            
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            $g = new Group();
            $g->delete();
            */            
        }

        public function testDelete() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {
                $err = null;
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();
                $g = new Group();
                $uuid = Utils::uuid();
                $existentUserId = new User();
                $existentUserId->set("00000000-0000-0000-0000-000000000000", "", "", 0);
                $g->set(
                    $uuid,
                    sprintf("Group name: %s", $uuid),
                    sprintf("Group description: %s", $uuid),
                    array(
                        $existentUserId
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            $g = new Group();
            $g->get();            
        }        

        public function testGetWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user                        
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->signout();
            $g = new Group();
            $g->get();
            */            
        }

        public function testGetWithoutEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->get();                        
        }

        public function testGetWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->set("z-z-z-z-z-z-z", "", "", array());
            $g->get();                                    
        }        

        public function testGetWithExistentId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $g = new Group();
            $g->set("1111111-1111-1111-1111-111111111111", "", "", array());
            $g->get();
            $this->assertEquals($g->name, "Public");                                    
        }        
    }
?>