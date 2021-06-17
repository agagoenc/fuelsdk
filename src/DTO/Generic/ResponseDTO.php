<?php

namespace FuelSdk\DTO\Generic;

class ResponseDTO
{
    private $data;
    private $codeResponse;
    private $status = false;
    private $httpCode;
    private $originalResponse;
    /**
     * @var ResponsePaginationDTO $pagination
     */
    private $pagination;

    /**
     * @param $response
     */
    public function fillWithBodyJson($response)
    {
        $this->originalResponse = $response;
        $bodyJson = json_decode($response, true);

        if(isset($bodyJson["data"]))
        {
            $this->data = $bodyJson["data"];
        }

        if(isset($bodyJson["codeResponse"]))
        {
            $this->codeResponse = $bodyJson["codeResponse"];
            if( strpos( "1_", (string)$this->codeResponse . "_")  === false)
            {
                $this->status = false;
            }else{
                $this->status = true;
            }
        }

        if(isset($bodyJson["error"]))
        {
            $this->error = $bodyJson["error"];
        }

        if(isset($bodyJson["pagination"]) && is_array($bodyJson["pagination"]))
        {
            $pagination = new ResponsePaginationDTO();
            $pagination->fillWithPaginationArray($bodyJson['pagination']);
            $this->pagination = $pagination;
        }

    }

    public function __toString()
    {
        return $this->originalResponse;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCodeResponse()
    {
        return $this->codeResponse;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return mixed
     */
    public function getOriginalResponse()
    {
        return $this->originalResponse;
    }

    /**
     * @return ResponsePaginationDTO
     */
    public function getPagination()
    {
        return $this->pagination;
    }


}