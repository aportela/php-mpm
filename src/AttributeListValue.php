<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class AttributeListValue {
        public $id;
        public $index;
        public $label;

	    public function __construct ($obj = null) {
            if ($obj) {
                $this->id = isset($obj["id"]) ? $obj["id"]: null;
                $this->index = isset($obj["index"]) ? intval($obj["index"]): 0;
                $this->label = isset($obj["label"]) ? $obj["label"]: null;
            }
        }

        public function __destruct() { }

        private function validate(): void {
        }


        private static function removeAll(\PHP_MPM\Database\DB $dbh, string $attributeId = ""): void {
            $dbh->execute(
                " DELETE FROM ATTRIBUTE_LIST_VALUE WHERE attribute_id = :attribute_id ",
                array(
                    (new \PHP_MPM\Database\DBParam())->str(":attribute_id", $attributeId)
                )
            );
        }

        private function add(\PHP_MPM\Database\DB $dbh, string $attributeId = ""): void {
            $dbh->execute(
                " INSERT INTO ATTRIBUTE_LIST_VALUE (id, attribute_id, idx, label) VALUES(:id, :attribute_id, :idx, :label) ",
                array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                    (new \PHP_MPM\Database\DBParam())->str(":attribute_id", $attributeId),
                    (new \PHP_MPM\Database\DBParam())->int(":idx", $this->index),
                    (new \PHP_MPM\Database\DBParam())->str(":label", $this->label)
                )
            );
        }

        public static function setCollection(\PHP_MPM\Database\DB $dbh, array $elements = [], string $attributeId = ""): void {
            self::removeAll($dbh, $attributeId);
            foreach($elements as $element) {
                $element->add($dbh, $attributeId);
            }
        }

        public static function getCollection(\PHP_MPM\Database\DB $dbh, string $attributeId = "") {
            return($dbh->query(
                "
                    SELECT id, label
                    FROM ATTRIBUTE_LIST_VALUE
                    WHERE attribute_id = :attribute_id
                    ORDER BY idx
                ",
                array(
                    (new \PHP_MPM\Database\DBParam())->str(":attribute_id", $attributeId)
                )
            ));
        }

    }
?>