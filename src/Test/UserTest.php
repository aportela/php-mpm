<?php

    declare(strict_types=1);

    namespace PHP_MPM\Test;

    require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

    final class UserTest extends \PHPUnit\Framework\TestCase {
        static private $app = null;
        static private $container = null;
        static private $dbh = null;

        const VALID_USER_ID = "00000000-0000-0000-0000-000000000000";
        const INVALID_USER_ID = "00000000-1111-1111-1111-000000000000";
        const VALID_USER_EMAIL = "admin@localhost.localnet";
        const INVALID_USER_EMAIL = "notfound@localhost.localnet";
        const BAD_USER_EMAIL = "000";
        const VALID_USER_PASSWORD = "secret";
        const INVALID_USER_PASSWORD = "nope";

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

        public function testGetWithoutIdOrEmail(): void {
            $this->expectException(\PHP_MPM\Exception\InvalidParamsException::class);
            $this->expectExceptionMessage("id,email");
            $u = new \PHP_MPM\User();
            $u->get(self::$dbh);
        }

        public function testGetWithNonExistentId(): void {
            $this->expectException(\PHP_MPM\Exception\NotFoundException::class);
            $u = new \PHP_MPM\User();
            $u->id = self::INVALID_USER_ID;
            $u->get(self::$dbh);
        }

        public function testGetWithInvalidEmail(): void {
            $this->expectException(\PHP_MPM\Exception\InvalidParamsException::class);
            $this->expectExceptionMessage("id,email");
            $u = new \PHP_MPM\User();
            $u->email = self::BAD_USER_EMAIL;
            $u->get(self::$dbh);
        }

        public function testGetWithNonExistentEmail(): void {
            $this->expectException(\PHP_MPM\Exception\NotFoundException::class);
            $u = new \PHP_MPM\User();
            $u->email = self::INVALID_USER_EMAIL;
            $u->get(self::$dbh);
        }

        public function testGetWithExistentId(): void {
            $u = new \PHP_MPM\User();
            $u->id = self::VALID_USER_ID;
            $u->get(self::$dbh);
            $this->assertTrue($u->email == self::VALID_USER_EMAIL);
        }

        public function testGetWithExistentEmail(): void {
            $u = new \PHP_MPM\User();
            $u->email = self::VALID_USER_EMAIL;
            $u->get(self::$dbh);
            $this->assertTrue($u->id == self::VALID_USER_ID);
        }

        public function testSignInWithEmptyPassword(): void {
            $this->expectException(\PHP_MPM\Exception\InvalidParamsException::class);
            $this->expectExceptionMessage("password");
            $u = new \PHP_MPM\User();
            $u->email = self::VALID_USER_EMAIL;
            $u->signIn(self::$dbh);
        }

        public function testSignInWithoutIdOrEmail(): void {
            $this->expectException(\PHP_MPM\Exception\InvalidParamsException::class);
            $this->expectExceptionMessage("id,email");
            $u = new \PHP_MPM\User();
            $u->password = self::VALID_USER_PASSWORD;
            $u->signIn(self::$dbh);
        }

        public function testSignInWithNonExistentId(): void {
            $this->expectException(\PHP_MPM\Exception\NotFoundException::class);
            $u = new \PHP_MPM\User();
            $u->id = self::INVALID_USER_ID;
            $u->password = self::VALID_USER_PASSWORD;
            $u->signIn(self::$dbh);
        }

        public function testSignInWithInvalidEmail(): void {
            $this->expectException(\PHP_MPM\Exception\InvalidParamsException::class);
            $this->expectExceptionMessage("id,email");
            $u = new \PHP_MPM\User();
            $u->email = self::BAD_USER_EMAIL;
            $u->password = self::VALID_USER_PASSWORD;
            $u->signIn(self::$dbh);
        }

        public function testSignInWithNonExistentEmail(): void {
            $this->expectException(\PHP_MPM\Exception\NotFoundException::class);
            $u = new \PHP_MPM\User();
            $u->email = self::INVALID_USER_EMAIL;
            $u->password = self::VALID_USER_PASSWORD;
            $u->signIn(self::$dbh);
        }

        public function testSignInWithInvalidPassword(): void {
            $u = new \PHP_MPM\User();
            $u->email = self::VALID_USER_EMAIL;
            $u->password = self::INVALID_USER_PASSWORD;
            $this->assertFalse($u->signIn(self::$dbh));
        }

        public function testSignInWithValidPassword(): void {
            $u = new \PHP_MPM\User();
            $u->email = self::VALID_USER_EMAIL;
            $u->password = self::VALID_USER_PASSWORD;
            $this->assertTrue($u->signIn(self::$dbh));
        }

        public function testAddWithoutPassword(): void {
            $this->expectException(\PHP_MPM\Exception\InvalidParamsException::class);
            $this->expectExceptionMessage("password");
            $u = new \PHP_MPM\User();
            $u->email = self::INVALID_USER_EMAIL;
            $u->add(self::$dbh);
        }

        public function testAddWithExistentEmail(): void {
            $this->expectException(\PHP_MPM\Exception\ElementAlreadyExistsException::class);
            $this->expectExceptionMessage("email");
            $u = new \PHP_MPM\User();
            $u->email = self::VALID_USER_EMAIL;
            $u->password = self::INVALID_USER_PASSWORD;
            $u->add(self::$dbh);
        }

        public function testAdd(): void {
            $u = new \PHP_MPM\User();
            $u->id = self::INVALID_USER_ID;
            $u->email = self::INVALID_USER_EMAIL;
            $u->password = self::INVALID_USER_PASSWORD;
            $u->accountType = "U";
            $this->assertTrue($u->add(self::$dbh));
        }
    }
?>