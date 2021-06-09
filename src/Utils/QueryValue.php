<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

abstract class QueryValue implements QueryItem
{
    protected $value;

    /**
     * QueryValue constructor.
     * @param $value
     * @throws ConnectionException
     */
    public function __construct( $value)
    {
        if( empty($value))
        {
            throw  new ConnectionException("The 'value' parameter must not be empty.");
        }
        $this->value = $value;
    }
}