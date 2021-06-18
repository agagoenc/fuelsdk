<?php


namespace FuelSdk;


use FuelSdk\Exception\ConnectionException;
use FuelSdk\Connection;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;
use Exception;

class ConnectionEmailPass extends Connection
{

    //Params required for login with email and pass;
    private $pass;
    private $email;
    private $token;
    private $refreshToken;

    /**
     * ConnectionEmailPass constructor.
     * @param $pass
     * @param $email
     * @param $fileNameLog
     * @param $environment
     * @throws Exception|ConnectionException
     */
    public function __construct($pass, $email, $fileNameLog, $environment)
    {
        if( empty($email))
        {
            throw  new ConnectionException("The 'email' parameter must not be empty.");
        }
        if( empty($pass))
        {
            throw  new ConnectionException("The 'pass' parameter must not be empty.");
        }

        parent::__construct($fileNameLog, $environment);

        $this->pass = $pass;
        $this->email = $email;

        $this->login();
        $this->logger->addInfo('Login with email and pass is completed successfully. E-mail: ' .$this->email);
    }

    /**
     * @throws ConnectionException
     */
    public function login()
    {

        $completeUrl = $this->getCompleteUrl(WebService::PATH_AUTH_LOGIN);

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
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('email' =>$this->email, 'pass' => $this->pass),
                CURLOPT_USERAGENT => self::USER_AGENT_NAME
            ));

            $output = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $this->request = curl_getinfo($curl);
            curl_close($curl);

            $this->saveResponse($output);
            $this->httpcode = $httpcode;

            if($httpcode != 200)
            {
                $this->httpcode = $httpcode;
                throw new ConnectionException($this, "Invalid Credentials");
            }
            $response = json_decode($output, true);


            if(isset($response["data"]["jwt"]))
            {
                $this->token = $response["data"]["jwt"];
            }

            if(isset($response["data"]["refresh"]))
            {
                $this->refreshToken = $response["data"]["refresh"];
            }

            if(isset($response["data"]["fechaRefrescoLicencia"]))
            {
                $this->fechaRefrescoLicencia = new \DateTime($response["data"]["fechaRefrescoLicencia"]);
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

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->token . " ",
            'Content-Type: application/json'
        ));


        return $curl;
    }


}