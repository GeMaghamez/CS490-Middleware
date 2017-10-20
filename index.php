<?php

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
    $content = file_get_contents('php://input');
    $code = urldecode(json_decode($content)->{'code'});
    $runner = new PyRunner();
    echo $runner->exec_python($code, $outputBuffers) . PHP_EOL;
    print_r($outputBuffers);
});

$router->add("test", function (BackendAPI $backendAPI) {

});

try {
    echo $router->call(getRoute());
} catch (Exception $e) {
    echo $e->getMessage();
}
