<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

class QueryOrder extends QueryValue
{
    const FUEL_STR_ORDER_ASC = "ASC";
    const FUEL_STR_ORDER_DESC = "DESC";

    const FUEL_STR_ORDER_NAME_BY = "orderBy";
    const FUEL_STR_ORDER_NAME_AS = "orderByAs";


    protected $name;

    public function __construct($name, $order)
    {
        if( empty($name))
        {
            throw  new ConnectionException("The 'name' parameter must not be empty.");
        }

        $this->value = 'ASC';
        if( !empty($order) && in_array($order, $this->availableOrders()))
        {
            throw  new ConnectionException("Invalid value for 'order' parameter.");
        }

        parent::__construct($order);

        $this->name = $name;
    }


    public function availableOrders()
    {
        return array(
            self::FUEL_STR_ORDER_ASC,
            self::FUEL_STR_ORDER_DESC
        );
    }

    public function composeQuery()
    {
        if(!empty($this->name))
        {
            return self::FUEL_STR_ORDER_NAME_BY . "=" . $this->name . "&" . self::FUEL_STR_ORDER_NAME_AS . "=" . $this->value;
        }
        return null;
    }
}