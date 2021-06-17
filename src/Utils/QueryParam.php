<?php


namespace FuelSdk\Utils;


use FuelSdk\Exception\ConnectionException;

class QueryParam extends QueryRelation
{

    protected $name;

    public function __construct($name, $relation, $value)
    {
        if( empty($name))
        {
            throw  new ConnectionException("The 'name' parameter must not be empty.");
        }

        parent::__construct($relation, $value);

        $this->name = $name;
    }

    public function composeQuery()
    {
        if(empty($this->value))
        {
            return null;
        }

        if(!is_array($this->value))
        {
            return "" . $this->name . $this->getSubFixName() . "=" . $this->value;
        }elseif( count($this->value) === 1){
            return "" . $this->name . $this->getSubFixName() . "=" . $this->value[0];
        }else{
            $response = "";
            foreach ($this->value as $value)
            {
                if(!empty($response))
                {
                    $response .= "&";
                }

                $response .= $this->name . $this->getSubFixName() . "[]=" . $value;
            }
        }
    }


}