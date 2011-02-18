<?php

class GenericList {
    private $list;
    private $totalCount;
    private $startingToken;
    private $updateToken;
    private $nextPageToken;


    function __construct($list, $totalCount, $startingToken, $nextPageToken=null, $updateToken=null) {
        $this->list = $list;
        $this->totalCount = (is_null($totalCount) || $totalCount === false ? false : (int)$totalCount);
        $this->startingToken = $startingToken;
        $this->updateToken = $updateToken;
        $this->nextPageToken = $nextPageToken;
    }

    /**
     * Returns a list of the objects which represent a part from the whole set.
     *
     * @return <array of objects>   a partial list
     */
    public function getList() {
        return $this->list;
    }

    /**
     * Total count of the items in the whole set. Null if the count is unknown.
     *
     * @return <number>  the number of items in the full result
     */
    public function getTotalCount() {
        return $this->totalCount;
    }

    public function setTotalCount($totalCount) {
        $this->totalCount = $totalCount;
    }

    /**
     * Token identifying a position of the first item from the list in the full result set.
     * Null if the token is unknown.
     *
     * @return <string>  starting index
     */
    public function getStartingToken() {
        return $this->startingToken;
    }

    /**
     * Just a convinience method that returns number of items in the current (partial) list.
     *
     * @return <number>  size of the list, 0 if list is empty or null
     */
    public function getCount() {
        return empty($this->list) ? 0 : count($this->list);
    }

    /**
     * Returns a token, if available, that will allow to issue update requests for the new items
     * after returned in the currect list.
     *
     * @return <string>
     */
    public function getUpdateToken() {
        return $this->updateToken;
    }

    /**
     * Returns a token that can be used to retrieve a next page.
     *
     * @return <string>
     */
    public function getNextPageToken() {
        return $this->nextPageToken;
    }
}
?>
