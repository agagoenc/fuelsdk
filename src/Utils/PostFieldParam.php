<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

abstract class PostFieldParam
{
    protected $nameField;

    public function __construct($nameField)
    {
        if( empty($nameField))
        {
            throw  new ConnectionException("The 'value' parameter must not be empty.");
        }

        $this->nameField = $nameField;
    }


    public function addToArrayPost(&$arrayExists)
    {
        $arrayExists['$this->nameField'] = null;
    }


}