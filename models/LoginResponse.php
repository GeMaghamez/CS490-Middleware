<?php namespace models;

use util\TypedProperty;

class LoginResponse extends Codable {

    public $message;

    public function __construct() {
        $this->message = new TypedProperty("message","string");
    }
}