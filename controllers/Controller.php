<?php namespace controllers;

class Controller {

	protected $context;
	
	public function __construct() {
		$this->context = \Context::getInstance();
	}
	
	public function handleRequest() {
		$request_method = $_SERVER['REQUEST_METHOD'];
		if (method_exists($this, $request_method)) {
			$this->$request_method();
		} else {
			$route = $this->context->getRoute();
			echo <<<EOT
			<html lang="en">
			<head>
				<meta charset="utf-8">
				<title>Error</title>
			</head>
			<body>
				<pre>Cannot {$_SERVER['REQUEST_METHOD']} {$route}</pre>
			</body>
			</html>
EOT;
		}
	}
}
