<?php

use models\NjitLoginResponse;
use models\LoginResponse;

class Context {

	public $app_name;
	private $session;

	private static $shared_instance = NULL;

	private function __clone() {}
	private function __wakeup() {}
	private function __construct() {
		$this->app_name = basename(dirname(__FILE__));
		$this->session = new RestService();
	}

	public static function getInstance() {
		if (self::$shared_instance == NULL) {
			self::$shared_instance = new Context();
		}
		return self::$shared_instance;
	}

    public function getRoute() {
        return preg_split("/.\/" . $this->app_name . "/",
            $_SERVER['REQUEST_URI'])[1];
    }

    public function getContentType() {
        return $_SERVER['CONTENT_TYPE'];
    }

    public function getBaseUrl() {
	    return $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'];
    }

	public function loginToNjit($request) {
	    $url = "https://cp4.njit.edu/cp/home/login";
	    $response = $this->session->startRequest("POST", $url, $request->toFormKVPairs());
	    return NjitLoginResponse::fromXML($response);
    }

    public function login($request) {
	    $url = $this->getBaseUrl() + "/~mad63/cs490/userExchange.php";
	    $response = $this->session->startRequest("POST", $url, $request->toJSON());
	    return LoginResponse::fromJSON($response);
    }
}
