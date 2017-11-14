<?php namespace Models;

class CodeCheck extends Decodable{
    public $name;
    public $codeCheckMaxScore;

    public function __construct($JSONObject) {
        $this->name = $this->validateTypeRequired("string", "codeCheckName", $JSONObject);
        $this->codeCheckMaxScore = $this->validateTypeRequired("string", "codeCheckMaxScore", $JSONObject);
    }
}