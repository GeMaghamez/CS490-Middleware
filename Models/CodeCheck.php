<?php namespace Models;

class CodeCheck extends Decodable{
    public $name;
    public $maxScore;

    public function __construct($JSONObject) {
        $this->name = $this->validateTypeRequired("string", "codeCheckName", $JSONObject);
        $this->maxScore = $this->validateTypeRequired("string", "maxScore", $JSONObject);
    }
}