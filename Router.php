<?php

use Network\BackendAPI;

class Router {

    private $routes = array();
    private $backendAPI;

    public function __construct() {
        $this->backendAPI = new BackendAPI();
    }

    public function add($name, $function) {
        $this->routes[$name] = $function;
    }

    public function call($name) {
        if (array_key_exists($name, $this->routes)) {
            return $this->routes[$name]($this->backendAPI);
        } else {
            throw new InvalidArgumentException("Route /" . $name . " does not exist");
        }
    }
}