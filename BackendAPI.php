<?php

class BackendAPI {

    private $baseUrl = "https://web.njit.edu/~mad63/cs490";
    private $networkSession;

    public function __construct() {
        $this->networkSession = new NetworkSession();
    }

    public function login($requestBody) {
        $url = $this->baseUrl . "/verifyUser.php";
        return $this->networkSession->startRequest($url, $requestBody);
    }
}