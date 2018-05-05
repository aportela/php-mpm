<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    abstract class AttributeType {
        const SHORT_TEXT = 1;
        const LONG_TEXT = 2;
        const INTEGER_NUMBER = 3;
        const DECIMAL_NUMBER = 4;
        const DATE = 5;
        const LIST_OF_VALUES = 6;
    }
?>