<?php namespace Models;


class TestCase extends Decodable {
    public $testCaseID;
    public $input;
    public $output;

    public  function __construct($JSONObject){
        $this->testCaseID = $this->validateTypeRequired("string", "testCaseID", $JSONObject);
        $this->input = $this->validateTypeRequired("string", "input", $JSONObject);
        $this->output = $this->validateTypeRequired("string", "output", $JSONObject);
    }
}