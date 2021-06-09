<?php


namespace FuelSdk;


use FuelSdk\DTO\Generic\ResponseDTO;
use FuelSdk\Exception\ConnectionException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class Connection
{

    const FUEL_URL_DEV = "https://apidev.fuelindata.com";
    const FUEL_URL_TEST = "https://apitest.fuelindata.com";
    const FUEL_URL_TESTINT = "https://apitestint.fuelindata.com";
    const FUEL_URL_PROD = "https://api.fuelindata.com";
    const FUEL_NAME_DEV = "DEV";
    const FUEL_NAME_TEST = "TEST";
    const FUEL_NAME_TESTINT = "TESTINT";
    const FUEL_NAME_PROD = "PROD";
    const FUEL_URL_ENV_NAME = "FUEL_URL";
    const USER_AGENT_NAME = "Sdk Fuel 1.0.0";

    //Iternal params
    /**
     * @var null
     */
    protected $fileNameLog;
    protected $url;
    protected $environment;
    protected $fechaRefrescoLicencia;
    protected $logger;
    protected $request;
    /** @var ResponseDTO $response */
    protected $response;
    protected $httpcode;

    /**
     * Connection constructor.
     * @param null $fileNameLog
     * @param null $environment
     * @throws ConnectionException
     */
    public function __construct($fileNameLog = null, $environment=null)
    {
        if(!empty($fileNameLog))
        {
            $this->fileNameLog = $fileNameLog;
            $this->logger = new Logger('Log Fuel Sdk');
            $this->logger->pushHandler(new StreamHandler($this->fileNameLog, Logger::INFO));
        }

        $this->getUrlForEnvironment($environment);


    }

    /**
     * @param $environment
     * @throws ConnectionException
     */
    public function getUrlForEnvironment($environment)
    {
        $env = getenv ( self::FUEL_URL_ENV_NAME );

        if($env)
        {
            switch (strtoupper($env))
            {
                case self::FUEL_NAME_DEV:
                    $this->url = self::FUEL_URL_DEV;
                    break;
                case self::FUEL_NAME_TEST:
                    $this->url = self::FUEL_URL_TEST;
                    break;
                case self::FUEL_NAME_TESTINT:
                    $this->url = self::FUEL_URL_TESTINT;
                    break;
                case self::FUEL_NAME_PROD:
                    $this->url = self::FUEL_URL_TESTINT;
                    break;
                default:
                    throw new ConnectionException($this, "Invalid value on env " . self::FUEL_URL_ENV_NAME, 0);
            }
        }else{
            switch (strtoupper($environment))
            {
                case self::FUEL_NAME_DEV:
                    $this->url = self::FUEL_URL_DEV;
                    break;
                case self::FUEL_NAME_TEST:
                    $this->url = self::FUEL_URL_TEST;
                    break;
                case self::FUEL_NAME_TESTINT:
                    $this->url = self::FUEL_URL_TESTINT;
                    break;
                case self::FUEL_NAME_PROD:
                    $this->url = self::FUEL_URL_TESTINT;
                    break;
                default:
                    throw new ConnectionException($this, "Invalid value on env " . self::FUEL_URL_ENV_NAME, 0);
            }
        }
    }

    public function getCompleteUrl($path)
    {
        return $this->url . "/api/v1" . $path;
    }


    public function saveResponse($responseRaw)
    {
        $response = new ResponseDTO();
        $response->fillWithBodyJson($responseRaw);
        $this->response = $response;
    }

    /**
     * @param $message
     * @param $level
     * @throws \Exception
     */
    public function writeLogException($message)
    {
        $this->logger->error($message . " http: " . $this->httpcode . " response: " . $this->response . " request: " . json_encode($this->request));
    }

    public function writeLog($message)
    {
        $this->logger->info($message);
    }











}