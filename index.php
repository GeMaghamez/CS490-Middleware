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
    echo $backendAPI->login(file_get_contents('php://input'));
});

$router->add("get_tests", function () {
    print_r("get_tests reached");
});

$router->add("create_tests", function () {
    print_r("create_tests reached");
});

$router->add("delete_test", function () {
    print_r("delete_test reached");
});

$router->add("get_question", function () {
    print_r("get_question reached");
});

$router->add("create_question", function () {
    print_r("create_question reached");
});

$router->add("update_question", function () {
    print_r("update_question reached");
});

$router->add("submit_test", function () {
    print_r("submit_test reached");
});

try {
    $router->call(getRoute());
} catch (Exception $e) {
    echo $e;
}
