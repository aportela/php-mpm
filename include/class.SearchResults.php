<?php
    namespace PHP_MPM;

    /**
    *   search results class
    */
    class SearchResults {

        public $pager = null;
        public $results = null;

		public function __construct () { 
            $this->pager = new \stdClass();
            $this->pager->enabled = false;
            $this->pager->actualPage = 1;
            $this->pager->resultspage = 0;
            $this->pager->totalPages = 1;
            $this->pager->totalResults = 0;
            $this->results = array();
        }

        public function __destruct() { }

        /**
        *   set pager properties
        *
        *   @param int totalResults
        *       total result count
        *   @param int actualPage
        *       actual requested page
        *   @param int resultsPage
        *       number of results per page (0 = disable pagination)
        */
        public function setPager(int $totalResults = 0, int $actualPage = 1, int $resultsPage = 0) {
            if ($resultsPage > 0) {
                $this->pager->enabled = true;
                $this->pager->totalResults = $totalResults;
                $this->pager->actualPage = $actualPage;
                $this->pager->resultspage = $resultsPage;
                if ($this->pager->totalResults > 0) {
                    $this->pager->totalPages = ceil($this->pager->totalResults / $this->pager->resultspage);
                } else {
                    $this->pager->totalPages = 1;
                }
                if ($this->pager->actualPage < 0 || $this->pager->actualPage > $this->pager->totalPages) {
                    throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
                }                
            } else {
                $this->pager->enabled = false;
                $this->pager->totalResults = $totalResults;
                $this->pager->actualPage = $actualPage;
                $this->pager->resultspage = $resultsPage;
                $this->pager->totalPages = 1;
            }
        }

        /**
        *   set result data
        *
        *   @param array results
        *       results collection
        */
        public function setResults(array $results) {
            $this->results = $results;
            if (! $this->pager->enabled) {
                $this->pager->totalResults = count($results);
                $this->pager->actualPage = 1;
                $this->pager->totalPages = 1;
            }
        }
    }
?>