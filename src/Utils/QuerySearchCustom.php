<?php


namespace FuelSdk\Utils;


class QuerySearchCustom extends QueryValue
{
    const FUEL_STR_QUERY_NAME_SEARCH_CUSTOM = 'searchCustom';
    protected $name;

    public function __construct($value)
    {
        $this->name = self::FUEL_STR_QUERY_NAME_SEARCH_CUSTOM;

        parent::__construct($value);
    }

    public function composeQuery()
    {
        return $this->name . "=" . $this->value;
    }

}