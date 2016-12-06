<?php
    namespace PHP_MPM;

    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "configuration.php";

    ob_start();    

    class SearchTemplateTest extends \PHPUnit_Framework_TestCase {

        const EXISTENT_GROUP_ID = "1111111-1111-1111-1111-111111111111";
        const EXISTENT_SEARCH_TEMPLATE_ID = "2222222-1111-2222-3333-222222222222";
        const EXISTENT_SEARCH_TEMPLATE_NAME = "Users";

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
            $u->set("", SearchTemplateTest::ADMIN_EMAIL, SearchTemplateTest::ADMIN_PASSWORD, "administrator", \PHP_MPM\UserType::DEFAULT);
            $u->login();            
        }

        private function signOut() {
            (new \PHP_MPM\User())->signout();
        }
        
        public function testExistsWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $st = new \PHP_MPM\SearchTemplate();
            $st->exists();                    
        }

        // TODO
        public function testExistsWithoutAuthAdminSession() {
        }

        public function testExistsWithExistentId() {
            $this->signInAsAdmin();
            $st = new \PHP_MPM\SearchTemplate();
            $st->id = SearchTemplateTest::EXISTENT_SEARCH_TEMPLATE_ID;
            $this->assertTrue($st->exists());        
        }

        public function testExistsWithNotExistentId() {
            $this->signInAsAdmin();
            $st = new \PHP_MPM\SearchTemplate();
            $st->id = \PHP_MPM\Utils::uuid();
            $this->assertFalse($st->exists());        
        }

        public function testExistsWithExistentName() {
            $this->signInAsAdmin();
            $st = new \PHP_MPM\SearchTemplate();
            $st->name = SearchTemplateTest::EXISTENT_SEARCH_TEMPLATE_NAME;
            $this->assertTrue($st->exists());        
        }

        public function testExistsWithNotExistentName() {
            $this->signInAsAdmin();
            $st = new \PHP_MPM\SearchTemplate();
            $st->name = \PHP_MPM\Utils::uuid();
            $this->assertFalse($st->exists());       
        }

        public function testAddWithoutAuthSession() {
            $this->setExpectedException('PHP_MPM\MPMAuthSessionRequiredException');
            $this->signOut();
            $st = new \PHP_MPM\SearchTemplate();
            $st->add();                    
        }
    
        // TODO
        public function testAddWithoutAuthAdminSession() {
        }

        public function testAddWithExistentId() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $st = new \PHP_MPM\SearchTemplate();
            $st->id = SearchTemplateTest::EXISTENT_SEARCH_TEMPLATE_ID; 
            $st->add();                                
        }

        // TODO
        public function testAddWithExistentName() {
            $this->setExpectedException('PHP_MPM\MPMAlreadyExistsException');
            $this->signInAsAdmin();
            $st = new \PHP_MPM\SearchTemplate();
            $st->name = SearchTemplateTest::EXISTENT_SEARCH_TEMPLATE_NAME; 
            $st->add();                                
        }

        public function testAddWithEmptyName() {
            $this->setExpectedException('PHP_MPM\MPMInvalidParamsException');
            $this->signInAsAdmin();
            $t = new \PHP_MPM\SearchTemplate();
            $t->id = \PHP_MPM\Utils::uuid();
            $t->name = ""; 
            $t->add();                                
        }

        public function testAdd() {
            $err = null;
            try {
                $this->signInAsAdmin();
                $st = new \PHP_MPM\SearchTemplate();
                $id = \PHP_MPM\Utils::uuid();
                $st->set($id, "new search template " . $id, "", array(), " SELECT id, name FROM USER; ");
                $st->add();
            } catch (Throwable $e) {
                $err = e;
            } finally {
                $this->assertNull($err);
            }            
            
        }

    }
?>
