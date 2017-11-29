<?php namespace Models;

class QuestionInfo extends Decodable {

    public $questId;
    public $functionName;
    public $maxScore;
    public $testCaseMaxScore;
    public $codeCheckMaxScore;
    public $parameters;
    public $testCases;
    public $codeChecks;

    public  function __construct($JSONObject){
        $this->questId = $this->validateTypeRequired("string", "questID", $JSONObject);
        $this->functionName = $this->validateTypeRequired("string", "functionName", $JSONObject);
        $this->maxScore = $this->validateTypeRequired("string", "maxScore", $JSONObject);
        $this->testCaseMaxScore = $this->validateTypeRequired("string", "testCaseMaxScore", $JSONObject);
        $parameters = $this->validateTypeRequired("array", "parameters", $JSONObject);
        foreach ($parameters as $parameter) {
            if(!is_null($this->validateType("string", $parameter))) {
                $this->parameters[] = $parameter;
            }
        }

        $testCases = $this->validateTypeRequired("array", "testCases", $JSONObject);
        foreach ($testCases as $testCase) {
            $this->testCases[] = new TestCase($testCase);
        }

        $this->codeCheckMaxScore = 0;
        $codeChecks = $this->validateTypeRequired("array", "codeChecks", $JSONObject);
        foreach ($codeChecks as $codeCheck) {
            $this->codeChecks[] = new CodeCheck($codeCheck);
            $this->codeCheckMaxScore += end($this->codeChecks)->codeCheckMaxScore;
        }
    }
}