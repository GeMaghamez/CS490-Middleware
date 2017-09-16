<?php namespace models;

use util\TypedProperty;

class NjitLoginResponse extends Decodable {

    public $message;

    public function __construct() {
        $this->message = new TypedProperty("title","string");
    }
}