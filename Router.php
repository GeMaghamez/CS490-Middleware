<?php

class Router {

    private $router = array();
    private $backendAPI;

    public function __construct() {
        $this->backendAPI = new BackendAPI();
    }

    public function add($name, $function) {
        $this->routes[$name] = $function;
    }

    public function call($name) {
        if (array_key_exists($name, $this->routes)) {
            $this->routes[$name]($this->backendAPI);
        } else {
            echo "page not found";
        }
    }
}