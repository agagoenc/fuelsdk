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
    protected $lastUrlRequest;

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

    #GET SERVICES
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
            if(!empty($extraFilters))
            {
                $path .= "?" . $extraFilters;
            }
        }

        $completeUrl = $this->getCompleteUrl($path);
        $this->requestRawGet($completeUrl);
    }

    /**
     * @throws ConnectionException
     */
    public function requestNextPage()
    {
        if(!$this->response->getPagination()->getNextPage())
        {
            return false;
            //throw new ConnectionException($this, "There are no more grow pages to consult");
        }

        //list Item Cliente
        $completeUrl = $this->getLastUrlRequest();
        $currentPage = $this->response->getPagination()->getPage();

        if(strpos($completeUrl, "page=" . $currentPage) !== false)
        {
            $completeUrl = str_replace("page=". $this->getResponse()->getPagination()->getPage(), "page=" . $this->response->getPagination()->getNextPage() ,$completeUrl);
        }else{
            if(strpos($completeUrl, "?") === false)
            {
                $completeUrl .= "?";
            }
            elseif(strpos($completeUrl, "&") !== false && strpos($completeUrl, "&" . $currentPage)+1 < strlen($completeUrl))
            {
                $completeUrl .= "&";
            }
            $completeUrl .= "page=" . $this->response->getPagination()->getNextPage();
        }
        var_dump($completeUrl);

        $this->requestRawGet($completeUrl);
        return true;
    }

    /**
     * @throws ConnectionException
     */
    public function requestPreviousPage()
    {
        if(!$this->response->getPagination()->getPrevPage())
        {
            return false;
            //throw new ConnectionException($this, "There are no more less pages to consult");
        }

        //list Item Cliente
        $completeUrl = $this->getLastUrlRequest();
        $currentPage = $this->response->getPagination()->getPage();
        if(strpos($completeUrl, "page=" . $currentPage) !== false)
        {
            $completeUrl = str_replace("page=". $this->getResponse()->getPagination()->getPage(), "page=" . $this->response->getPagination()->getPrevPage() ,$completeUrl);
        }else{
            if(strpos($completeUrl, "?") === false)
            {
                $completeUrl .= "?";
            }
            elseif(strpos($completeUrl, "&") !== false && strpos($completeUrl, "&" . $currentPage)+1 < strlen($completeUrl))
            {
                $completeUrl .= "&";
            }
            $completeUrl .= "page=" . $this->response->getPagination()->getPrevPage();
        }

        $this->requestRawGet($completeUrl);
        return true;
    }

    /**
     * @param $completeUrl
     * @throws ConnectionException
     */
    protected function requestRawGet($completeUrl)
    {
        $httpVerb = 'GET';
        var_dump($completeUrl);

        try{
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
            $this->lastUrlRequest = $completeUrl;

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

    /**
     * @param $path
     * @param  $queryItems
     * @throws ConnectionException
     */
    public function requestWilcardDelete($path, $queryItems)
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
            if(!empty($extraFilters))
            {
                $path .= "?" . $extraFilters;
            }
        }

        $completeUrl = $this->getCompleteUrl($path);
        $this->requestRawDelete($completeUrl);
    }

    /**
     * @param $completeUrl
     * @throws ConnectionException
     */
    protected function requestRawDelete($completeUrl)
    {
        $httpVerb = 'DELETE';
        var_dump($completeUrl);

        try{
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
            $this->lastUrlRequest = $completeUrl;

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

    #POST SERVICES
    /**
     * @param $path
     * @param  $queryItems
     * @throws ConnectionException
     */
    public function requestWilcardPost($path, $data, $queryItems = array())
    {

        //Reset Request and Response
        $this->resetRequestAndResponse();

        #QueryItems are allowed for styleResponse
        if($queryItems)
        {
            if(!is_array($queryItems))
            {
                throw new ConnectionException($this, "Invalid 'queryItems' parameter must be an iterable with QueryItem instances");
            }

            $extraFilters = $this->getQueryParams($queryItems);
            if(!empty($extraFilters))
            {
                $path .= "?" . $extraFilters;
            }
        }

        $bodyJson = null;
        if(is_array($data))
        {
            $bodyJson = json_encode(array("data"=>$data));
        }

        $completeUrl = $this->getCompleteUrl($path);
        $this->requestRawPostPut($completeUrl,  $bodyJson, 'POST');
    }

    #PUT SERVICES
    /**
     * @param $path
     * @param  $queryItems
     * @throws ConnectionException
     */
    public function requestWilcardPut($path, $data, $queryItems = array())
    {

        //Reset Request and Response
        $this->resetRequestAndResponse();

        #QueryItems are allowed for styleResponse
        if($queryItems)
        {
            if(!is_array($queryItems))
            {
                throw new ConnectionException($this, "Invalid 'queryItems' parameter must be an iterable with QueryItem instances");
            }

            $extraFilters = $this->getQueryParams($queryItems);
            if(!empty($extraFilters))
            {
                $path .= "?" . $extraFilters;
            }
        }

        $bodyJson = null;
        if(is_array($data))
        {
            $bodyJson = json_encode(array("data"=>$data));
        }

        $completeUrl = $this->getCompleteUrl($path);
        $this->requestRawPostPut($completeUrl,  $bodyJson, 'PUT');
    }
    /**
     * @param $completeUrl
     * @param string $httpVerb
     * @throws ConnectionException
     */
    protected function requestRawPostPut($completeUrl, $body,  $httpVerb='POST')
    {
        var_dump($completeUrl);

        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $completeUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => strtoupper($httpVerb),
                CURLOPT_USERAGENT => self::USER_AGENT_NAME
            ));
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            $curl = $this->setCredentials($curl);
            $output = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $this->request = curl_getinfo($curl);
            curl_close($curl);

            $this->saveResponse($output);
            $this->httpcode = $httpcode;
            $this->lastUrlRequest = $completeUrl;

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

    /**
     * @return mixed
     */
    public function getLastUrlRequest()
    {
        return $this->lastUrlRequest;
    }














}