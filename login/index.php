<?php namespace login;

require_once "../autoloader.php";
use \controllers\LoginController;

$controller = new LoginController;
$controller->handleRequest();
