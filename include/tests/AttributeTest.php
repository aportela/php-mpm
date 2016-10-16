<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "configuration.php";

    ob_start();    

    class AttributeTest extends \PHPUnit_Framework_TestCase {

        const ADMIN_EMAIL = "admin@localhost";
        const ADMIN_PASSWORD = "password";

        const EXISTENT_ATTRIBUTE_ID = "1111111-1111-1111-0000-333333333333";
        const EXISTENT_ATTRIBUTE_NAME = "Age";

        public function __construct () { 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        private function signInAsAdmin() {
            $u = new \PHP_MPM\User();
            $u->set("", AttributeTest::ADMIN_EMAIL, AttributeTest::ADMIN_PASSWORD, "administrator", \PHP_MPM\UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new \PHP_MPM\User())->signout();
        }
        
        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $a = new \PHP_MPM\Attribute();
            $a->exists();                    
        }

        // TODO
        public function testExistsWithoutAuthAdminSession() {
        }

        public function testExistsWithExistentId() {
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();
            $a->id = AttributeTest::EXISTENT_ATTRIBUTE_ID;
            $this->assertTrue($a->exists());        
        }

        public function testExistsWithNotExistentId() {
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();
            $a->id = \PHP_MPM\Utils::uuid();
            $this->assertFalse($a->exists());        
        }

        public function testExistsWithExistentName() {
            $this->signInAsAdmin();            
            $a = new \PHP_MPM\Attribute();
            $a->name = AttributeTest::EXISTENT_ATTRIBUTE_NAME;
            $this->assertTrue($a->exists());        
        }

        public function testExistsWithNotExistentName() {
            $this->signInAsAdmin();            
            $a = new \PHP_MPM\Attribute();
            $a->name = \PHP_MPM\Utils::uuid();
            $this->assertFalse($a->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $a = new \PHP_MPM\Attribute();
            $a->add();                    
        }

        // TODO
        public function testAddWithoutAuthAdminSession() {
        }

        public function testAddWithExistentId() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();
            $a->id = AttributeTest::EXISTENT_ATTRIBUTE_ID;             
            $a->add();                    
        }

        public function testAddWithExistentName() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();
            $a->name = AttributeTest::EXISTENT_ATTRIBUTE_NAME;             
            $a->add();                                
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();
            $a->add();                                
        }

        public function testAdd() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $a = new \PHP_MPM\Attribute();
                $uuid = \PHP_MPM\Utils::uuid();
                $a->set(
                    $uuid,
                    sprintf("Attribute name: %s", $uuid),
                    sprintf("Attribute description: %s", $uuid),
                    rand(1, 7)
                );
                $a->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                        
        }

        public function testUpdateWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $a = new \PHP_MPM\Attribute();
            $a->update();                    
        }

        // TODO
        public function testUpdateWithoutAuthAdminSession() {
        }

        public function testUpdateWithEmptyId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();            
            $a->update();                    
        }

        public function testUpdateWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();            
            $a->id = \PHP_MPM\Utils::uuid();
            $a->name = sprintf("Attribute name: %s", \PHP_MPM\Utils::uuid());
            $a->update();                                
        }

        public function testUpdateWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();            
            $a->id = AttributeTest::EXISTENT_ATTRIBUTE_ID;
            $a->update();                                
        }

        public function testUpdate() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $a = new \PHP_MPM\Attribute();
                $a->set(
                    AttributeTest::EXISTENT_ATTRIBUTE_ID,
                    AttributeTest::EXISTENT_ATTRIBUTE_NAME,
                    "Integer values",
                    AttributeType::NUMBER_INTEGER
                );
                $a->update();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }

        public function testDeleteWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $a = new \PHP_MPM\Attribute();
            $a->delete();
        }

        // TODO
        public function testDeleteWithoutAuthAdminSession() {
        }

        public function testDeleteWithoutId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $a = new \PHP_MPM\Attribute();
            $a->delete();
        }

        public function testDelete() {
            $err = null;
            try {
                $this->signInAsAdmin();  
                $a = new \PHP_MPM\Attribute();
                $uuid = \PHP_MPM\Utils::uuid();
                $a->set(
                    $uuid,
                    sprintf("Attribute name: %s", $uuid),
                    sprintf("Attribute description: %s", $uuid),
                    rand(1, 7)
                );
                $a->add();
                $a->delete();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }                        
        }
        
        public function testSearchWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            Attribute::search(1, 16, "");
        }

        public function testSearchWithAuthSession() {
            $this->signInAsAdmin();
            $data = Attribute::search(1, 16, "");
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($data->results));
        }        
    }
?>