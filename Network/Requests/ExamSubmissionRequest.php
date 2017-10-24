<?php namespace Network\Requests;

use Models\AnsweredQuestion;
use Models\Decodable;

class ExamSubmissionRequest extends Decodable {
    public $authToken;
    public $userId;
    public $examId;
    public $answeredQuestions;

    public  function __construct($JSONObject) {
        $this->authToken = $this->validateTypeRequired("string", "authToken", $JSONObject);
        $this->userId = $this->validateTypeRequired("integer", "userID", $JSONObject);
        $this->examId = $this->validateTypeRequired("integer", "examID", $JSONObject);
        $answeredQuestions = $this->validateTypeRequired("array", "answeredQuestions", $JSONObject);

        foreach ($answeredQuestions as $question) {
            $this->answeredQuestions[] = new AnsweredQuestion($question);
        }
    }
}