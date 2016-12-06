<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "configuration.php";

    ob_start();    

    class ElementTest extends \PHPUnit_Framework_TestCase {

        const EXISTENT_USER_ID = "00000000-0000-0000-0000-000000000000";
        const EXISTENT_TEMPLATE_ID = "2222222-1111-2222-1111-222222222222";

        const ADMIN_EMAIL = "admin@localhost";
        const ADMIN_PASSWORD = "password";
        
        public $db;

        public function __construct () { 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $this->db = \PHP_MPM\Database::getHandler(true);
        }

        public function __destruct () {
            if ($this->db) {
                $this->db->rollbackTrans();
            }
        }        

        private function signInAsAdmin() {
            $u = new \PHP_MPM\User();
            $u->set("", ElementTest::ADMIN_EMAIL, ElementTest::ADMIN_PASSWORD, "administrator", \PHP_MPM\UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new \PHP_MPM\User())->signout();
        }

        public function testCreateWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $e = new \PHP_MPM\Element();
            $e->create();
        }

        public function testCreateWithoutTemplateId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $e = new \PHP_MPM\Element();
            $e->create();
        }

        public function testCreate() {
            $this->signInAsAdmin();
            $e = new \PHP_MPM\Element();
            $e->templateId = ElementTest::EXISTENT_TEMPLATE_ID;
            $e->create();
            $this->assertNotNull($e->id);            
        }
        
        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $e = new \PHP_MPM\Element();
            $e->add();            
        }

        public function testAddWithoutTemplateId() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $e = new \PHP_MPM\Element();
            $e->add();
        }

        public function testAddWithoutDescription() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $e = new \PHP_MPM\Element();
            $e->templateId = ElementTest::EXISTENT_TEMPLATE_ID;
            $e->add();
        }

        /*
        * TODO
        public function testAddWithoutCreationPermissions() {

            $this->MPMAccessDeniedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $e = new \PHP_MPM\Element();
            $e->templateId = ElementTest::EXISTENT_TEMPLATE_ID;
            $e->add();
        }
        */

        public function testAdd() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $e = new \PHP_MPM\Element();
                $e->templateId = ElementTest::EXISTENT_TEMPLATE_ID;
                $e->create();
                $e->description = "description test...";
                $e->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
        }
        
    }
?>
