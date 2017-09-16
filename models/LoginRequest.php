<?php namespace models;

use util\TypedProperty;

class LoginRequest extends Decodable {

    public $ucid;
    public $password;

    public function __construct() {
        $this->ucid = new TypedProperty("ucid","string");
        $this->password = new TypedProperty("password","string");
    }
}