<?php namespace Network;

class CurlSession {

    private $session;

    public function __construct() {
        $this->session = curl_init();
    }

    public function startRequest($url, $body, $method = "POST") {
        curl_setopt_array($this->session, array(
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ));

        $response = curl_exec($this->session);
        $err = curl_errno($this->session);

        if ($err) {
            throw new \Exception(curl_strerror($err));
        }

        return $response;
    }

    public function __destruct()
    {
        curl_close($this->session);
    }
}