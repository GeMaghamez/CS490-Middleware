<?php namespace controllers;

use models\LoginRequest;

class LoginController extends Controller {

    public function POST() {
	    try {
	        if ($this->context->getContentType() != "application/json") {
	            throw new \Exception("Invalid content type, expected application/json");
            }
            $requestBody = json_decode(file_get_contents('php://input'), true);
	        $requestBody['uuid'] = "0xACA021";
            $request = LoginRequest::fromAssociativeArray($requestBody);
            $njitloginResponse = $this->context->loginToNjit($request);
            $loginResponse = $this->context->login($request);

            $response = array(
                "njitLogin" => $njitloginResponse->message->getValue(),
                "authorized" => $loginResponse->message->getValue()
            );

            echo json_encode($response);
        } catch (\Exception $exception) {
	        echo json_encode(["message" => $exception->getMessage()]);
        }

	}
}
