<?php

require_once "autoloader.php";

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
    $content = file_get_contents('php://input');
    $code = urldecode(json_decode($content)->{'code'});
    $grader = new AutoGrader();
    $grader->executeCode($code);
    if (empty($grader->lastStderr)) {
        echo $grader->lastStdout;
    } else {
        echo $grader->lastStderr;
    }
});

try {
    echo $router->call(getRoute());
} catch (Exception $e) {
    echo $e->getMessage();
}
