<?php

use Network\BackendAPI;
use Network\Requests\ExamSubmissionRequest;
use Models\QuestionInfo;

require_once "autoloader.php";
ignore_user_abort(true);
set_time_limit(0);

if ($_SERVER['REQUEST_METHOD'] != "POST" || $_SERVER['CONTENT_TYPE'] != "application/json") {
    echo 'Incorrect HTTP method or content type';
    exit();
}

function getRoute() {
    $appName = basename(dirname(__FILE__));
    $route = explode($appName, $_SERVER['REQUEST_URI']);
    $cleanRoute = strtolower(trim($route[1], "/"));
    return $cleanRoute;
}

function sendResponse($response) {
    ob_start();
    echo $response;
    header('Connection: close');
    header('Content-Length: '.ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();
}

$router = new Router();

$router->add("login", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/verifyUser.php");
});

$router->add("get_exams", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/getExams.php");
});

$router->add("create_exam", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/createExam.php");
});

$router->add("delete_exam", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/deleteExam.php");
});

$router->add("get_question", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/getQuestion.php");
});

$router->add("create_question", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/createQuestion.php");
});

$router->add("update_question", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/editQuestion.php");
});

$router->add("submit_test", function (BackendAPI $backendAPI) {
    $autoGrader = new AutoGrader();
    $examSubmissionRequest = new ExamSubmissionRequest(json_decode(file_get_contents("php://input")));
    $getQuestionInfoRequest = [
        "authToken" => $examSubmissionRequest->authToken,
        "userID" => $examSubmissionRequest->userId,
        "examID" => $examSubmissionRequest->examId,
        "questions" => []
    ];

    $response = [
        "authToken" => $examSubmissionRequest->authToken,
        "userID" => $examSubmissionRequest->userId,
        "examID" => $examSubmissionRequest->examId,
        "answeredQuestions" => []
    ];

    foreach ($examSubmissionRequest->answeredQuestions as $question) {
        $getQuestionInfoRequest["questions"][0] = $question->questionId;
        $request = json_encode($getQuestionInfoRequest);
        $questionInfo = new QuestionInfo(json_decode($backendAPI->getQuestionInfo($request))[0]);
        $gradedQuestion = $autoGrader->grade($questionInfo, $question->answer);
        $response['answeredQuestions'][] = $gradedQuestion;
    }

    return $backendAPI->submitGradedQuestions(json_encode($response));
});

$router->add("get_graded_exam", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/getGradedExams.php");
});

$router->add("get_exam_response", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/getExamResponse.php");
});

$router->add("release_exam", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/releaseExam.php");
});

$router->add("edit_response", function (BackendAPI $backendAPI) {
    return $backendAPI->forwardTo("/responseEdit.php");
});

try {
    echo $router->call(getRoute());
} catch (Exception $e) {
    echo $e->getMessage();
}
