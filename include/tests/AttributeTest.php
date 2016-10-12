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


    }
?>