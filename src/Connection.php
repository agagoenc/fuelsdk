<?php


namespace FuelSdk;


use FuelSdk\DTO\Generic\ResponseDTO;
use FuelSdk\Exception\ConnectionException;
use FuelSdk\Utils\QueryItem;
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

    /**
     * @param $message
     */
    public function writeLog($message)
    {
        $this->logger->info($message);
    }

    public function resetRequestAndResponse()
    {
        $this->request = null;
        $this->response = null;
    }

    public function setCredentials($curl)
    {
        return $curl;
    }

    /**
     * @param $path
     * @param  $queryItems
     * @throws ConnectionException
     */
    public function requestWilcardGet($path, $queryItems)
    {

        
        //Reset Request and Response
        $this->resetRequestAndResponse();

        if($queryItems)
        {
            if(!is_array($queryItems))
            {
                throw new ConnectionException($this, "Invalid 'queryItems' parameter must be an iterable with QueryItem instances");
            }

            $extraFilters = $this->getQueryParams($queryItems);
            $path .= $extraFilters;
        }


        try{
        //list Item Cliente
        $completeUrl = $this->getCompleteUrl($path);
        $httpVerb = 'GET';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $completeUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpVerb,
            CURLOPT_USERAGENT => self::USER_AGENT_NAME
        ));

        $curl = $this->setCredentials($curl);
        $output = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->request = curl_getinfo($curl);
        curl_close($curl);

        $this->saveResponse($output);
        $this->httpcode = $httpcode;


        if($httpcode != 200)
        {
            $this->httpcode = $httpcode;
            throw new ConnectionException($this, "Invalid Http code response");
        }

        }catch(ConnectionException $e){
            throw $e;
        }catch(\Exception $e)
        {
            throw new ConnectionException($this, $e->getMessage());
        }
    }

    private function getQueryParams($queryItems)
    {
        $query = "";
        $firstQuery = true;

        /** @var QueryItem $queryItem */
        foreach ($queryItems as $queryItem)
        {
            $aux = $queryItem->composeQuery();
            if($aux)
            {
                if(!$firstQuery)
                {
                    $query .= "&";
                }else{
                    $firstQuery = false;
                }

                $query .= $aux;

            }
        }

        return $query;
    }




    /**
     * @return ResponseDTO
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getHttpcode()
    {
        return $this->httpcode;
    }

    /**
     * @return mixed
     */
    public function getFechaRefrescoLicencia()
    {
        return $this->fechaRefrescoLicencia;
    }













}