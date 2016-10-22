<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "configuration.php";

    ob_start();    

    class TemplateTest extends \PHPUnit_Framework_TestCase {

        const EXISTENT_GROUP_ID = "1111111-1111-1111-1111-111111111111";
        const EXISTENT_TEMPLATE_ID = "2222222-1111-2222-1111-222222222222";
        const EXISTENT_TEMPLATE_NAME = "Bills";

        const ADMIN_EMAIL = "admin@localhost";
        const ADMIN_PASSWORD = "password";

        public function __construct () { 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        private function signInAsAdmin() {
            $u = new \PHP_MPM\User();
            $u->set("", TemplateTest::ADMIN_EMAIL, TemplateTest::ADMIN_PASSWORD, "administrator", \PHP_MPM\UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new \PHP_MPM\User())->signout();
        }
        
        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $t = new \PHP_MPM\Template();
            $t->exists();                    
        }

        // TODO
        public function testExistsWithoutAuthAdminSession() {
        }

        public function testExistsWithExistentId() {
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = TemplateTest::EXISTENT_TEMPLATE_ID;
            $this->assertTrue($t->exists());        
        }

        public function testExistsWithNotExistentId() {
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = \PHP_MPM\Utils::uuid();
            $this->assertFalse($t->exists());        
        }

        public function testExistsWithExistentName() {
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->name = TemplateTest::EXISTENT_TEMPLATE_NAME;
            $this->assertTrue($t->exists());        
        }

        public function testExistsWithNotExistentName() {
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->name = \PHP_MPM\Utils::uuid();
            $this->assertFalse($t->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $t = new \PHP_MPM\Template();
            $t->add();                    
        }
    
        // TODO
        public function testAddWithoutAuthAdminSession() {
        }

        public function testAddWithExistentId() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = TemplateTest::EXISTENT_TEMPLATE_ID; 
            $t->add();                                
        }

        // TODO
        public function testAddWithExistentName() {
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = \PHP_MPM\Utils::uuid();
            $t->name = ""; 
            $t->add();                                
        }

        public function testAddWithoutGroups() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $t = new \PHP_MPM\Template();
                $uuid = \PHP_MPM\Utils::uuid();
                $t->set(
                    $uuid,
                    sprintf("Template name: %s", $uuid),
                    sprintf("Template description: %s", $uuid),
                    array()
                );
                $t->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }
        }

        public function testAddWithEmptyGroupId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $uuid = \PHP_MPM\Utils::uuid();
            $t->set(
                $uuid,
                sprintf("Template name: %s", $uuid),
                sprintf("Template description: %s", $uuid),
                array(
                    new \PHP_MPM\TemplatePermission()
                )
            );
            $t->add();
        }

        public function testAddWithNonExistentGroupId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $uuid = \PHP_MPM\Utils::uuid();
            $g = new \PHP_MPM\Group();            
            $g->id = \PHP_MPM\Utils::uuid();
            $p = new \PHP_MPM\TemplatePermission();
            $p->group = $g;
            $t->set(
                $uuid,
                sprintf("Template name: %s", $uuid),
                sprintf("Template description: %s", $uuid),
                array(
                    $p
                )
            );
            $t->add();
        }

        public function testAddWithExistentGroupId() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $t = new \PHP_MPM\Template();
                $uuid = \PHP_MPM\Utils::uuid();
                $g = new \PHP_MPM\Group();
                $g->id = TemplateTest::EXISTENT_GROUP_ID;
                $p = new \PHP_MPM\TemplatePermission();
                $p->group = $g;                
                $t->set(
                    $uuid,
                    sprintf("Template name: %s", $uuid),
                    sprintf("Template description: %s", $uuid),
                    array(
                        $p
                    )
                );
                $t->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testUpdateWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $t = new \PHP_MPM\Template();
            $t->update();
        }

        // TODO
        public function testUpdateWithoutAuthAdminSession() {
        }

        public function testUpdateWithEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->update();                                
        }        

        public function testUpdateWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = \PHP_MPM\Utils::uuid();
            $t->update();                                
        }        

        public function testUpdateWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->set(
                TemplateTest::EXISTENT_TEMPLATE_ID,
                "",
                "optional template description",
                array()
            );
            $t->update();                                
        }

        public function testUpdateWithoutGroups() {            
            $err = null;
            try {
                $this->signInAsAdmin();            
                $t = new \PHP_MPM\Template();
                $t->set(
                    TemplateTest::EXISTENT_TEMPLATE_ID,
                    TemplateTest::EXISTENT_TEMPLATE_NAME,
                    "optional template description",
                    array()
                );
                $t->update();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                                                                    
        }

        public function testUpdateWithEmptyGroupId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->set(
                TemplateTest::EXISTENT_TEMPLATE_ID,
                TemplateTest::EXISTENT_TEMPLATE_NAME,
                "optional template description",
                array(
                    new \PHP_MPM\TemplatePermission()
                )
            );
            $t->update();                                
        }

        public function testUpdateWithNonExistentGroupId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $g = new \PHP_MPM\Group();
            $g->id = \PHP_MPM\Utils::uuid();
            $p = new \PHP_MPM\TemplatePermission();
            $p->group = $g;                                        
            $t = new \PHP_MPM\Template();
            $t->set(
                TemplateTest::EXISTENT_TEMPLATE_ID,
                TemplateTest::EXISTENT_TEMPLATE_NAME,
                "optional template description",
                array(
                    $p
                )
            );
            $t->update();
        }

        public function testUpdateWithExistentGroupId() {
            $err = null;
            try {
                $this->signInAsAdmin();                        
                $g = new \PHP_MPM\Group();
                $g->id = TemplateTest::EXISTENT_GROUP_ID;
                $p = new \PHP_MPM\TemplatePermission();
                $p->group = $g;                                            
                $t = new \PHP_MPM\Template();
                $t->set(
                    TemplateTest::EXISTENT_TEMPLATE_ID,
                    TemplateTest::EXISTENT_TEMPLATE_NAME,
                    "optional template description",
                    array(
                        $p
                    )
                );
                $t->update();  
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }
        }

        public function testSearchWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            Template::search(0, 16, "");
        }

        public function testSearchWithAuthSession() {
            $this->signInAsAdmin();
            $data = Template::search(0, 16, "");
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($data->results));
        }

        public function testDeleteWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $t = new \PHP_MPM\Template();
            $t->delete();
        }

        // TODO
        public function testDeleteWithoutAuthAdminSession() {
        }

        public function testDelete() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $t = new \PHP_MPM\Template();
                $uuid = \PHP_MPM\Utils::uuid();
                $g = new \PHP_MPM\Group();
                $g->id = TemplateTest::EXISTENT_GROUP_ID;
                $p = new \PHP_MPM\TemplatePermission();
                $p->group = $g;                                
                $t->set(
                    $uuid,
                    sprintf("Template name: %s", $uuid),
                    sprintf("Template description: %s", $uuid),
                    array(
                        $p
                    )
                );
                $t->add();
                $t->delete();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                        
        }

        public function testGetWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $t = new \PHP_MPM\Template();
            $t->get();            
        }        

        // TODO
        public function testGetWithoutAuthAdminSession() {
        }

        public function testGetWithoutEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->get();                        
        }

        public function testGetWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = \PHP_MPM\Utils::uuid();
            $t->get();                        
        }        

        public function testGetWithExistentId() {
            $this->signInAsAdmin();
            $t = new \PHP_MPM\Template();
            $t->id = TemplateTest::EXISTENT_TEMPLATE_ID;
            $t->get();                        
            $this->assertEquals($t->name, TemplateTest::EXISTENT_TEMPLATE_NAME);                                    
        }        
    }
?>
