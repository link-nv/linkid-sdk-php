<?php

/*
 * LinkID Report's date filter
 *
 * @author Wim Vandenhaute
 */

class LinkIDReportPageFilter
{
    public $firstResult;
    public $maxResults;

    /**
     * LinkIDReportPageFilter constructor.
     * @param int $firstResult
     * @param int $maxResults
     */
    public function __construct($firstResult, $maxResults)
    {
        $this->firstResult = $firstResult;
        $this->maxResults = $maxResults;
    }


}