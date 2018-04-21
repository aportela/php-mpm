<?php

      declare(strict_types=1);

      namespace PHP_MPM\Database;

      /**
       * Simple PDO Database Param Wrapper
       */
      class DBParam {

            public $name;
            public $value;
            public $type;

            /**
             * set param properties
             *
             * @param $name
             * @param $value
             * @param $type
             *
             * @return \PHP_MPM\Database\DBParam
             */
            public function set($name, $value, $type): \PHP_MPM\Database\DBParam {
                  $this->name = $name;
                  $this->value = $value;
                  $this->type = $type;
                  return($this);
            }

            /**
             * set NULL param
             *
             * @param $name
             *
             * @return \PHP_MPM\Database\DBParam
             */
            public function null(string $name): \PHP_MPM\Database\DBParam {
                  $this->name = $name;
                  $this->value = null;
                  $this->type = \PDO::PARAM_NULL;
                  return($this);
            }

            /**
             * set BOOL param
             *
             * @param $name string
             * @param $value boolean
             *
             * @return \PHP_MPM\Database\DBParam
             */
            public function bool(string $name, bool $value): \PHP_MPM\Database\DBParam {
                  $this->name = $name;
                  $this->value = $value;
                  $this->type = \PDO::PARAM_BOOL;
                  return($this);
            }

            /**
             * set INTEGER param
             *
             * @param $name string
             * @param $value int
             *
             * @return \PHP_MPM\Database\DBParam
             */
            public function int(string $name, int $value): \PHP_MPM\Database\DBParam {
                  $this->name = $name;
                  $this->value = $value;
                  $this->type = \PDO::PARAM_INT;
                  return($this);
            }

            /**
             * set FLOAT param
             *
             * @param $name string
             * @param $value int
             *
             * @return \PHP_MPM\Database\DBParam
             */
            public function float(string $name, float $value): \PHP_MPM\Database\DBParam {
                  $this->name = $name;
                  $this->value = $value;
                  $this->type = \PDO::PARAM_STR;
                  return($this);
            }

            /**
             * set STRING param
             *
             * @param $name string
             * @param $value int
             *
             * @return \PHP_MPM\Database\DBParam
             */
            public function str(string $name, $value): \PHP_MPM\Database\DBParam {
                  $this->name = $name;
                  $this->value = $value;
                  $this->type = \PDO::PARAM_STR;
                  return($this);
            }
      }

?>