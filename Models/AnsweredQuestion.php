<?php namespace Models;

class AnsweredQuestion extends Decodable {

    public $questionId;
    public $answer;

    public  function __construct($JSONObject) {
        $this->questionId = $this->validateTypeRequired("string", "questID", $JSONObject);
        $this->answer = $this->validateTypeRequired("string", "answer", $JSONObject);
    }

}