<?php

class Context {
		
	public $app_name;
	
	private static $shared_instance = NULL;

	private function __clone() {}
	private function __wakeup() {}
	private function __construct() {
		$this->app_name = basename(dirname(__FILE__));
	}
	
	public function getRoute() {
		return preg_split("/.\/" . $this->app_name . "/", 
		$_SERVER['REQUEST_URI'])[1];
	}

	public function getContentType() {
	    return $_SERVER['CONTENT_TYPE'];
    }

	public static function getInstance() {
		if (self::$shared_instance == NULL) {
			self::$shared_instance = new Context();
		}
		return self::$shared_instance;
	}
}
