<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

abstract class QueryRelation extends QueryValue
{

    const FUEL_INT_QUERY_RELATION_EQUAL = 1;
    const FUEL_INT_QUERY_RELATION_GROW = 2;
    const FUEL_INT_QUERY_RELATION_LESS = 3;
    const FUEL_INT_QUERY_RELATION_LIKE = 4;

    /**
     * Availables relation
     * 1 = EQUAL
     * 2 = GROW
     * 3 = LESS
     * 4 = CONTAINS/LIKE
     */
    protected $relation;

    public function __construct($relation, $value)
    {
        if( empty($relation))
        {
            throw  new ConnectionException("The 'relation' parameter must not be empty.");
        }

        $relation = (int)$relation;

        if(!in_array($relation, $this->availablesRelations(), true))
        {
            throw  new ConnectionException("Invalid value for 'relation' parameter.");

        }

        parent::__construct($value);

        $this->relation = $relation;
    }

    public function availablesRelations()
    {
        return array(
            self::FUEL_INT_QUERY_RELATION_EQUAL,
            self::FUEL_INT_QUERY_RELATION_GROW,
            self::FUEL_INT_QUERY_RELATION_LESS,
            self::FUEL_INT_QUERY_RELATION_LIKE
        );
    }

    public function getSubFixName()
    {
        switch ($this->relation)
        {
            case self::FUEL_INT_QUERY_RELATION_EQUAL:
                return "";
                break;
            case self::FUEL_INT_QUERY_RELATION_GROW:
                return "Grow";
                break;
            case self::FUEL_INT_QUERY_RELATION_LESS:
                return "Less";
                break;
            case self::FUEL_INT_QUERY_RELATION_LIKE:
                return "Like";
                break;
            default:
                return "";
        }
    }


}