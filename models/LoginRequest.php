<?php namespace models;

use util\TypedProperty;

class LoginRequest extends Codable {

    public $username;
    public $password;

    public function __construct() {
        $this->username = new TypedProperty("username","string");
        $this->password = new TypedProperty("password","string");
    }
}