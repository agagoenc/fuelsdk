<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;
use CURLFile;

class PostFieldFile extends PostFieldParam
{

    protected $fileUrl;

    /**
     * PostField constructor.
     * @param $name
     * @param $fileUrl
     * @throws ConnectionException
     */
    public function __construct($name, $fileUrl)
    {
        if( empty($fileUrl))
        {
            throw  new ConnectionException("The 'fileUrl' parameter must not be empty.");
        }

        parent::__construct($name);

        $this->fileUrl = $fileUrl;
    }

    public function addToArrayPost(&$arrayExists)
    {
      $arrayExists[$this->nameField] = new CURLFile($this->fileUrl);
    }


}