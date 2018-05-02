<?php

      declare(strict_types=1);

      namespace PHP_MPM\Database;

      class Version {

        private $dbh;
        private $databaseType;

        private $installQueries = array(
            "PDO_MARIADB" => array(
                '
                    DROP TABLE IF EXISTS VERSION;
                ',
                '
                    CREATE TABLE `VERSION` (
                        `num` DECIMAL(5,2) UNSIGNED NOT NULL,
                        `installed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`num`)
                    );
                ',
                '
                    INSERT INTO `VERSION` VALUES (1.00, UTC_TIMESTAMP(3));
                '
            )
        );

        private $upgradeQueries = array(
            "PDO_MARIADB" => array(
                "1.01" => array(
                    '
                        DROP TABLE IF EXISTS `USER`;
                    ',
                    '
                        CREATE TABLE `USER` (
                            `id` VARCHAR(36) NOT NULL,
                            `email` VARCHAR(254) NOT NULL,
                            `password_hash` VARCHAR(60) NOT NULL,
                            `name` VARCHAR(254) NULL DEFAULT NULL,
                            `account_type` VARCHAR(1) NOT NULL DEFAULT "U",
                            `creator` VARCHAR(36) NOT NULL,
                            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `deleted` TIMESTAMP NULL DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            INDEX `email` (`email`(191)),
                            INDEX `creator` (`creator`),
                            INDEX `deleted` (`deleted`),
                            INDEX `account_type` (`account_type`)
                        );
                    ',
                    '
                        INSERT INTO `USER` VALUES ("00000000-0000-0000-0000-000000000000", "admin@localhost.localnet", "$2y$12$wbhHTy2TRI5vSgrzwLvdd.0iaskb8Dh.vzKhTojxnn.2MGDqpxX6y", "Administrator", "A", "00000000-0000-0000-0000-000000000000", UTC_TIMESTAMP(3), NULL);
                    '
                ),
                "1.02" => array(
                    '
                        DROP TABLE IF EXISTS `GROUP`;
                    ',
                    '
                        CREATE TABLE `GROUP` (
                            `id` VARCHAR(36) NOT NULL,
                            `name` VARCHAR(64) NOT NULL,
                            `description` VARCHAR(254) NULL,
                            `creator` VARCHAR(36) NOT NULL,
                            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `deleted` TIMESTAMP NULL DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            INDEX `name` (`name`),
                            INDEX `description` (`description`),
                            INDEX `creator` (`creator`),
                            INDEX `deleted` (`deleted`)
                        );
                    ',
                    '
                        INSERT INTO `GROUP` VALUES ("11111111-1111-1111-1111-111111111111", "Administrators", "All users contained in this group will have administrative privileges", "00000000-0000-0000-0000-000000000000", "2018-04-28 20:32:01", NULL);
                    '
                ),
                "1.03" => array(
                    '
                        DROP TABLE IF EXISTS `USER_GROUP`;
                    ',

                    '
                        CREATE TABLE `USER_GROUP` (
                            `user_id` VARCHAR(36) NOT NULL,
                            `group_id` VARCHAR(36) NOT NULL,
                            PRIMARY KEY (`user_id`, `group_id`),
                            INDEX `user_id` (`user_id`),
                            INDEX `group_id` (`group_id`),
                            CONSTRAINT `FK_USER_GROUP_PERMISSION_GROUP` FOREIGN KEY (`group_id`) REFERENCES `GROUP` (`id`),
                            CONSTRAINT `FK_USER_GROUP_PERMISSION_USER` FOREIGN KEY (`user_id`) REFERENCES `USER` (`id`)
                        );
                    ',
                    '
                        INSERT INTO `USER_GROUP` VALUES ("00000000-0000-0000-0000-000000000000", "11111111-1111-1111-1111-111111111111");
                    '
                ),
                "1.04" => array(
                    '
                        DROP TABLE IF EXISTS `ATTRIBUTE_TYPE`;
                    ',
                    '
                        CREATE TABLE `ATTRIBUTE_TYPE` (
                            `id` TINYINT(3) UNSIGNED NOT NULL,
                            `name` VARCHAR(32) NOT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE INDEX `name` (`name`)
                        );
                    ',
                    '
                        INSERT INTO `ATTRIBUTE_TYPE` VALUES (1, "Short text (0-1024 chars)");
                        INSERT INTO `ATTRIBUTE_TYPE` VALUES (2, "Long text (memo)");
                        INSERT INTO `ATTRIBUTE_TYPE` VALUES (3, "Integer number");
                        INSERT INTO `ATTRIBUTE_TYPE` VALUES (4, "Decimal number");
                        INSERT INTO `ATTRIBUTE_TYPE` VALUES (5, "Date");
                    ',
                    '
                        DROP TABLE IF EXISTS `ATTRIBUTE`;
                    ',
                    '
                        CREATE TABLE `ATTRIBUTE` (
                            `id` VARCHAR(36) NOT NULL,
                            `name` VARCHAR(64) NOT NULL,
                            `description` VARCHAR(254) NULL DEFAULT NULL,
                            `type` TINYINT(3) UNSIGNED NOT NULL,
                            `creator` VARCHAR(36) NOT NULL,
                            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `deleted` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            INDEX `FK_ATTRIBUTE_ATTRIBUTE_TYPE` (`type`),
                            INDEX `name` (`name`),
                            INDEX `description` (`description`(191)),
                            CONSTRAINT `FK_ATTRIBUTE_ATTRIBUTE_TYPE` FOREIGN KEY (`type`) REFERENCES `ATTRIBUTE_TYPE` (`id`)
                        );
                    '
                )
            )
        );

        public function __construct (\PHP_MPM\Database\DB $dbh, string $databaseType) {
            $this->dbh = $dbh;
            $this->databaseType = $databaseType;
        }

        public function __destruct() { }

        public function get() {
            $query = ' SELECT num FROM VERSION ORDER BY num DESC LIMIT 1; ';
            $results = $this->dbh->query($query, array());
            if ($results && count($results) == 1) {
                return($results[0]->num);
            } else {
                throw new \PHP_MPM\Exception\NotFoundException("invalid database version");
            }
        }

        private function set(float $number) {
            $params = array(
                (new \PHP_MPM\Database\DBParam())->float(":num", $number)
            );
            $query = '
                INSERT INTO VERSION
                    (num, installed)
                VALUES
                    (:num, current_timestamp);
            ';
            return($this->dbh->execute($query, $params));
        }

        public function install() {
            if (isset($this->installQueries[$this->databaseType])) {
                foreach($this->installQueries[$this->databaseType] as $query) {
                    $this->dbh->execute($query);
                }
            } else {
                throw new \Exception("Unsupported database type: " . $this->databaseType);
            }
        }

        public function upgrade() {
            if (isset($this->upgradeQueries[$this->databaseType])) {
                $result = array(
                    "successVersions" => array(),
                    "failedVersions" => array()
                );
                $actualVersion = $this->get();
                $errors = false;
                foreach($this->upgradeQueries[$this->databaseType] as $version => $queries) {
                    if (! $errors && $version > $actualVersion) {
                        try {
                            $this->dbh->beginTransaction();
                            foreach($queries as $query) {
                                $this->dbh->execute($query);
                            }
                            $this->set(floatval($version));
                            $this->dbh->commit();
                            $result["successVersions"][] = $version;
                        } catch (\PDOException $e) {
                            echo $e->getMessage();
                            $this->dbh->rollBack();
                            $errors = true;
                            $result["failedVersions"][] = $version;
                        }
                    } else if ($errors) {
                        $result["failedVersions"][] = $version;
                    }
                }
                return($result);
            } else {
                throw new \Exception("Unsupported database type: " . $this->databaseType);
            }
        }

        public function hasUpgradeAvailable() {
            $actualVersion = $this->get();
            $errors = false;
            foreach($this->upgradeQueries[$this->databaseType] as $version => $queries) {
                if ($version > $actualVersion) {
                    return(true);
                }
            }
            return(false);
        }

    }

?>