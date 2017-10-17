<?php

class BackendAPI {

    private $baseUrl = "https://web.njit.edu/~mad63/cs490";
    private $networkSession;

    public function __construct() {
        $this->networkSession = new NetworkSession();
    }

    public function forwardTo($path) {
        $url = $this->baseUrl + $path;
        return $this->networkSession->startRequest($url,file_get_contents('php://input'));
    }
}