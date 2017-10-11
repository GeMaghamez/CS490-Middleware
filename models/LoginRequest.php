<?php namespace models;

use util\TypedProperty;

class LoginRequest extends Codable {

    public $username;
    public $password;
    public $uuid;

    public function __construct() {
        $this->username = new TypedProperty("user","string");
        $this->password = new TypedProperty("pass","string");
        $this->uuid = new TypedProperty("uuid","string");
    }
}