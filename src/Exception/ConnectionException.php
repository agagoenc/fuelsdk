<?php


namespace FuelSdk\Exception;


use FuelSdk\Connection;
use Psr\Log\LogLevel;
use Throwable;

class ConnectionException extends \Exception
{
    public function __construct($connection, $message = "", $code = 0, Throwable $previous = null)
    {
        if($connection instanceof Connection)
        {
            try {
                $connection->writeLogException($message);
            }catch(\Exception $e)
            {

            }
        }
        parent::__construct($message, $code, $previous);
    }

}