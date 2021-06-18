<?php

namespace FuelSdk;

use FuelSdk\Exception\ConnectionException;
use Exception;

class ConnectionIpAuthenticated extends Connection
{

    //Params required for login with ip
    private $licenseId;
    private $userId;
    private $userIdOld;
    private $email;


    /**
     * ConnectionIpAuthenticated constructor.
     * @param $licenseId
     * @param $userId
     * @param $fileNameLog
     * @param $environment
     * @throws Exception|ConnectionException
     */
    public function __construct($licenseId, $fileNameLog, $environment)
    {
        if(empty($licenseId))
        {
            throw  new ConnectionException("The 'licenseId' parameter must not be empty.");
        }

        parent::__construct($fileNameLog, $environment);

        $this->licenseId = $licenseId;
        $status = $this->login();
        if($status)
        {
            $this->writeLog('License and IP authentication completes successfully. License: ' .$this->licenseId);
        }

        parent::__construct($fileNameLog, $environment);
    }

    /**
     * @return bool|Exception|ConnectionException
     * @throws ConnectionException
     */
    public function login()
    {
        try{

            //list Item Cliente
            $completeUrl = $this->getCompleteUrl(WebService::PATH_CLIENTE_LIST);
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
                throw new ConnectionException($this, "Invalid License requested from your IP");
            }

        }catch(ConnectionException $e){
            return $e;
        }catch(\Exception $e)
        {
            throw new ConnectionException($this, $e->getMessage());
        }

        return true;
    }

    /**
     * @throws ConnectionException
     */
    public function loginUser()
    {
        try{
            if(!is_null($this->userId))
            {
                //Get Usuario
                $completeUrl = $this->getCompleteUrl(WebService::PATH_USER_GET);
                $completeUrl .= "?id=$this->userId";
                $httpVerb = 'GET';
            }elseif(!is_null($this->userIdOld)) {
                $completeUrl = $this->getCompleteUrl(WebService::PATH_USER_GET);
                $completeUrl .= "?idOld=$this->userIdOld";
                $httpVerb = 'GET';
            }else {
                throw new ConnectionException($this, "UserId is required");
            }

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
                curl_close($curl);

                $this->saveResponse($output);
                $this->httpcode = $httpcode;

                if($httpcode != 200)
                {
                    $this->httpcode = $httpcode;
                    throw new ConnectionException($this, "Invalid User Id");
                }

                if(!is_null($this->userIdOld) && isset($this->response->getData()["id"]))
                {
                    $this->userId = $this->response->getData()["id"];
                    $this->writeLog("Set Id User to: " . $this->userId . " based on Id old: " . $this->userIdOld);
                    $this->userIdOld = null;
                }

        }catch(ConnectionException $e){
            return $e;
        }catch(\Exception $e)
        {
                throw new ConnectionException($this, $e->getMessage());
        }

        return true;
    }

    public function setCredentials($curl)
    {
        if(!is_null($this->userId))
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'cliente: ' . $this->licenseId,
                'usuario: ' . $this->userId,
                'Content-Type: application/json'
        ));
        }elseif(!is_null($this->userIdOld)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'cliente: ' . $this->licenseId,
                'usuarioold: ' . $this->userIdOld,
                'Content-Type: application/json'
            ));
        }else{
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'cliente: ' . $this->licenseId,
                'Content-Type: application/json'
            ));
        }

        return $curl;
    }

    /**
     * @param $userId
     * @throws ConnectionException
     */
    public function setUserId($userId)
    {
        $this->userIdOld = null;
        $this->userId = $userId;
        $status = $this->loginUser();
        if($status === true)
        {
            $this->writeLog('User with License and IP authentication completes successfully. License: ' . $this->licenseId . ' User: ' .$this->userId);
        }
    }


    /**
     * @param $userIdOld
     * @throws ConnectionException
     */
    public function setUserIdOld($userIdOld)
    {
        $this->userId = null;
        $this->userIdOld = $userIdOld;
        $status = $this->loginUser();
        if($status === true)
        {
            $this->writeLog('User with License and IP authentication completes successfully. License: ' . $this->licenseId . ' User: ' .$this->userId . ' UserOld: ' . $this->userIdOld );
        }
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getEmail()
    {
        return $this->email;
    }



}