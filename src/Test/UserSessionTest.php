<?php

    declare(strict_types=1);

    namespace PHP_MPM\Test;

    require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

    final class UserSessionTest extends \PHPUnit\Framework\TestCase
    {
        static private $app = null;
        static private $container = null;
        static private $dbh = null;

        /**
         * Called once just like normal constructor
         */
        public static function setUpBeforeClass () {
            self::$app = (new \PHP_MPM\App())->get();
            self::$container = self::$app->getContainer();
            self::$dbh = new \PHP_MPM\Database\DB(self::$container);
        }

        /**
         * Initialize the test case
         * Called for every defined test
         */
        public function setUp() {
            self::$dbh->beginTransaction();
        }

        /**
         * Clean up the test case, called for every defined test
         */
        public function tearDown() {
            self::$dbh->rollBack();
        }

        /**
         * Clean up the whole test class
         */
        public static function tearDownAfterClass() {
            self::$dbh = null;
            self::$container = null;
            self::$app = null;
        }

        public function testIsLoggedWithoutSession(): void {
            \PHP_MPM\User::logout();
            $this->assertFalse(\PHP_MPM\UserSession::isLogged());

        }

        public function testIsLogged(): void {
            $id = (\Ramsey\Uuid\Uuid::uuid4())->toString();
            $u = new \PHP_MPM\User($id, $id . "@server.com", "secret", $id, "");
            $u->add(self::$dbh);
            $u->login(self::$dbh);
            $this->assertTrue(\PHP_MPM\UserSession::isLogged());
        }

        public function testGetUserIdWithoutSession(): void {
            \PHP_MPM\User::logout();
            $this->assertNull(\PHP_MPM\UserSession::getUserId());

        }

        public function testGetUserId(): void {
            \PHP_MPM\User::logout();
            $id = (\Ramsey\Uuid\Uuid::uuid4())->toString();
            $u = new \PHP_MPM\User($id, $id . "@server.com", "secret", $id, "");
            $u->add(self::$dbh);
            $u->login(self::$dbh);
            $this->assertEquals($u->id, \PHP_MPM\UserSession::getUserId());
        }

        public function testGetEmailWithoutSession(): void {
            \PHP_MPM\User::logout();
            $this->assertNull(\PHP_MPM\UserSession::getEmail());

        }

        public function testGetEmail(): void {
            \PHP_MPM\User::logout();
            $id = (\Ramsey\Uuid\Uuid::uuid4())->toString();
            $u = new \PHP_MPM\User($id, $id . "@server.com", "secret", $id, "");
            $u->add(self::$dbh);
            $u->login(self::$dbh);
            $this->assertEquals($u->email, \PHP_MPM\UserSession::getEmail());
        }

        public function testGetNickWithoutSession(): void {
            \PHP_MPM\User::logout();
            $this->assertNull(\PHP_MPM\UserSession::getNick());

        }

        public function testGetNick(): void {
            \PHP_MPM\User::logout();
            $id = (\Ramsey\Uuid\Uuid::uuid4())->toString();
            $u = new \PHP_MPM\User($id, $id . "@server.com", "secret", $id, "");
            $u->add(self::$dbh);
            $u->login(self::$dbh);
            $this->assertEquals($u->nick, \PHP_MPM\UserSession::getNick());
        }

        public function testGetAvatarWithoutSession(): void {
            \PHP_MPM\User::logout();
            $this->assertNull(\PHP_MPM\UserSession::getAvatarUrl());
        }

        public function testGetAvatar(): void {
            \PHP_MPM\User::logout();
            $id = (\Ramsey\Uuid\Uuid::uuid4())->toString();
            $u = new \PHP_MPM\User($id, $id . "@server.com", "secret", $id, "http://avat.ar");
            $u->add(self::$dbh);
            $u->login(self::$dbh);
            $this->assertEquals($u->avatarUrl, \PHP_MPM\UserSession::getAvatarUrl());
        }

    }
?>