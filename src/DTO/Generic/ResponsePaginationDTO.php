<?php


namespace FuelSdk\DTO\Generic;


class ResponsePaginationDTO
{

    protected $page;
    protected $results;
    protected $prevPage;
    protected $nextPage;
    protected $numberPages;

    public function fillWithPaginationArray($arrayPagination)
    {
        if(isset($arrayPagination["page"]))
        {
            $this->page = $arrayPagination["page"];
        }

        if(isset($arrayPagination['results']))
        {
            $this->results = $arrayPagination["results"];
        }

        if(isset($arrayPagination['prevPage']))
        {
            $this->prevPage = $arrayPagination["prevPage"];
        }

        if(isset($arrayPagination['nextPage']))
        {
            $this->nextPage = $arrayPagination["nextPage"];
        }else{
        }

        if(isset($arrayPagination["numberPages"]))
        {
            $this->numberPages = $arrayPagination["numberPages"];
        }
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return mixed
     */
    public function getPrevPage()
    {
        return $this->prevPage;
    }

    /**
     * @return mixed
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * @return mixed
     */
    public function getNumberPages()
    {
        return $this->numberPages;
    }


}