<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

class QueryStyleResponse  extends QueryValue
{

    const FUEL_STR_QUERY_NAME_STYLE_RESPONSE = 'styleResponse';
    protected $name;

    public function __construct($value)
    {
        $this->name = self::FUEL_STR_QUERY_NAME_STYLE_RESPONSE;

        parent::__construct($value);
    }

    public function composeQuery()
    {
        return $this->name . "=" . $this->value;
    }

}