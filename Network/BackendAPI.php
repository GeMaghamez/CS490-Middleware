<?php namespace Network;

class BackendAPI {

    private $baseUrl = "https://web.njit.edu/~mad63/cs490";
    private $networkSession;

    public function __construct() {
        $this->networkSession = new CurlSession();
    }

    public function forwardTo($path) {
        $url = $this->baseUrl . $path;
        return $this->networkSession->startRequest($url, file_get_contents('php://input'));
    }

    public function login($request) {
        $url = $this->baseUrl . "/verifyUser.php";
        return $this->networkSession->startRequest($url, $request);
    }

    public function getQuestionInfo($request) {
        $url = $this->baseUrl . "/getQuestionInfo.php";
        return $this->networkSession->startRequest($url, $request);
    }

    public function submitGradedQuestions($request) {
        $url = $this->baseUrl . "/submitResponse.php";
        return $this->networkSession->startRequest($url, $request);
    }
}