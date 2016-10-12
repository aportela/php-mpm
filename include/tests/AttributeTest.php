<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.User.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Attribute.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.Utils.php";
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "class.CustomExceptions.php";

    ob_start();    

    class AttributeTest extends \PHPUnit_Framework_TestCase {

        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $a = new Attribute();
            $a->exists();                    
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
            $a = new Attribute();
            $a->exists();                    
            */                    
        }

        public function testExistsWithExistentId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $a = new Attribute();
            $a->set(
                "1111111-1111-1111-0000-111111111111",
                "",
                "",
                AttributeType::NONE
            );
            $this->assertTrue($a->exists());        
        }

        public function testExistsWithNotExistentId() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $a = new Attribute();
            $a->set(
                "z-z-z--z-z-z-z",
                "",
                "",
                AttributeType::NONE
            );
            $this->assertFalse($a->exists());        
        }

        public function testExistsWithExistentName() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $a = new Attribute();
            $a->set(
                "",
                "Age",
                "",
                AttributeType::NONE
            );
            $this->assertTrue($a->exists());        
        }

        public function testExistsWithNotExistentName() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $a = new Attribute();
            $a->set(
                "",
                "this_atribute_Name_DO_NOT_exISt__--",
                "",
                AttributeType::NONE
            );
            $this->assertFalse($a->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $a = new Attribute();
            $a->set(
                Utils::uuid(),
                "Surname",
                "Type person surname",
                AttributeType::TEXT_SHORT
            );
            $a->add();                    
        }

        public function testAddWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user            
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $a->set(
                Utils::uuid(),
                "Surname",
                "Type person surname",
                AttributeType::TEXT_SHORT
            );
            $a->add();                    
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
            $a = new Attribute();            
            $a->set(
                "1111111-1111-1111-0000-111111111111",
                "Surname",
                "Type person surname",
                AttributeType::TEXT_SHORT
            );
            $a->add();                    
        }

        public function testAddWithExistentName() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $a = new Attribute();
            $a->set(
                Utils::uuid(),
                "Age",
                "Used for storing ages",
                AttributeType::NUMBER_INTEGER
            );
            $a->add();                                
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $a = new Attribute();
            $a->set(
                Utils::uuid(),
                "",
                "Used for storing ages",
                AttributeType::NUMBER_INTEGER
            );
            $a->add();                                
        }

        public function testAdd() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {
                $err = null;
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();
                $a = new Attribute();
                $uuid = Utils::uuid();
                $a->set(
                    $uuid,
                    sprintf("Attribute name: %s", $uuid),
                    sprintf("Attribute description: %s", $uuid),
                    AttributeType::TEXT_LONG
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $a = new Attribute();
            $a->update();                    
        }

        public function testUpdateWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user            
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $a = new Attribute();
            $a->set(
                Utils::uuid(),
                "Surname",
                "Type person surname",
                AttributeType::NONE
            );
            $a->update();                    
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
            $a = new Attribute();            
            $a->set(
                "",
                "Updated name",
                "Updated description",
                AttributeType::TEXT_SHORT
            );
            $a->update();                    
        }

        public function testUpdateWithNonExistentId() {
            $this->setExpectedException('PHP_MPM\MPMNotFoundException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $a = new Attribute();
            $a->set(
                "z-z-z-z-z-z-z-z-z",
                "Age2",
                "Used for storing ages2",
                AttributeType::NONE
            );
            $a->update();                                
        }

        public function testUpdateWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();            
            $a = new Attribute();
            $a->set(
                "1111111-1111-1111-0000-111111111111",
                "",
                "Used for storing ages",
                AttributeType::NONE
            );
            $a->update();                                
        }

        public function testUpdate() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $err = null;
            try {
                $u = new User();
                $u->set("", "admin@localhost", "password", 0);
                $u->login();            
                $a = new Attribute();
                $a->set(
                    "1111111-1111-1111-0000-111111111111",
                    "Name",
                    "For short (0-255 chars) texts",
                    AttributeType::NONE
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->logout();
            $a = new Attribute();
            $a->delete();
        }

        public function testDeleteWithoutAuthAdminSession() {
            /*
            // TODO: default (non admin) user            
            $this->setExpectedException('PHP_MPM\MPMAdminPrivilegesRequiredException');
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->logout();
            $a = new Attribute();
            $a->delete();
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
                $a = new Attribute();
                $uuid = Utils::uuid();
                $a->set(
                    $uuid,
                    sprintf("Attribute name: %s", $uuid),
                    sprintf("Attribute description: %s", $uuid),
                    AttributeType::TEXT_LONG
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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->logout();
            Attribute::search(0, 16);
        }

        public function testSearchWithAuthSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $u = new User();
            $u->set("", "admin@localhost", "password", 0);
            $u->login();
            $results = Attribute::search(0, 16);
            // TODO: better search results check
            $this->assertGreaterThanOrEqual(1, count($results));
        }
        
    }
?>