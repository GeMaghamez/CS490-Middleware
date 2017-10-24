<?php namespace Models;

class QuestionInfo extends Decodable {

    public $questId;
    public $functionName;
    public $parameters;
    public $input;
    public $output;
    public $codeChecks;

    public  function __construct($JSONObject){
        $this->questId = $this->validateTypeRequired("string", "questID", $JSONObject);
        $this->functionName = $this->validateTypeRequired("string", "functionName", $JSONObject);
        $parameters = $this->validateTypeRequired("array", "parameters", $JSONObject);
        foreach ($parameters as $parameter) {
            if(!is_null($this->validateType("string", $parameter))) {
                $this->parameters[] = $parameter;
            }
        }

        $this->input = $this->validateTypeOptional("string", "input", $JSONObject);
        $this->output = $this->validateTypeOptional("string", "output", $JSONObject);
        $codeChecks = $this->validateTypeRequired("array", "codeChecks", $JSONObject);

        foreach ($codeChecks as $codeCheck) {
            $this->codeChecks[] = new CodeCheck($codeCheck);
        }
    }
}