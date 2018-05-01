<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class SearchResult {

        public $actualPage;
        public $resultsPage;
        public $totalResults;
        public $totalPages;

        public function __construct (int $actualPage = 1, int $resultsPage = 0, int $totalResults = 0) {
            $this->actualPage = $actualPage;
            $this->resultsPage = $resultsPage;
            $this->totalResults = $totalResults;
            if ($resultsPage > 0) {
                $this->totalPages = ceil($this->totalResults / $resultsPage);
            } else {
                $this->totalPages = $this->totalResults > 0 ? 1: 0;
            }

        }

        public function __destruct() { }

        public function getSQLPageOffset() {
            return($this->resultsPage * ($this->actualPage - 1));
        }

    }
?>