<?php namespace Models;

class WrittenFunction {

    public $functionName;
    public $functionParameters;
    public $functionBody;


    public function __construct($functionName, $functionParameters, $functionBody)
    {
        $this->functionName = $functionName;
        $this->functionParameters = $functionParameters;
        $this->functionBody = $functionBody;
    }


}