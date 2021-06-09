<?php


namespace FuelSdk\Utils;


class QueryPagination implements QueryItem
{

    const FUEL_STR_QUERY_NAME_PAGE = "page";
    const FUEL_STR_QUERY_NAME_MAX_RESULTS = "maxResults";

    protected $page;
    protected $maxResults;


    public function __construct($page, $maxResults)
    {
        if(!empty($page))
        {
            $this->page = $page;
        }

        if(!empty($maxResults))
        {
            $this->maxResults = $maxResults;
        }
    }

    /**
     * @return string|null
     */
    public function composeQuery()
    {
        $response = "";

        if(!empty($this->page))
        {
            $response .= self::FUEL_STR_QUERY_NAME_PAGE . "=" . $this->page;
        }

        if(!empty($this->page))
        {
            if(!empty($response))
            {
                $response .= "&";
            }

            $response .=  self::FUEL_STR_QUERY_NAME_MAX_RESULTS . "=" . $this->maxResults;
        }

        if(empty($response))
        {
            return null;
        }

        return $response;
    }

}