<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

class PostField extends PostFieldParam
{

    protected $value;

    /**
     * PostField constructor.
     * @param $name
     * @param $value
     * @throws ConnectionException
     */
    public function __construct($name, $value)
    {
        if( empty($value))
        {
            throw  new ConnectionException("The 'value' parameter must not be empty.");
        }

        parent::__construct($name);

        $this->value = $value;
    }

    public function addToArrayPost(&$arrayExists)
    {
        $arrayExists[$this->nameField] = $this->value;
    }



}